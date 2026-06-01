<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Applicant::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('grade')) {
            $query->where('applying_for_grade', $request->input('grade'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('first_name',      'like', "%{$s}%")
                  ->orWhere('last_name',     'like', "%{$s}%")
                  ->orWhere('reference_number', 'like', "%{$s}%")
                  ->orWhere('lrn',           'like', "%{$s}%");
            });
        }

        $applicants = $query->with('reviewedBy')->paginate(25)->withQueryString();

        $grades = Applicant::distinct()->orderBy('applying_for_grade')->pluck('applying_for_grade');

        $counts = [
            'pending'      => Applicant::where('status', 'pending')->count(),
            'under_review' => Applicant::where('status', 'under_review')->count(),
            'accepted'     => Applicant::where('status', 'accepted')->count(),
            'rejected'     => Applicant::where('status', 'rejected')->count(),
        ];

        return view('admin.applicants.index', compact('applicants', 'grades', 'counts'));
    }

    public function show(Applicant $applicant): View
    {
        $applicant->load('reviewedBy');
        return view('admin.applicants.show', compact('applicant'));
    }

    public function updateStatus(Request $request, Applicant $applicant): RedirectResponse
    {
        $validated = $request->validate([
            'status'  => ['required', 'in:pending,under_review,accepted,rejected,enrolled'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $applicant->status;

        $applicant->update([
            'status'      => $validated['status'],
            'remarks'     => $validated['remarks'] ?? $applicant->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::record(AuditLog::APPLICANT_STATUS_UPDATED, [
            'applicant_id'     => $applicant->id,
            'reference_number' => $applicant->reference_number,
            'old_status'       => $old,
            'new_status'       => $validated['status'],
        ]);

        return back()->with('success', "Application status updated to \"{$validated['status']}\".");
    }
}
