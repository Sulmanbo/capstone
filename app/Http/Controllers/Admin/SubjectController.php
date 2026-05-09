<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

/**
 * SubjectController
 *
 * Manages the master database of all subjects offered by the institution.
 * Each subject has a unique, immutable Subject ID.
 */
class SubjectController extends Controller
{
    /**
     * List all subjects
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $query = Subject::query();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $subjects = $query->orderBy('subject_code', 'asc')->paginate(50);
        
        return view('admin.registrars.subjects.index', compact('subjects'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.registrars.subjects.create');
    }

    /**
     * Store a new subject
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => ['required', 'string', 'max:50', 'unique:subjects,subject_code'],
            'subject_name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        
        $subject = Subject::create($validated);
        
        return redirect()
            ->route('admin.subjects.index')
            ->with('success', "Subject '{$subject->subject_name}' created successfully. Subject ID: {$subject->subject_id}");
    }

    /**
     * Show subject details
     */
    public function show(Subject $subject)
    {
        $curriculumUsage = $subject->curriculumMappings()
            ->with('academicYear')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.registrars.subjects.show', compact('subject', 'curriculumUsage'));
    }

    /**
     * Show edit form
     */
    public function edit(Subject $subject)
    {
        return view('admin.registrars.subjects.edit', compact('subject'));
    }

    /**
     * Update subject
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'subject_code' => ['required', 'string', 'max:50', 'unique:subjects,subject_code,' . $subject->id],
            'subject_name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        
        $subject->update($validated);
        
        return redirect()
            ->route('admin.subjects.index')
            ->with('success', "Subject '{$subject->subject_name}' updated successfully.");
    }

    /**
     * Delete subject (only if not used in curriculum)
     */
    public function destroy(Subject $subject)
    {
        if ($subject->isUsedInCurriculum()) {
            return back()
                ->withErrors(['curriculum' => 'Cannot delete subject that is used in curriculum mappings.']);
        }
        
        $name = $subject->subject_name;
        $subject->delete();
        
        return redirect()
            ->route('admin.subjects.index')
            ->with('success', "Subject '{$name}' deleted successfully.");
    }
}
