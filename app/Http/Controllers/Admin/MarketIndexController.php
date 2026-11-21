<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketIndex;
use App\Support\MarketCsvReader;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MarketIndexController extends Controller
{
    public function index(Request $request)
    {
        $preview = $request->session()->get('marketIndexCsvPreview');

        return Inertia::render('Admin/MarketIndices/Index', [
            'indices' => MarketIndex::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'apiConfigured' => filled(config('services.market_indices.url')),
        ]);
    }

    public function store(Request $request)
    {
        MarketIndex::create($this->manualAttributes($request));

        return back()->with('success', 'Market index created successfully.');
    }

    public function update(Request $request, MarketIndex $marketIndex)
    {
        $marketIndex->update($this->manualAttributes($request, $marketIndex));

        return back()->with('success', 'Market index updated successfully.');
    }

    public function previewCsv(Request $request, MarketCsvReader $reader)
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);
        $csv = $reader->read(
            $validated['csv_file'],
            ['name', 'value', 'change_percent'],
            ['symbol', 'change', 'change_value', 'is_positive', 'is_active', 'sort_order', 'as_of'],
        );
        if (! in_array('change', $csv['headers'], true) && ! in_array('change_value', $csv['headers'], true)) {
            throw ValidationException::withMessages([
                'csv_file' => 'The CSV file must include a change or change_value column.',
            ]);
        }

        $rows = $csv['rows'];
        $identities = [];
        foreach ($rows as $index => &$row) {
            if ($row['errors'] !== []) {
                $row['valid'] = false;
                $row['action'] = 'Invalid row';
                continue;
            }

            $data = $this->trimRow($row['data']);
            $data['symbol'] = filled($data['symbol'] ?? null) ? strtoupper($data['symbol']) : null;
            $data['change'] = $data['change'] ?? $data['change_value'] ?? null;
            $data['sort_order'] = filled($data['sort_order'] ?? null) ? $data['sort_order'] : $index + 1;
            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'symbol' => ['nullable', 'string', 'max:100'],
                'value' => ['required', 'numeric', 'between:-99999999999.9999,99999999999.9999'],
                'change' => ['required', 'numeric', 'between:-99999999999.9999,99999999999.9999'],
                'change_percent' => ['required', 'numeric', 'between:-9999.9999,9999.9999'],
                'is_positive' => ['nullable', 'boolean'],
                'is_active' => ['nullable', 'boolean'],
                'sort_order' => ['nullable', 'integer', 'min:1'],
                'as_of' => ['nullable', 'date'],
            ]);

            if ($validator->fails()) {
                $row['errors'] = $validator->errors()->all();
            } else {
                $data = $validator->validated();
                $matches = $this->matchingIndices($data);
                if ($matches->count() > 1) {
                    $row['errors'][] = 'More than one existing index matches this symbol or name.';
                }
                $row['data'] = $data;
                $row['action'] = $matches->isNotEmpty() ? 'Update existing index' : 'Create index';
                foreach (array_filter([
                    filled($data['symbol'] ?? null) ? 'symbol:'.strtoupper($data['symbol']) : null,
                    'name:'.strtolower(trim($data['name'])),
                ]) as $identity) {
                    $identities[$identity][] = $index;
                }
            }

            $row['valid'] = $row['errors'] === [];
            $row['action'] = $row['valid'] ? ($row['action'] ?? 'Create index') : 'Invalid row';
        }
        unset($row);

        foreach ($identities as $indexes) {
            if (count($indexes) < 2) {
                continue;
            }
            foreach ($indexes as $index) {
                $rows[$index]['valid'] = false;
                $rows[$index]['action'] = 'Invalid row';
                $rows[$index]['errors'][] = 'This symbol or name appears more than once in the CSV.';
            }
        }

        $request->session()->put('marketIndexCsvPreview', [
            'token' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'rows' => $rows,
        ]);
        $request->session()->forget('marketIndexCsvReport');

        return back()->with('success', 'CSV preview ready. Review the rows before committing valid updates.');
    }

    public function commitCsv(Request $request)
    {
        $validated = $request->validate([
            'preview_token' => ['required', 'string'],
        ]);
        $preview = $request->session()->get('marketIndexCsvPreview');
        if (
            ! is_array($preview)
            || ($preview['user_id'] ?? null) !== $request->user()->id
            || ! hash_equals((string) ($preview['token'] ?? ''), $validated['preview_token'])
        ) {
            throw ValidationException::withMessages([
                'csv_file' => 'This CSV preview has expired. Upload the file again.',
            ]);
        }

        $reportRows = [];
        foreach ($preview['rows'] as $row) {
            if (! ($row['valid'] ?? false)) {
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $row['data']['symbol'] ?? '',
                    'status' => 'invalid',
                    'message' => implode(' ', $row['errors']),
                ];
                continue;
            }

            try {
                DB::transaction(function () use ($row): void {
                    $matches = $this->matchingIndices($row['data']);
                    if ($matches->count() > 1 || ($row['action'] === 'Update existing index' && $matches->isEmpty())) {
                        throw new \RuntimeException('The matched index changed after preview; this row was not applied.');
                    }

                    $index = $matches->first() ?? new MarketIndex();
                    $this->fillImportedIndex($index, $row['data'])->save();
                });
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $row['data']['symbol'] ?? '',
                    'status' => 'success',
                    'message' => 'Index update committed.',
                ];
            } catch (QueryException $exception) {
                Log::error('Market index CSV row could not be saved.', [
                    'line' => $row['line'],
                    'symbol' => $row['data']['symbol'] ?? null,
                    'exception' => $exception->getMessage(),
                ]);
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $row['data']['symbol'] ?? '',
                    'status' => 'failed',
                    'message' => 'Database update failed; the index was not changed.',
                ];
            } catch (\RuntimeException $exception) {
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $row['data']['symbol'] ?? '',
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ];
            }
        }

        $report = [
            'succeeded' => count(array_filter($reportRows, fn ($row) => $row['status'] === 'success')),
            'failed' => count(array_filter($reportRows, fn ($row) => $row['status'] === 'failed')),
            'invalid' => count(array_filter($reportRows, fn ($row) => $row['status'] === 'invalid')),
            'rows' => $reportRows,
        ];
        $request->session()->forget('marketIndexCsvPreview');
        $request->session()->flash('marketIndexCsvReport', $report);

        return back()->with(
            'success',
            "CSV import complete: {$report['succeeded']} applied, {$report['failed']} failed, {$report['invalid']} invalid."
        );
    }

    public function cancelCsv(Request $request)
    {
        $request->session()->forget('marketIndexCsvPreview');

        return back()->with('success', 'CSV preview discarded.');
    }

    public function importApi()
    {
        $url = config('services.market_indices.url');
        if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw ValidationException::withMessages([
                'api' => 'Configure a valid HTTPS MARKET_INDICES_API_URL before importing from an API.',
            ]);
        }

        try {
            $request = Http::acceptJson()->timeout(15)->withoutRedirecting();
            $token = config('services.market_indices.token');
            if (filled($token)) {
                $request = $request->withToken($token);
            }
            $response = $request->get($url);
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'api' => 'The configured market data API could not be reached.',
            ]);
        }

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'api' => 'The configured market data API returned HTTP '.$response->status().'.',
            ]);
        }

        $payload = $response->json();
        $records = $payload['data'] ?? $payload['indices'] ?? $payload;
        if (! is_array($records) || ! array_is_list($records) || $records === []) {
            throw ValidationException::withMessages([
                'api' => 'The market data API must return a non-empty JSON list or a list under data or indices.',
            ]);
        }
        if (count($records) > 500) {
            throw ValidationException::withMessages([
                'api' => 'A single API import is limited to 500 indices.',
            ]);
        }

        $rows = [];
        foreach ($records as $index => $record) {
            if (! is_array($record)) {
                throw ValidationException::withMessages([
                    'api' => 'API record '.($index + 1).' must be an object.',
                ]);
            }

            $record['change'] = $record['change'] ?? $record['change_value'] ?? null;
            $record['sort_order'] = $record['sort_order'] ?? $index + 1;
            $rows[] = $this->validateImportRow($record, 'API record '.($index + 1), 'api');
        }

        $this->importRows($rows);

        return back()->with('success', count($rows).' market indices imported from the configured API.');
    }

    private function apiUrl(): ?string
    {
        $url = config('services.market_indices.url');

        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL)
            && parse_url($url, PHP_URL_SCHEME) === 'https'
                ? $url
                : null;
    }

    public function destroy(MarketIndex $marketIndex)
    {
        $marketIndex->delete();

        return back()->with('success', 'Market index deleted successfully.');
    }

    public function toggle(MarketIndex $marketIndex)
    {
        $marketIndex->update([
            'is_active' => ! $marketIndex->is_active,
        ]);

        return back()->with(
            'success',
            $marketIndex->is_active ? 'Market index activated.' : 'Market index deactivated.'
        );
    }

    private function manualAttributes(Request $request, ?MarketIndex $marketIndex = null): array
    {
        $request->merge([
            'symbol' => filled($request->input('symbol'))
                ? strtoupper(trim((string) $request->input('symbol')))
                : null,
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('market_indices', 'name')->ignore($marketIndex?->id)],
            'symbol' => ['nullable', 'string', 'max:100', Rule::unique('market_indices', 'symbol')->ignore($marketIndex?->id)],
            'value' => ['required', 'numeric'],
            'change' => ['required', 'numeric'],
            'change_percent' => ['required', 'numeric'],
            'is_positive' => ['required', 'boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        return [
            'name' => $data['name'],
            'symbol' => $data['symbol'] ?? null,
            'value' => $data['value'],
            'change_value' => ($request->boolean('is_positive') ? 1 : -1) * abs((float) $data['change']),
            'change_percent' => ($request->boolean('is_positive') ? 1 : -1) * abs((float) $data['change_percent']),
            'as_of' => now(),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'],
        ];
    }

    private function validateImportRow(array $row, string $label, string $errorField = 'csv_file'): array
    {
        $validator = Validator::make($row, [
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:100'],
            'value' => ['required', 'numeric'],
            'change' => ['required', 'numeric'],
            'change_percent' => ['required', 'numeric'],
            'is_positive' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'as_of' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $errorField => $label.': '.implode(' ', $validator->errors()->all()),
            ]);
        }

        return $validator->validated();
    }

    private function importRows(array $rows): void
    {
        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                $matches = $this->matchingIndices($row);
                if ($matches->count() > 1) {
                    throw ValidationException::withMessages([
                        'api' => 'An API record matches multiple existing market indices.',
                    ]);
                }

                $index = $matches->first() ?? new MarketIndex();
                $this->fillImportedIndex($index, $row)->save();
            }
        });
    }

    private function matchingIndices(array $row)
    {
        return MarketIndex::query()
            ->where(function ($query) use ($row): void {
                if (filled($row['symbol'] ?? null)) {
                    $query->whereRaw('UPPER(symbol) = ?', [strtoupper($row['symbol'])])
                        ->orWhereRaw('LOWER(name) = ?', [strtolower(trim($row['name']))]);
                } else {
                    $query->whereRaw('LOWER(name) = ?', [strtolower(trim($row['name']))]);
                }
            })
            ->get();
    }

    private function fillImportedIndex(MarketIndex $index, array $row): MarketIndex
    {
        $change = (float) $row['change'];
        $changePercent = (float) $row['change_percent'];
        if (filled($row['is_positive'] ?? null)) {
            $direction = filter_var($row['is_positive'], FILTER_VALIDATE_BOOLEAN) ? 1 : -1;
            $change = abs($change) * $direction;
            $changePercent = abs($changePercent) * $direction;
        }

        return $index->fill([
            'name' => $row['name'],
            'symbol' => filled($row['symbol'] ?? null) ? strtoupper($row['symbol']) : null,
            'value' => $row['value'],
            'change_value' => $change,
            'change_percent' => $changePercent,
            'as_of' => filled($row['as_of'] ?? null) ? $row['as_of'] : now(),
            'is_active' => filled($row['is_active'] ?? null)
                ? filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN)
                : ($index->exists ? $index->is_active : true),
            'sort_order' => $row['sort_order'] ?? ($index->exists ? $index->sort_order : MarketIndex::max('sort_order') + 1),
        ]);
    }

    private function trimRow(array $row): array
    {
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = trim($value);
            }
        }

        return $row;
    }
}
