<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

/**
 * SubjectController
 *
 * Manages the master database of all subjects offered by the institution.
 * Each subject has a unique, immutable Subject ID and optional custom grade weights.
 */
class SubjectController extends Controller
{
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

    public function create()
    {
        return view('admin.registrars.subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code'       => ['required', 'string', 'max:50', 'unique:subjects,subject_code'],
            'subject_name'       => ['required', 'string', 'max:200'],
            'year_level'         => ['nullable', 'string', 'max:20'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'credits'            => ['nullable', 'integer', 'min:1'],
            'status'             => ['required', 'in:active,inactive'],
            'use_custom_weights' => ['nullable'],
            'ww_weight'          => ['nullable', 'numeric', 'min:1', 'max:98'],
            'pt_weight'          => ['nullable', 'numeric', 'min:1', 'max:98'],
            'qa_weight'          => ['nullable', 'numeric', 'min:1', 'max:98'],
        ]);

        [$validated, $weightError] = $this->resolveWeights($request, $validated);

        if ($weightError) {
            return back()->withInput()->withErrors(['weights' => $weightError]);
        }

        $subject = Subject::create($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', "Subject '{$subject->subject_name}' created successfully. Subject ID: {$subject->subject_id}");
    }

    public function show(Subject $subject)
    {
        $curriculumUsage = $subject->curriculumMappings()
            ->with('academicYear')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.registrars.subjects.show', compact('subject', 'curriculumUsage'));
    }

    public function edit(Subject $subject)
    {
        return view('admin.registrars.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'subject_code'       => ['required', 'string', 'max:50', 'unique:subjects,subject_code,' . $subject->id],
            'subject_name'       => ['required', 'string', 'max:200'],
            'year_level'         => ['nullable', 'string', 'max:20'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'credits'            => ['nullable', 'integer', 'min:1'],
            'status'             => ['required', 'in:active,inactive'],
            'use_custom_weights' => ['nullable'],
            'ww_weight'          => ['nullable', 'numeric', 'min:1', 'max:98'],
            'pt_weight'          => ['nullable', 'numeric', 'min:1', 'max:98'],
            'qa_weight'          => ['nullable', 'numeric', 'min:1', 'max:98'],
        ]);

        [$validated, $weightError] = $this->resolveWeights($request, $validated);

        if ($weightError) {
            return back()->withInput()->withErrors(['weights' => $weightError]);
        }

        $subject->update($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', "Subject '{$subject->subject_name}' updated successfully.");
    }

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

    // ── Private helpers ────────────────────────────────────────────────────

    private function resolveWeights(Request $req, array $validated): array
    {
        if ($req->boolean('use_custom_weights')) {
            $ww  = (float) ($validated['ww_weight'] ?? 0);
            $pt  = (float) ($validated['pt_weight'] ?? 0);
            $qa  = (float) ($validated['qa_weight'] ?? 0);
            $sum = round($ww + $pt + $qa, 2);

            if (abs($sum - 100) > 0.5) {
                unset($validated['use_custom_weights']);
                return [$validated, "Custom weights must sum to 100%. Current sum: {$sum}%."];
            }

            $validated['ww_weight'] = $ww;
            $validated['pt_weight'] = $pt;
            $validated['qa_weight'] = $qa;
        } else {
            $validated['ww_weight'] = null;
            $validated['pt_weight'] = null;
            $validated['qa_weight'] = null;
        }

        unset($validated['use_custom_weights']);
        return [$validated, null];
    }
}
