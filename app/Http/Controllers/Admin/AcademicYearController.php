<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

/**
 * AcademicYearController
 *
 * Manages academic years (e.g., 2025-2026).
 * Enforces: Only one academic year can be "active" at any time.
 */
class AcademicYearController extends Controller
{
    /**
     * List all academic years
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $query = AcademicYear::query();
        
        if ($search) {
            $query->where('year_label', 'like', "%{$search}%");
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $academicYears = $query->orderBy('created_at', 'desc')->paginate(50);
        
        return view('admin.registrars.academic-years.index', compact('academicYears'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.registrars.academic-years.create');
    }

    /**
     * Store a new academic year
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_label' => ['required', 'string', 'max:50', 'unique:academic_years,year_label'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);
        
        $academicYear = AcademicYear::create($validated);
        
        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$academicYear->year_label}' created successfully.");
    }

    /**
     * Show edit form
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.registrars.academic-years.edit', compact('academicYear'));
    }

    /**
     * Update academic year
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'year_label' => ['required', 'string', 'max:50', 'unique:academic_years,year_label,' . $academicYear->id],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);
        
        $academicYear->update($validated);
        
        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$academicYear->year_label}' updated successfully.");
    }

    /**
     * Delete academic year
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Prevent deletion of active academic years
        if ($academicYear->status === 'active') {
            return back()->withErrors(['status' => 'Cannot delete an active academic year.']);
        }
        
        // Check if there are associated quarters
        if ($academicYear->quarters()->count() > 0) {
            return back()->withErrors(['grade_levels' => 'Cannot delete academic year with associated grading quarters.']);
        }
        
        $label = $academicYear->year_label;
        $academicYear->delete();
        
        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$label}' deleted successfully.");
    }
}
