<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters from query string ──────────────────────────────────────
        $actor      = $request->input('actor');
        $actionType = $request->input('action_type');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $sourceIp   = $request->input('source_ip');
        $sort       = $request->input('sort', 'created_at');
        $dir        = $request->input('dir', 'desc');

        // ── Query ──────────────────────────────────────────────────────────
        $query = AuditLog::query();

        if ($actor) {
            $query->where(function ($q) use ($actor) {
                $q->where('user_id', $actor)
                  ->orWhere('actor_name', 'like', "%{$actor}%");
            });
        }

        if ($actionType) {
            $query->where('action_type', $actionType);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($sourceIp) {
            $query->where('source_ip', 'like', "%{$sourceIp}%");
        }

        $logs = $query->orderBy($sort, $dir)->paginate(100)->withQueryString();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'total_events'         => AuditLog::count(),
            'failed_logins'        => AuditLog::where('action_type', 'LIKE', '%LOGIN_FAILED%')->whereDate('created_at', today())->count(),
            'privilege_violations' => AuditLog::where('action_type', 'LIKE', '%PRIVILEGE%')->count(),
            'grade_updates'        => AuditLog::where('action_type', 'LIKE', '%GRADE%')->count(),
            'locked_accounts'      => AuditLog::where('action_type', 'LIKE', '%LOCK%')->count(),
        ];

        return view('admin.threat.audit-log', compact(
            'logs', 'stats', 'actor', 'actionType', 'dateFrom', 'dateTo', 'sourceIp'
        ));
    }

    /**
     * GET /admin/audit/export.pdf
     *
     * Compliance Export — generates a PDF report of the audit trail for a
     * chosen date range. Supports external security audits and RA 10173
     * compliance reviews (FRS §Compliance & Reporting Interface).
     *
     * The export action itself is audited as AUDIT_LOG_EXPORTED.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
            'action_type' => ['nullable', 'string', 'max:64'],
            'actor'       => ['nullable', 'string', 'max:128'],
        ]);

        // Default to last 30 days if no range provided
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());

        $query = AuditLog::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('actor')) {
            $actor = $request->actor;
            $query->where(function ($q) use ($actor) {
                $q->where('user_id', $actor)
                  ->orWhere('actor_name', 'like', "%{$actor}%");
            });
        }

        // Cap at 5,000 rows per export to keep PDF size reasonable
        $logs = $query->orderBy('created_at', 'desc')->limit(5000)->get();
        $truncated = $query->count() > 5000;

        $generatedAt   = now();
        $generatedBy   = auth()->user();
        $totalReturned = $logs->count();
        $totalMatched  = $query->count();

        // Audit the export action itself
        AuditLog::record(AuditLog::AUDIT_LOG_EXPORTED, [
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
            'action_type'    => $request->action_type,
            'actor_filter'   => $request->actor,
            'rows_exported'  => $totalReturned,
            'rows_matched'   => $totalMatched,
            'truncated'      => $truncated,
        ]);

        $pdf = Pdf::loadView('admin.threat.audit-log-pdf', compact(
            'logs', 'dateFrom', 'dateTo', 'generatedAt', 'generatedBy',
            'totalReturned', 'totalMatched', 'truncated'
        ))->setPaper('a4', 'landscape');

        $filename = "audit-log-{$dateFrom}-to-{$dateTo}.pdf";
        return $pdf->download($filename);
    }
}

