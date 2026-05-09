<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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
        $sort       = $request->input('sort', 'created_at');
        $dir        = $request->input('dir', 'desc');

        // ── Query ──────────────────────────────────────────────────────────
        $query = AuditLog::query();

        if ($actor) {
            $query->where(function($q) use ($actor) {
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

        $logs = $query->orderBy($sort, $dir)->paginate(100)->withQueryString();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'total_events'         => AuditLog::count(),
            'failed_logins'        => AuditLog::where('action_type', 'LIKE', '%LOGIN_FAILED%')->whereDate('created_at', today())->count(),
            'privilege_violations' => AuditLog::where('action_type', 'LIKE', '%PRIVILEGE%')->count(),
            'grade_updates'        => AuditLog::where('action_type', 'LIKE', '%GRADE%')->count(),
            'locked_accounts'      => AuditLog::where('action_type', 'LIKE', '%LOCK%')->count(),
        ];

        return view('admin.threat.audit-log', compact('logs', 'stats', 'actor', 'actionType', 'dateFrom', 'dateTo'));
    }
}
