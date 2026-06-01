<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    private const GRADE_LEVELS = [
        'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
        'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
    ];

    // ── Public: show the application form ──────────────────────────────────

    public function create(): View
    {
        return view('applicant.create', [
            'gradeLevels' => self::GRADE_LEVELS,
        ]);
    }

    // ── Public: handle submission ───────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'             => ['required', 'string', 'max:100'],
            'middle_name'            => ['nullable', 'string', 'max:100'],
            'last_name'              => ['required', 'string', 'max:100'],
            'suffix'                 => ['nullable', 'string', 'max:20'],
            'date_of_birth'          => ['required', 'date', 'before:today'],
            'sex'                    => ['required', 'in:Male,Female'],
            'lrn'                    => ['nullable', 'digits:12'],
            'nationality'            => ['nullable', 'string', 'max:80'],
            'address'                => ['required', 'string', 'max:300'],
            'barangay'               => ['nullable', 'string', 'max:100'],
            'municipality'           => ['nullable', 'string', 'max:100'],
            'province'               => ['nullable', 'string', 'max:100'],
            'zip_code'               => ['nullable', 'string', 'max:10'],
            'previous_school'        => ['nullable', 'string', 'max:200'],
            'previous_grade_level'   => ['nullable', 'string', 'max:50'],
            'school_year_completed'  => ['nullable', 'string', 'max:20'],
            'applying_for_grade'     => ['required', 'string', 'in:' . implode(',', self::GRADE_LEVELS)],
            'applying_for_year'      => ['nullable', 'string', 'max:20'],
            'parent_guardian_name'   => ['required', 'string', 'max:200'],
            'relationship'           => ['required', 'string', 'max:50'],
            'parent_contact'         => ['required', 'string', 'max:20'],
            'parent_email'           => ['nullable', 'email', 'max:180'],
        ]);

        $validated['nationality'] = $validated['nationality'] ?? 'Filipino';

        $applicant = Applicant::create($validated);

        return redirect()->route('apply.thanks', $applicant->reference_number);
    }

    // ── Public: thank-you / confirmation page ──────────────────────────────

    public function thanks(string $reference): View
    {
        $applicant = Applicant::where('reference_number', $reference)->firstOrFail();
        return view('applicant.thanks', compact('applicant'));
    }
}
