<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class MarketCsvReader
{
    public function read(UploadedFile $file, array $requiredHeaders, array $optionalHeaders = []): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            throw ValidationException::withMessages([
                'csv_file' => 'The uploaded CSV file could not be read.',
            ]);
        }

        try {
            $headers = fgetcsv($handle, null, ',', '"', '');
            if (! is_array($headers) || $headers === []) {
                throw ValidationException::withMessages([
                    'csv_file' => 'The CSV file must include a header row.',
                ]);
            }

            $headers = array_map(
                fn ($header) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $header))),
                $headers,
            );
            if (in_array('', $headers, true)) {
                throw ValidationException::withMessages([
                    'csv_file' => 'The CSV header contains an empty column name.',
                ]);
            }
            if (count($headers) !== count(array_unique($headers))) {
                throw ValidationException::withMessages([
                    'csv_file' => 'The CSV header contains duplicate columns.',
                ]);
            }

            $missingHeaders = array_values(array_diff($requiredHeaders, $headers));
            if ($missingHeaders !== []) {
                throw ValidationException::withMessages([
                    'csv_file' => 'Missing CSV columns: '.implode(', ', $missingHeaders).'.',
                ]);
            }

            $unexpectedHeaders = array_values(array_diff($headers, [...$requiredHeaders, ...$optionalHeaders]));
            if ($unexpectedHeaders !== []) {
                throw ValidationException::withMessages([
                    'csv_file' => 'Unsupported CSV columns: '.implode(', ', $unexpectedHeaders).'.',
                ]);
            }

            $rows = [];
            $line = 1;
            while (($values = fgetcsv($handle, null, ',', '"', '')) !== false) {
                $line++;
                if ($values === [null] || (count($values) === 1 && trim((string) $values[0]) === '')) {
                    continue;
                }

                if (count($values) !== count($headers)) {
                    $rows[] = [
                        'line' => $line,
                        'data' => [],
                        'errors' => ["Expected ".count($headers)." columns; found ".count($values).'.'],
                    ];
                } else {
                    $rows[] = [
                        'line' => $line,
                        'data' => array_combine($headers, $values),
                        'errors' => [],
                    ];
                }

                if (count($rows) > 500) {
                    throw ValidationException::withMessages([
                        'csv_file' => 'A single CSV import is limited to 500 rows.',
                    ]);
                }
            }

            if ($rows === []) {
                throw ValidationException::withMessages([
                    'csv_file' => 'The CSV file does not contain any data rows.',
                ]);
            }

            return [
                'headers' => $headers,
                'rows' => $rows,
            ];
        } finally {
            fclose($handle);
        }
    }
}
