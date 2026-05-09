<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplianceController extends Controller
{
    public function index()
    {
        // ── Fetch recent export history ────────────────────────────────────
        $exportHistory = AuditLog::where('action_type', 'EXPORT_REPORT')
                                 ->orderBy('created_at', 'desc')
                                 ->take(20)
                                 ->get();

        return view('admin.threat.compliance', compact('exportHistory'));
    }

    public function export(Request $request)
    {
        $type     = $request->input('type', 'full');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $actor    = $request->input('actor');
        $action   = $request->input('action_type');

        // ── Build the dataset for the PDF ──────────────────────────────────
        $query = AuditLog::orderBy('created_at', 'desc');

        if ($type === 'threats') {
            $query->whereIn('action_type', ['LOGIN_FAILED', 'ACCOUNT_LOCKED', 'PRIVILEGE_VIOLATION', 'INJECTION_ATTEMPT', 'BRUTE_FORCE']);
        } elseif ($type === 'grades') {
            $query->where('action_type', 'LIKE', '%GRADE%');
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($actor) {
            $query->where(function($q) use ($actor) {
                $q->where('user_id', $actor)->orWhere('actor_name', 'like', "%{$actor}%");
            });
        }

        if ($action) {
            $query->where('action_type', 'like', "%{$action}%");
        }

        $logs = $query->get();

        // ── Log the export action ──────────────────────────────────────────
        AuditLog::create([
            'user_id'      => auth()->id(),
            'actor_name'   => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'action_type'  => 'EXPORT_REPORT',
            'data_payload' => "Report type: {$type} | Range: {$dateFrom} to {$dateTo} | Records: " . count($logs),
            'source_ip'    => $request->ip(),
        ]);

        // ── Generate PDF ───────────────────────────────────────────────────
        $pdf = Pdf::loadView('admin.threat.pdf-report', [
            'logs'        => $logs,
            'type'        => $type,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'generatedBy' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            'generatedAt' => now()->format('m/d/Y H:i:s'),
            'institution' => 'Phil. Academy of Sakya',
        ])->setPaper('a4', 'portrait');

        $filename = 'EncryptEd_' . ucfirst($type) . 'Report_' . now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
