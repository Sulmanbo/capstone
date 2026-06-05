<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Stores the globally-selected academic year in the session so every
 * year-scoped page (Sections, Subjects, Schedules, Grades, Reports, …)
 * shares the same working year. Set from the selector in the top header.
 */
class AcademicYearContextController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
        ]);

        if ($request->filled('academic_year_id')) {
            session(['active_academic_year_id' => (int) $request->academic_year_id]);
        } else {
            session()->forget('active_academic_year_id');
        }

        // Return to the page the user was on, dropping any stale ?academic_year_id
        // query string so the new session value takes effect cleanly.
        $back = url()->previous();
        $back = preg_replace('/([?&])academic_year_id=[^&]*(&|$)/', '$1', $back);
        $back = rtrim($back, '?&');

        return redirect($back ?: '/');
    }
}
