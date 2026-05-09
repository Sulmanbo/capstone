<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThreatEvent;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ThreatController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters from query string ──────────────────────────────────────
        $threatType = $request->input('threat_type');
        $severity = $request->input('severity');
        $status = $request->input('status');

        // ── Query Threats ──────────────────────────────────────────────────
        $threatQuery = ThreatEvent::orderBy('created_at', 'desc');

        if ($threatType) {
            $threatQuery->where('threat_type', $threatType);
        }

        if ($severity) {
            $threatQuery->where('severity', $severity);
        }

        if ($status) {
            $threatQuery->where('status', $status);
        }

        $threats = $threatQuery->paginate(50)->withQueryString();

        // ── Query Security-related Audit Logs ──────────────────────────────
        $auditLogs = AuditLog::whereIn('action_type', [
            'LOGIN_FAILED',
            'ACCOUNT_LOCKED',
            'PRIVILEGE_VIOLATION',
            'INJECTION_ATTEMPT',
            'BRUTE_FORCE',
        ])->orderBy('created_at', 'desc')->take(30)->get();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'brute_force'           => ThreatEvent::where('threat_type', 'brute_force')->where('status', 'active')->count(),
            'injection_attempts'    => ThreatEvent::where('threat_type', 'injection')->where('status', 'active')->count(),
            'privilege_escalations' => ThreatEvent::where('threat_type', 'privilege')->where('status', 'active')->count(),
            'accounts_locked'       => AuditLog::where('action_type', 'ACCOUNT_LOCKED')->whereDate('created_at', today())->count(),
            'threats_resolved'      => ThreatEvent::where('status', 'resolved')->count(),
        ];

        $criticalCount = ThreatEvent::where('severity', 'critical')->where('status', 'active')->count();

        return view('admin.threat.threats', compact('threats', 'auditLogs', 'stats', 'criticalCount', 'threatType', 'severity', 'status'));
    }
}
