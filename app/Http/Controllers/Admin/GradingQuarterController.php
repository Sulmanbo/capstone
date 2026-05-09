<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\GradingQuarter;
use Illuminate\Http\Request;

/**
 * GradingQuarterController
 *
 * Manages grading quarters within academic years.
 * Enforces: Only one quarter can be "active" per academic year.
 */
class GradingQuarterController extends Controller
{
    /**
     * List grading quarters for an academic year
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $academicYearId = $request->input('academic_year_id');
        
        $query = GradingQuarter::with('academicYear');
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $quarters = $query->orderBy('academic_year_id', 'desc')
                          ->orderBy('quarter_number', 'asc')
                          ->paginate(50);
        
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        
        return view('admin.registrars.grading-quarters.index', compact('quarters', 'academicYears'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $academicYear = $academicYearId ? AcademicYear::findOrFail($academicYearId) : null;
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        
        return view('admin.registrars.grading-quarters.create', compact('academicYear', 'academicYears'));
    }

    /**
     * Store a new grading quarter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'quarter_number' => ['required', 'integer', 'min:1', 'max:4'],
            'quarter_name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);
        
        // Check for duplicate quarter number in this academic year
        $exists = GradingQuarter::where('academic_year_id', $validated['academic_year_id'])
            ->where('quarter_number', $validated['quarter_number'])
            ->exists();
        
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['quarter_number' => 'Quarter already exists for this academic year.']);
        }
        
        $quarter = GradingQuarter::create($validated);
        
        return redirect()
            ->route('admin.grading-quarters.index')
            ->with('success', "Grading Quarter '{$quarter->quarter_name}' created successfully.");
    }

    /**
     * Show edit form
     */
    public function edit(GradingQuarter $quarter)
    {
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        
        return view('admin.registrars.grading-quarters.edit', compact('quarter', 'academicYears'));
    }

    /**
     * Update grading quarter
     */
    public function update(Request $request, GradingQuarter $quarter)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'quarter_number' => ['required', 'integer', 'min:1', 'max:4'],
            'quarter_name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);
        
        // Check for duplicate quarter number (excluding current)
        $exists = GradingQuarter::where('academic_year_id', $validated['academic_year_id'])
            ->where('quarter_number', $validated['quarter_number'])
            ->where('id', '!=', $quarter->id)
            ->exists();
        
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['quarter_number' => 'Quarter already exists for this academic year.']);
        }
        
        $quarter->update($validated);
        
        return redirect()
            ->route('admin.grading-quarters.index')
            ->with('success', "Grading Quarter '{$quarter->quarter_name}' updated successfully.");
    }

    /**
     * Delete grading quarter
     */
    public function destroy(GradingQuarter $quarter)
    {
        $name = $quarter->quarter_name;
        $quarter->delete();
        
        return redirect()
            ->route('admin.grading-quarters.index')
            ->with('success', "Grading Quarter '{$name}' deleted successfully.");
    }
}
