<?php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Notification;
use App\Models\SectionSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    // ── Faculty: list assignments for their classes ───────────────────────
    public function facultyIndex()
    {
        $sectionSubjects = SectionSubject::with(['section', 'subject'])
            ->where('faculty_id', auth()->id())
            ->get();

        $assignments = Assignment::with(['sectionSubject.section', 'sectionSubject.subject', 'submissions'])
            ->whereIn('section_subject_id', $sectionSubjects->pluck('id'))
            ->orderByDesc('created_at')
            ->get();

        return view('assignments.faculty-index', compact('assignments', 'sectionSubjects'));
    }

    public function facultyStore(Request $request)
    {
        $data = $request->validate([
            'section_subject_id' => ['required', 'exists:section_subjects,id'],
            'title'              => ['required', 'string', 'max:255'],
            'instructions'       => ['nullable', 'string'],
            'type'               => ['required', 'in:assignment,quiz,project,activity'],
            'max_score'          => ['required', 'numeric', 'min:1', 'max:1000'],
            'due_date'           => ['required', 'date'],
            'allow_late'         => ['boolean'],
            'is_published'       => ['boolean'],
        ]);

        // Verify faculty owns this section_subject
        $ss = SectionSubject::findOrFail($data['section_subject_id']);
        if ($ss->faculty_id !== auth()->id()) {
            abort(403);
        }

        $data['created_by']   = auth()->id();
        $data['allow_late']   = $request->boolean('allow_late');
        $data['is_published'] = $request->boolean('is_published');

        $assignment = Assignment::create($data);

        if ($assignment->is_published) {
            $this->notifyStudents($assignment);
        }

        return back()->with('success', 'Assignment created successfully.');
    }

    public function facultyShow(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $assignment->load(['sectionSubject.section', 'sectionSubject.subject', 'submissions.student']);
        return view('assignments.faculty-show', compact('assignment'));
    }

    public function publish(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $assignment->update(['is_published' => true]);
        $this->notifyStudents($assignment);
        return back()->with('success', 'Assignment published.');
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $this->authorizeAssignment($submission->assignment);
        $data = $request->validate([
            'score'    => ['required', 'numeric', 'min:0', 'max:' . $submission->assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);
        $submission->update([
            'score'      => $data['score'],
            'feedback'   => $data['feedback'] ?? null,
            'status'     => 'graded',
            'graded_at'  => now(),
            'graded_by'  => auth()->id(),
        ]);

        Notification::create([
            'user_id' => $submission->student_id,
            'type'    => 'enrollment',
            'title'   => 'Assignment Graded',
            'body'    => "Your submission for \"{$submission->assignment->title}\" has been graded: {$data['score']}/{$submission->assignment->max_score}.",
        ]);

        return back()->with('success', 'Submission graded.');
    }

    // ── Student: view + submit ────────────────────────────────────────────
    public function studentIndex()
    {
        $user       = auth()->user();
        $enrollment = \App\Models\Enrollment::with('section')
            ->where('student_id', $user->id)
            ->where('status', 'enrolled')
            ->latest()
            ->first();

        $assignments = collect();
        if ($enrollment) {
            $sectionSubjectIds = SectionSubject::where('section_id', $enrollment->section_id)->pluck('id');
            $assignments = Assignment::with(['sectionSubject.subject', 'submissions' => fn($q) => $q->where('student_id', $user->id)])
                ->whereIn('section_subject_id', $sectionSubjectIds)
                ->where('is_published', true)
                ->orderBy('due_date')
                ->get();
        }

        return view('assignments.student-index', compact('assignments'));
    }

    public function studentSubmit(Request $request, Assignment $assignment)
    {
        if (!$assignment->is_published) abort(403);

        $data = $request->validate([
            'content' => ['nullable', 'string'],
            'file'    => ['nullable', 'file', 'max:10240'],
        ]);

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', auth()->id())
            ->first();

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('assignments', 'public');
            $fileName = $file->getClientOriginalName();
        }

        if ($existing) {
            $existing->update([
                'content'      => $data['content'] ?? $existing->content,
                'file_path'    => $filePath ?? $existing->file_path,
                'file_name'    => $fileName ?? $existing->file_name,
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]);
        } else {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id'    => auth()->id(),
                'content'       => $data['content'] ?? null,
                'file_path'     => $filePath,
                'file_name'     => $fileName,
                'status'        => 'submitted',
                'submitted_at'  => now(),
            ]);
        }

        return back()->with('success', 'Assignment submitted successfully.');
    }

    private function authorizeAssignment(Assignment $assignment): void
    {
        if ($assignment->sectionSubject->faculty_id !== auth()->id()) {
            abort(403);
        }
    }

    private function notifyStudents(Assignment $assignment): void
    {
        $assignment->load('sectionSubject.section.enrollments');
        foreach ($assignment->sectionSubject->section->enrollments ?? [] as $enrollment) {
            if ($enrollment->status === 'enrolled') {
                Notification::create([
                    'user_id' => $enrollment->student_id,
                    'type'    => 'enrollment',
                    'title'   => 'New Assignment Posted',
                    'body'    => "New {$assignment->type}: \"{$assignment->title}\" due " . $assignment->due_date->format('M d, Y'),
                ]);
            }
        }
    }
}
