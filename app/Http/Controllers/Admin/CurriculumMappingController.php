<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CurriculumMapping;
use App\Models\Subject;
use Illuminate\Http\Request;

/**
 * CurriculumMappingController
 *
 * Manages curriculum mappings (subject assignments to grade levels).
 * Creates the hierarchy that allows automatic generation of student class lists.
 */
class CurriculumMappingController extends Controller
{
    /**
     * List curriculum mappings
     */
    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $gradeLevel = $request->input('grade_level');
        $status = $request->input('status');
        
        $query = CurriculumMapping::with(['academicYear', 'subject', 'prerequisiteSubject']);
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        if ($gradeLevel) {
            $query->where('grade_level', $gradeLevel);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $mappings = $query->orderBy('academic_year_id', 'desc')
                         ->orderBy('grade_level', 'asc')
                         ->orderBy('sequence_order', 'asc')
                         ->paginate(50);
        
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        
        // Get distinct grade levels
        $gradeLevels = CurriculumMapping::distinct()
            ->pluck('grade_level')
            ->sort()
            ->values();
        
        return view('admin.registrars.curriculum-mappings.index', compact('mappings', 'academicYears', 'gradeLevels'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $gradeLevel = $request->input('grade_level');

        $academicYear = $academicYearId ? AcademicYear::findOrFail($academicYearId) : null;
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        $subjects = Subject::where('status', 'active')->orderBy('subject_code', 'asc')->get();

        $standardGradeLevels = [
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
            'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'
        ];

        return view('admin.registrars.curriculum-mappings.create', compact(
            'academicYear',
            'academicYears',
            'subjects',
            'standardGradeLevels',
            'gradeLevel'
        ));
    }

    /**
     * Store a new curriculum mapping
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id'        => ['required', 'exists:academic_years,id'],
            'grade_level'             => ['required', 'string', 'max:100'],
            'subject_id'              => ['required', 'exists:subjects,id'],
            'prerequisite_subject_id' => ['nullable', 'exists:subjects,id', 'different:subject_id'],
            'prerequisite_min_grade'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_required'             => ['required', 'boolean'],
            'sequence_order'          => ['nullable', 'integer', 'min:0'],
            'status'                  => ['required', 'in:active,inactive'],
        ]);
        
        // Check for duplicate
        $exists = CurriculumMapping::where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_level', $validated['grade_level'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();
        
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'This subject is already assigned to this grade level in this academic year.']);
        }
        
        CurriculumMapping::create($validated);

        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', "Curriculum mapping created successfully.");
    }

    /**
     * Show edit form
     */
    public function edit(CurriculumMapping $mapping)
    {
        $mapping->load(['academicYear', 'subject', 'prerequisiteSubject']);
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        $subjects = Subject::where('status', 'active')->orderBy('subject_code', 'asc')->get();

        $standardGradeLevels = [
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
            'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'
        ];

        return view('admin.registrars.curriculum-mappings.edit', compact(
            'mapping',
            'academicYears',
            'subjects',
            'standardGradeLevels'
        ));
    }

    /**
     * Update curriculum mapping
     */
    public function update(Request $request, CurriculumMapping $mapping)
    {
        $validated = $request->validate([
            'academic_year_id'        => ['required', 'exists:academic_years,id'],
            'grade_level'             => ['required', 'string', 'max:100'],
            'subject_id'              => ['required', 'exists:subjects,id'],
            'prerequisite_subject_id' => ['nullable', 'exists:subjects,id', 'different:subject_id'],
            'prerequisite_min_grade'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_required'             => ['required', 'boolean'],
            'sequence_order'          => ['nullable', 'integer', 'min:0'],
            'status'                  => ['required', 'in:active,inactive'],
        ]);
        
        // Check for duplicate (excluding current mapping)
        $exists = CurriculumMapping::where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_level', $validated['grade_level'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $mapping->id)
            ->exists();
        
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'This subject is already assigned to this grade level in this academic year.']);
        }
        
        $mapping->update($validated);
        
        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', "Curriculum mapping updated successfully.");
    }

    /**
     * Delete curriculum mapping
     */
    public function destroy(CurriculumMapping $mapping)
    {
        $mapping->delete();
        
        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', "Curriculum mapping deleted successfully.");
    }

    /**
     * Bulk operations endpoint
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:curriculum_mappings,id'],
        ]);
        
        $query = CurriculumMapping::whereIn('id', $validated['ids']);
        
        switch ($validated['action']) {
            case 'activate':
                $query->update(['status' => 'active']);
                $message = 'Curriculum mappings activated successfully.';
                break;
            case 'deactivate':
                $query->update(['status' => 'inactive']);
                $message = 'Curriculum mappings deactivated successfully.';
                break;
            case 'delete':
                $query->delete();
                $message = 'Curriculum mappings deleted successfully.';
                break;
        }
        
        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', $message);
    }
}
