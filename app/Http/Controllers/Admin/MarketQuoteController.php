<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketQuote;
use App\Support\MarketCsvReader;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MarketQuoteController extends Controller
{
    public function index(Request $request)
    {
        $preview = $request->session()->get('marketQuoteCsvPreview');

        return Inertia::render('Admin/MarketQuotes/Index', [
            'quotes' => MarketQuote::query()
                ->orderBy('sort_order')
                ->orderBy('symbol')
                ->get(),
            'csvPreview' => $preview
                ? ['token' => $preview['token'], 'rows' => $preview['rows']]
                : null,
            'csvReport' => $request->session()->get('marketQuoteCsvReport'),
        ]);
    }

    public function store(Request $request)
    {
        MarketQuote::create($this->manualAttributes($request));

        return back()->with('success', 'Market quote created successfully.');
    }

    public function update(Request $request, MarketQuote $marketQuote)
    {
        $marketQuote->update($this->manualAttributes($request, $marketQuote));

        return back()->with('success', 'Market quote updated successfully.');
    }

    public function previewCsv(Request $request, MarketCsvReader $reader)
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);
        $csv = $reader->read(
            $validated['csv_file'],
            ['symbol', 'name', 'value', 'change', 'change_percent'],
            ['as_of', 'is_active', 'sort_order'],
        );
        $rows = $csv['rows'];

        $symbolRows = [];
        foreach ($rows as $index => $row) {
            $symbol = strtoupper(trim((string) ($row['data']['symbol'] ?? '')));
            if ($symbol !== '') {
                $symbolRows[$symbol][] = $index;
            }
        }

        foreach ($rows as $index => &$row) {
            if ($row['errors'] !== []) {
                $row['valid'] = false;
                $row['action'] = 'Invalid row';
                continue;
            }

            $data = $this->trimRow($row['data']);
            $data['symbol'] = strtoupper($data['symbol']);
            $validator = Validator::make($data, [
                'symbol' => ['required', 'string', 'max:100'],
                'name' => ['required', 'string', 'max:255'],
                'value' => ['required', 'numeric', 'min:0', 'max:99999999999.9999'],
                'change' => ['required', 'numeric', 'between:-99999999999.9999,99999999999.9999'],
                'change_percent' => ['required', 'numeric', 'between:-9999.9999,9999.9999'],
                'as_of' => ['nullable', 'date'],
                'is_active' => ['nullable', 'boolean'],
                'sort_order' => ['nullable', 'integer', 'min:1'],
            ]);

            if ($validator->fails()) {
                $row['errors'] = $validator->errors()->all();
            } else {
                $data = $validator->validated();
                if (count($symbolRows[$data['symbol']] ?? []) > 1) {
                    $row['errors'][] = 'This symbol appears more than once in the CSV.';
                }
                if (! MarketQuote::query()->where('symbol', $data['symbol'])->exists()) {
                    $row['errors'][] = 'Unknown symbol. Create the stock symbol manually before importing its quote.';
                }
                $row['data'] = $data;
            }

            $row['valid'] = $row['errors'] === [];
            $row['action'] = $row['valid'] ? 'Update existing quote' : 'Invalid row';
        }
        unset($row);

        $request->session()->put('marketQuoteCsvPreview', [
            'token' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'rows' => $rows,
        ]);
        $request->session()->forget('marketQuoteCsvReport');

        return back()->with('success', 'CSV preview ready. Review the rows before committing valid updates.');
    }

    public function commitCsv(Request $request)
    {
        $validated = $request->validate([
            'preview_token' => ['required', 'string'],
        ]);
        $preview = $request->session()->get('marketQuoteCsvPreview');

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

            $data = $row['data'];
            try {
                DB::transaction(function () use ($data): void {
                    $quote = MarketQuote::query()->where('symbol', $data['symbol'])->first();
                    if (! $quote) {
                        throw new \RuntimeException('The stock symbol no longer exists.');
                    }

                    $quote->fill([
                        'name' => $data['name'],
                        'value' => $data['value'],
                        'change_value' => $data['change'],
                        'change_percent' => $data['change_percent'],
                        'as_of' => filled($data['as_of'] ?? null) ? $data['as_of'] : now(),
                        'is_active' => filled($data['is_active'] ?? null)
                            ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN)
                            : $quote->is_active,
                        'sort_order' => $data['sort_order'] ?? $quote->sort_order,
                    ])->save();
                });
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $data['symbol'],
                    'status' => 'success',
                    'message' => 'Quote updated.',
                ];
            } catch (QueryException $exception) {
                Log::error('Market quote CSV row could not be saved.', [
                    'line' => $row['line'],
                    'symbol' => $data['symbol'],
                    'exception' => $exception->getMessage(),
                ]);
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $data['symbol'],
                    'status' => 'failed',
                    'message' => 'Database update failed; the quote was not changed.',
                ];
            } catch (\RuntimeException $exception) {
                $reportRows[] = [
                    'line' => $row['line'],
                    'symbol' => $data['symbol'],
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
        $request->session()->forget('marketQuoteCsvPreview');
        $request->session()->flash('marketQuoteCsvReport', $report);

        return back()->with(
            'success',
            "CSV import complete: {$report['succeeded']} updated, {$report['failed']} failed, {$report['invalid']} invalid."
        );
    }

    public function cancelCsv(Request $request)
    {
        $request->session()->forget('marketQuoteCsvPreview');

        return back()->with('success', 'CSV preview discarded.');
    }

    public function toggle(MarketQuote $marketQuote)
    {
        $marketQuote->update(['is_active' => ! $marketQuote->is_active]);

        return back()->with(
            'success',
            $marketQuote->is_active ? 'Market quote published.' : 'Market quote unpublished.'
        );
    }

    public function destroy(MarketQuote $marketQuote)
    {
        $marketQuote->delete();

        return back()->with('success', 'Market quote deleted successfully.');
    }

    private function manualAttributes(Request $request, ?MarketQuote $marketQuote = null): array
    {
        $request->merge([
            'symbol' => strtoupper(trim((string) $request->input('symbol'))),
        ]);

        $data = $request->validate([
            'symbol' => [
                'required',
                'string',
                'max:100',
                Rule::unique('market_quotes', 'symbol')->ignore($marketQuote?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0', 'max:99999999999.9999'],
            'change' => ['required', 'numeric', 'between:-99999999999.9999,99999999999.9999'],
            'change_percent' => ['required', 'numeric', 'between:-9999.9999,9999.9999'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        return [
            'symbol' => $data['symbol'],
            'name' => $data['name'],
            'value' => $data['value'],
            'change_value' => $data['change'],
            'change_percent' => $data['change_percent'],
            'as_of' => now(),
            'is_active' => $data['is_active'],
            'sort_order' => $data['sort_order'],
        ];
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
