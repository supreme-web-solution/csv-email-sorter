<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\EmailFilterResult;
use Illuminate\Support\Facades\Auth;

class EmailFilterController extends Controller
{
    public function show()
    {
        return view('email-filter');
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'source_file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
            'exclude_file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $mainPath = $validated['source_file']->getRealPath();
        $removePath = $validated['exclude_file']->getRealPath();

        $mainEmails = $this->extractEmails($mainPath, $validated['source_file']->getClientOriginalExtension());
        $removeEmails = $this->extractEmails($removePath, $validated['exclude_file']->getClientOriginalExtension());

        $removeSet = array_flip($removeEmails);
        $result = array_values(array_filter($mainEmails, function ($email) use ($removeSet) {
            return !isset($removeSet[$email]);
        }));

        $mainCount = count($mainEmails);
        $removeCount = count($removeEmails);
        $resultCount = count($result);

        $token = (string) Str::uuid();
        $relativePath = "email-filter/{$token}.csv";

        Storage::put($relativePath, $this->emailsToCsv($result));

        // Store result in the database
        EmailFilterResult::create([
            'user_id' => Auth::id(),
            'filename' => $relativePath,
            'token' => $token,
            'source_count' => $mainCount,
            'exclude_count' => $removeCount,
            'result_count' => $resultCount,
        ]);

        $request->session()->flash('email_filter.preview', [
            'main' => $mainCount,
            'remove' => $removeCount,
            'result' => $resultCount,
            'token' => $token,
        ]);

        return redirect()->route('dashboard');
    }

    public function download(string $token): StreamedResponse
    {
        $relativePath = "email-filter/{$token}.csv";

        if (! Storage::exists($relativePath)) {
            abort(404);
        }

        $filename = 'filtered-emails-' . $token . '.csv';

        return Storage::download($relativePath, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @param  string  $path
     * @param  string|null  $extension
     * @return array<int, string>
     */
    protected function extractEmails(string $path, ?string $extension): array
    {
        $extension = strtolower((string) $extension);

        if ($extension === 'xlsx') {
            // Basic XLSX support via PhpSpreadsheet if available.
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $sheet = $spreadsheet->getActiveSheet();
                $emails = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $value = trim((string) $cell->getValue());
                        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $emails[] = strtolower($value);
                        }
                    }
                }

                return array_values(array_unique($emails));
            }

            // Fallback: treat as text if PhpSpreadsheet is not installed.
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $emails = [];

        foreach ($lines as $line) {
            // Split by comma / semicolon / whitespace and extract emails.
            $parts = preg_split('/[,\s;]+/', $line) ?: [];
            foreach ($parts as $part) {
                $value = trim($part);
                if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = strtolower($value);
                }
            }
        }

        return array_values(array_unique($emails));
    }

    /**
     * @param  array<int, string>  $emails
     */
    protected function emailsToCsv(array $emails): string
    {
        $handle = fopen('php://temp', 'r+');

        // Add header row
        fputcsv($handle, ['Email']);

        foreach ($emails as $email) {
            fputcsv($handle, [$email]);
        }

        rewind($handle);

        $csv = stream_get_contents($handle) ?: '';

        fclose($handle);

        return $csv;
    }
}

