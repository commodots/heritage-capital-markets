<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketIndex;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MarketIndexController extends Controller
{
    public function index()
    {
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
        $marketIndex->update($this->manualAttributes($request));

        return back()->with('success', 'Market index updated successfully.');
    }

    public function importCsv(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($validated['csv_file']->getRealPath(), 'r');
        if ($handle === false) {
            throw ValidationException::withMessages([
                'csv_file' => 'The uploaded CSV file could not be read.',
            ]);
        }

        $headers = fgetcsv($handle);
        if (! is_array($headers) || $headers === []) {
            fclose($handle);
            throw ValidationException::withMessages([
                'csv_file' => 'The CSV file must include a header row.',
            ]);
        }

        $headers = array_map(
            fn ($header) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $header))),
            $headers,
        );
        if (count($headers) !== count(array_unique($headers))) {
            fclose($handle);
            throw ValidationException::withMessages([
                'csv_file' => 'The CSV header contains duplicate columns.',
            ]);
        }

        $changeHeader = in_array('change', $headers, true)
            ? 'change'
            : (in_array('change_value', $headers, true) ? 'change_value' : null);
        $requiredHeaders = ['name', 'value', 'change_percent'];
        if ($changeHeader === null) {
            $requiredHeaders[] = 'change or change_value';
        }
        $missingHeaders = array_values(array_diff($requiredHeaders, $headers));
        if ($missingHeaders !== []) {
            fclose($handle);
            throw ValidationException::withMessages([
                'csv_file' => 'Missing CSV columns: '.implode(', ', $missingHeaders).'.',
            ]);
        }

        $rows = [];
        $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++;
            if ($values === [null] || (count($values) === 1 && trim((string) $values[0]) === '')) {
                continue;
            }
            if (count($values) !== count($headers)) {
                fclose($handle);
                throw ValidationException::withMessages([
                    'csv_file' => "CSV row {$line} has a different number of values than the header.",
                ]);
            }

            $row = array_combine($headers, $values);
            $row['change'] = $row[$changeHeader] ?? null;
            $row['sort_order'] = filled($row['sort_order'] ?? null) ? $row['sort_order'] : count($rows) + 1;
            $rows[] = $this->validateImportRow($row, "CSV row {$line}");
            if (count($rows) > 500) {
                fclose($handle);
                throw ValidationException::withMessages([
                    'csv_file' => 'A single CSV import is limited to 500 indices.',
                ]);
            }
        }
        fclose($handle);

        if ($rows === []) {
            throw ValidationException::withMessages([
                'csv_file' => 'The CSV file does not contain any index rows.',
            ]);
        }

        $this->importRows($rows);

        return back()->with('success', count($rows).' market indices imported from CSV.');
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
            $request = Http::acceptJson()->timeout(15);
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

    private function manualAttributes(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:100'],
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
                $query = MarketIndex::query();
                if (filled($row['symbol'] ?? null)) {
                    $query->where('symbol', $row['symbol']);
                } else {
                    $query->where('name', $row['name']);
                }

                $index = $query->first() ?? new MarketIndex();
                $isPositive = isset($row['is_positive'])
                    ? (bool) $row['is_positive']
                    : (float) $row['change'] >= 0;
                $change = abs((float) $row['change']) * ($isPositive ? 1 : -1);
                $index->fill([
                    'name' => $row['name'],
                    'symbol' => $row['symbol'] ?? null,
                    'value' => $row['value'],
                    'change_value' => $change,
                    'change_percent' => abs((float) $row['change_percent']) * ($isPositive ? 1 : -1),
                    'as_of' => $row['as_of'] ?? now(),
                    'is_active' => isset($row['is_active']) ? (bool) $row['is_active'] : ($index->exists ? $index->is_active : true),
                    'sort_order' => $row['sort_order'] ?? ($index->exists ? $index->sort_order : MarketIndex::max('sort_order') + 1),
                ])->save();
            }
        });
    }
}
