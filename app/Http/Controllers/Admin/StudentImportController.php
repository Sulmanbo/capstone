<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentImportController extends Controller
{
    public function showForm(): View
    {
        return view('admin.students.import');
    }

    public function import(Request $request): View|RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path   = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->withErrors(['csv_file' => 'Could not open the uploaded file.']);
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'The CSV file is empty.']);
        }

        // Normalize header names
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $required = ['first_name', 'last_name', 'email', 'lrn'];
        $missing  = array_diff($required, $header);
        if ($missing) {
            fclose($handle);
            return back()->withErrors([
                'csv_file' => 'Missing required columns: ' . implode(', ', $missing),
            ]);
        }

        $activeYear = AcademicYear::where('status', 'active')->first();

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;

            if (count($data) < count($header)) {
                $errors[] = "Row {$row}: too few columns — skipped.";
                $skipped++;
                continue;
            }

            $fields = array_combine($header, $data);
            $fields = array_map('trim', $fields);

            // ── Per-row validation ─────────────────────────────────────────
            $rowErrors = $this->validateRow($fields, $row);
            if ($rowErrors) {
                $errors = array_merge($errors, $rowErrors);
                $skipped++;
                continue;
            }

            // ── Duplicate checks ───────────────────────────────────────────
            $emailHash = hash('sha256', strtolower($fields['email']));

            if (User::where('lrn', $fields['lrn'])->exists()) {
                $errors[] = "Row {$row}: LRN {$fields['lrn']} already exists — skipped.";
                $skipped++;
                continue;
            }

            if (User::where('email_hash', $emailHash)->exists()) {
                $errors[] = "Row {$row}: Email already registered — skipped.";
                $skipped++;
                continue;
            }

            // ── Create user ────────────────────────────────────────────────
            try {
                DB::transaction(function () use ($fields, $emailHash, $activeYear, &$imported) {
                    $username = $this->uniqueUsername($fields['lrn']);

                    $user = User::create([
                        'first_name'             => $fields['first_name'],
                        'last_name'              => $fields['last_name'],
                        'email'                  => $fields['email'],
                        'username'               => $username,
                        'password'               => $fields['lrn'],  // LRN as temp password
                        'role_id'                => '01',
                        'lrn'                    => $fields['lrn'],
                        'gender'                 => $fields['gender'] ?? null,
                        'grade_level'            => $fields['grade_level'] ?? null,
                        'address'                => $fields['address'] ?? null,
                        'phone'                  => $fields['phone'] ?? null,
                        'status'                 => 'active',
                        'password_reset_required'=> true,
                        'enrollment_date'        => now()->toDateString(),
                    ]);

                    // Optional: auto-enroll in section if provided
                    if (!empty($fields['section_name']) && $activeYear) {
                        $section = Section::where('section_name', $fields['section_name'])->first();
                        if ($section && !Enrollment::existsForStudentAndYear($user->id, $activeYear->id)) {
                            $gradeLevel   = $fields['grade_level'] ?? null;
                            $unmetPrereqs = $gradeLevel
                                ? app(\App\Services\PrerequisiteService::class)
                                    ->getUnmet($user, $gradeLevel, $activeYear->id)
                                : [];

                            if (!empty($unmetPrereqs)) {
                                $unmetNames = collect($unmetPrereqs)
                                    ->pluck('requires')
                                    ->unique()
                                    ->implode(', ');
                                throw new \RuntimeException(
                                    "Student {$user->lrn} cannot be enrolled in {$gradeLevel} — unmet prerequisites: {$unmetNames}."
                                );
                            }

                            Enrollment::create([
                                'student_id'       => $user->id,
                                'section_id'       => $section->id,
                                'academic_year_id' => $activeYear->id,
                                'status'           => 'enrolled',
                            ]);
                            $user->update(['section_id' => $section->id]);
                        }
                    }

                    $imported++;
                });
            } catch (\Exception $e) {
                $errors[] = "Row {$row}: Database error — " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);

        AuditLog::record(AuditLog::CREATE_USER, [
            'action'   => 'bulk_csv_import',
            'imported' => $imported,
            'skipped'  => $skipped,
        ]);

        return view('admin.students.import', compact('imported', 'skipped', 'errors'));
    }

    private function validateRow(array $fields, int $row): array
    {
        $errors = [];

        foreach (['first_name', 'last_name', 'email', 'lrn'] as $req) {
            if (empty($fields[$req])) {
                $errors[] = "Row {$row}: missing '{$req}'.";
            }
        }

        if (!empty($fields['email']) && !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row {$row}: invalid email '{$fields['email']}'.";
        }

        if (!empty($fields['lrn']) && !preg_match('/^\d{12}$/', $fields['lrn'])) {
            $errors[] = "Row {$row}: LRN must be exactly 12 digits (got '{$fields['lrn']}').";
        }

        if (!empty($fields['grade_level']) && !in_array($fields['grade_level'], ['7','8','9','10','11','12'])) {
            $errors[] = "Row {$row}: grade_level must be 7–12.";
        }

        return $errors;
    }

    private function uniqueUsername(string $lrn): string
    {
        $base = $lrn;
        if (!User::where('username', $base)->exists()) {
            return $base;
        }
        return $base . Str::random(3);
    }
}
