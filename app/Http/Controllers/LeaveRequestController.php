<?php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    // ── Faculty: list own + submit ────────────────────────────────────────
    public function facultyIndex()
    {
        $requests = LeaveRequest::where('faculty_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();
        return view('leave.faculty-index', compact('requests'));
    }

    public function facultyStore(Request $request)
    {
        $data = $request->validate([
            'leave_type' => ['required', 'in:' . implode(',', array_keys(LeaveRequest::$types))],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'reason'     => ['required', 'string', 'max:1000'],
        ]);
        $data['faculty_id'] = auth()->id();
        $data['days_count'] = \Carbon\Carbon::parse($data['start_date'])
            ->diffInWeekdays(\Carbon\Carbon::parse($data['end_date'])) + 1;
        $data['status'] = 'pending';
        LeaveRequest::create($data);
        return back()->with('success', 'Leave request submitted successfully.');
    }

    // ── Admin/Registrar: manage all ────────────────────────────────────────
    public function adminIndex(Request $request)
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $requests = LeaveRequest::with('faculty', 'reviewer')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->whereHas('faculty', fn($q2) =>
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%")
                   ->orWhere('employee_number', 'like', "%{$search}%")
            ))
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        $counts = [
            'pending'  => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('leave.admin-index', compact('requests', 'counts', 'status', 'search'));
    }

    public function review(Request $request, LeaveRequest $leaveRequest)
    {
        $data = $request->validate([
            'status'        => ['required', 'in:approved,rejected'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $leaveRequest->update([
            'status'        => $data['status'],
            'admin_remarks' => $data['admin_remarks'] ?? null,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        Notification::create([
            'user_id' => $leaveRequest->faculty_id,
            'type'    => 'enrollment',
            'title'   => 'Leave Request ' . ucfirst($data['status']),
            'body'    => "Your {$leaveRequest->type_label} request has been {$data['status']}." .
                         ($data['admin_remarks'] ? " Remarks: {$data['admin_remarks']}" : ''),
        ]);

        return back()->with('success', 'Leave request ' . $data['status'] . '.');
    }

    public function bulkReview(Request $request)
    {
        $data = $request->validate([
            'ids'           => ['required', 'string'],
            'status'        => ['required', 'in:approved,rejected'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $ids = array_filter(array_map('intval', explode(',', $data['ids'])));
        if (empty($ids)) return back()->with('error', 'No requests selected.');

        $requests = LeaveRequest::whereIn('id', $ids)->where('status', 'pending')->get();

        foreach ($requests as $req) {
            $req->update([
                'status'        => $data['status'],
                'admin_remarks' => $data['admin_remarks'] ?? null,
                'reviewed_by'   => auth()->id(),
                'reviewed_at'   => now(),
            ]);
            Notification::create([
                'user_id' => $req->faculty_id,
                'type'    => 'enrollment',
                'title'   => 'Leave Request ' . ucfirst($data['status']),
                'body'    => "Your {$req->type_label} request has been {$data['status']}." .
                             ($data['admin_remarks'] ? " Remarks: {$data['admin_remarks']}" : ''),
            ]);
        }

        return back()->with('success', "{$requests->count()} leave request(s) " . $data['status'] . '.');
    }
}
