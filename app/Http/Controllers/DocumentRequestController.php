<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DocumentRequest;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    // ── Student: list own requests + submit new ───────────────────────────
    public function studentIndex()
    {
        $requests = DocumentRequest::where('student_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();
        return view('documents.student-index', compact('requests'));
    }

    public function studentStore(Request $request)
    {
        $data = $request->validate([
            'document_type' => ['required', 'in:' . implode(',', array_keys(DocumentRequest::$types))],
            'purpose'       => ['required', 'string', 'max:500'],
            'copies'        => ['required', 'integer', 'min:1', 'max:10'],
        ]);
        $data['student_id'] = auth()->id();
        DocumentRequest::create($data);
        return back()->with('success', 'Document request submitted. The registrar will process it shortly.');
    }

    // ── Registrar: manage all requests ───────────────────────────────────
    public function registrarIndex(Request $request)
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $requests = DocumentRequest::with('student','processor')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->whereHas('student', fn($q2) =>
                $q2->where('first_name','like',"%{$search}%")
                   ->orWhere('last_name','like',"%{$search}%")
                   ->orWhere('lrn','like',"%{$search}%")
            ))
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        $counts = [
            'pending'    => DocumentRequest::where('status','pending')->count(),
            'processing' => DocumentRequest::where('status','processing')->count(),
            'ready'      => DocumentRequest::where('status','ready')->count(),
        ];

        return view('documents.registrar-index', compact('requests','counts','status','search'));
    }

    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        $data = $request->validate([
            'status'  => ['required', 'in:processing,ready,released,rejected'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $update = ['status' => $data['status'], 'remarks' => $data['remarks']];

        if (in_array($data['status'], ['processing','ready','rejected'])) {
            $update['processed_by'] = auth()->id();
            $update['processed_at'] = now();
        }
        if ($data['status'] === 'released') {
            $update['released_at'] = now();
        }

        $documentRequest->update($update);

        // Notify student
        \App\Models\Notification::create([
            'user_id' => $documentRequest->student_id,
            'type'    => 'enrollment',
            'title'   => 'Document Request Update',
            'body'    => "Your {$documentRequest->document_label} is now: " . ucfirst($data['status']),
        ]);

        return back()->with('success', 'Request status updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'ids'     => ['required', 'string'],
            'status'  => ['required', 'in:processing,ready,released,rejected'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $ids = array_filter(array_map('intval', explode(',', $data['ids'])));
        if (empty($ids)) return back()->with('error', 'No requests selected.');

        $requests = DocumentRequest::whereIn('id', $ids)->whereNotIn('status', ['released'])->get();

        $update = ['status' => $data['status'], 'remarks' => $data['remarks'] ?? null];
        if (in_array($data['status'], ['processing', 'ready', 'rejected'])) {
            $update['processed_by'] = auth()->id();
            $update['processed_at'] = now();
        }
        if ($data['status'] === 'released') {
            $update['released_at'] = now();
        }

        foreach ($requests as $req) {
            $req->update($update);
            \App\Models\Notification::create([
                'user_id' => $req->student_id,
                'type'    => 'enrollment',
                'title'   => 'Document Request Update',
                'body'    => "Your {$req->document_label} is now: " . ucfirst($data['status']),
            ]);
        }

        return back()->with('success', "Updated {$requests->count()} document request(s) to " . ucfirst($data['status']) . '.');
    }
}
