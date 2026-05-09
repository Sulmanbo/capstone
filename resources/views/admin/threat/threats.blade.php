{{-- resources/views/admin/threat/threats.blade.php --}}
@extends('layouts.app')

@section('title', 'Threat Events')
@section('breadcrumb', 'Threat Events')

@section('content')

{{-- Page Header --}}
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Threat Monitoring</h1>
      <p class="enc-page__subtitle">
        Active defense · Event-triggered response · Real-time
      </p>
    </div>
    <div class="enc-page__actions">
      <span class="enc-live-dot">MONITORING</span>
      <button class="enc-btn enc-btn--outline" onclick="window.location.reload()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-4.519l3.181 3.182m0-4.991v4.99"/>
        </svg>
        Refresh
      </button>
    </div>
  </div>
</div>

{{-- Critical Alert Banner (shown only if there are critical threats) --}}
@if(isset($criticalCount) && $criticalCount > 0)
  <div class="enc-alert enc-alert--danger">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <div class="enc-alert__body">
      <div class="enc-alert__title">{{ $criticalCount }} Critical Threat(s) Detected</div>
      <div class="enc-alert__text">
        Immediate review required. Accounts may have been automatically locked.
        Check the brute force and privilege escalation entries below.
      </div>
    </div>
  </div>
@endif

{{-- Threat Stats --}}
<div class="enc-stats">
  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['brute_force'] ?? 0 }}</div>
      <div class="enc-stat-label">Brute Force</div>
      <div class="enc-stat-delta enc-stat-delta--up">Today</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['injection_attempts'] ?? 0 }}</div>
      <div class="enc-stat-label">Injection Blocked</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['privilege_escalations'] ?? 0 }}</div>
      <div class="enc-stat-label">Priv. Escalations</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['accounts_locked'] ?? 0 }}</div>
      <div class="enc-stat-label">Accounts Locked</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['threats_resolved'] ?? 0 }}</div>
      <div class="enc-stat-label">Resolved</div>
      <div class="enc-stat-delta enc-stat-delta--down">↓ Cleared</div>
    </div>
  </div>
</div>

{{-- Two-column layout: Timeline + Defense Status --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

  {{-- Threat Event Timeline --}}
  <div class="enc-card">
    <div class="enc-card__header">
      <div class="enc-card__title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Recent Threat Events
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <select class="enc-select" id="threat-filter" style="height:30px;font-size:.75rem;">
          <option value="all">All Types</option>
          <option value="brute_force">Brute Force</option>
          <option value="injection">Injection</option>
          <option value="privilege">Privilege Escalation</option>
        </select>
      </div>
    </div>

    <div class="enc-card__body enc-card__body--no-pad">
      <div class="enc-timeline">
        @forelse($threats as $threat)
          <div class="enc-timeline-item" data-type="{{ $threat->threat_type }}">
            <div class="enc-timeline__indicator">
              <div class="enc-timeline__dot enc-timeline__dot--{{ $threat->severity ?? 'medium' }}"></div>
              <div class="enc-timeline__line"></div>
            </div>
            <div class="enc-timeline__content">
              <div class="enc-timeline__header">
                <span class="enc-timeline__event">{{ $threat->event_label ?? $threat->threat_type }}</span>
                <span class="enc-timeline__time">
                  {{ \Carbon\Carbon::parse($threat->created_at)->format('m/d H:i') }}
                </span>
              </div>
              <div class="enc-timeline__detail">
                {{ $threat->description ?? 'No additional details.' }}
              </div>
              <div class="enc-timeline__meta">
                @php
                  $sevClass = match($threat->severity ?? 'medium') {
                    'critical' => 'danger',
                    'high'     => 'warning',
                    'medium'   => 'info',
                    'low'      => 'neutral',
                    default    => 'neutral',
                  };
                @endphp
                <span class="enc-badge enc-badge--{{ $sevClass }}">
                  {{ strtoupper($threat->severity ?? 'MEDIUM') }}
                </span>

                @if($threat->status === 'resolved')
                  <span class="enc-badge enc-badge--success">Resolved</span>
                @elseif($threat->status === 'active')
                  <span class="enc-badge enc-badge--danger">Active</span>
                @endif

                @if($threat->source_ip)
                  <span class="mono mono--ip" style="font-size:.72rem;">{{ $threat->source_ip }}</span>
                @endif

                @if($threat->user_id)
                  <span style="font-size:.72rem;color:var(--gray-300);">
                    UID:{{ $threat->user_id }}
                  </span>
                @endif
              </div>
            </div>
          </div>
        @empty
          <div class="enc-empty" style="padding:40px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            <div class="enc-empty__title">No active threats</div>
            <div class="enc-empty__sub">System defenses are operating normally</div>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Right Column: Defense Modules Status --}}
  <div>

    {{-- Brute Force Counter --}}
    <div class="enc-card" style="margin-bottom:16px;">
      <div class="enc-card__header" style="padding:14px 16px;">
        <div class="enc-card__title" style="font-size:.82rem;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
          Brute Force Counter
        </div>
        <span class="enc-badge enc-badge--success">Active</span>
      </div>
      <div class="enc-card__body" style="padding:14px 16px;">
        <div style="font-size:.75rem;color:var(--gray-500);margin-bottom:12px;line-height:1.5;">
          Accounts locked after <strong>5</strong> consecutive failed login attempts.
          Lockout duration: <strong>10 minutes</strong>.
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div style="text-align:center;padding:10px;background:var(--gray-50);border-radius:var(--radius-md);">
            <div style="font-size:1.3rem;font-weight:700;color:var(--danger);font-family:var(--font-mono);">
              {{ $stats['brute_force'] ?? 0 }}
            </div>
            <div style="font-size:.65rem;color:var(--gray-400);margin-top:2px;">Triggers Today</div>
          </div>
          <div style="text-align:center;padding:10px;background:var(--gray-50);border-radius:var(--radius-md);">
            <div style="font-size:1.3rem;font-weight:700;color:var(--warning);font-family:var(--font-mono);">
              {{ $stats['accounts_locked'] ?? 0 }}
            </div>
            <div style="font-size:.65rem;color:var(--gray-400);margin-top:2px;">Locked Now</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Injection Defense --}}
    <div class="enc-card" style="margin-bottom:16px;">
      <div class="enc-card__header" style="padding:14px 16px;">
        <div class="enc-card__title" style="font-size:.82rem;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
          </svg>
          Injection Defense
        </div>
        <span class="enc-badge enc-badge--success">Active</span>
      </div>
      <div class="enc-card__body" style="padding:14px 16px;">
        <div style="font-size:.75rem;color:var(--gray-500);margin-bottom:10px;line-height:1.5;">
          Laravel Query Builder auto-escapes all input. Forbidden patterns trigger
          <strong>403 Forbidden</strong> before reaching the database.
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
          @foreach(["<script>", "--", "DROP", "UNION", "' OR", "<iframe>", "\\", "/etc/passwd"] as $pattern)
            <span style="font-family:var(--font-mono);font-size:.65rem;background:var(--danger-bg);color:var(--danger);padding:2px 6px;border-radius:3px;border:1px solid var(--danger-border);">
              {{ $pattern }}
            </span>
          @endforeach
        </div>
        <div style="margin-top:10px;font-size:.72rem;font-family:var(--font-mono);color:var(--info);">
          {{ $stats['injection_attempts'] ?? 0 }} blocked today
        </div>
      </div>
    </div>

    {{-- Privilege Escalation Monitor --}}
    <div class="enc-card">
      <div class="enc-card__header" style="padding:14px 16px;">
        <div class="enc-card__title" style="font-size:.82rem;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
          </svg>
          Privilege Escalation
        </div>
        <span class="enc-badge enc-badge--success">Active</span>
      </div>
      <div class="enc-card__body" style="padding:14px 16px;">
        <div style="font-size:.75rem;color:var(--gray-500);margin-bottom:12px;line-height:1.5;">
          Session Role validated against every protected route.
          Mismatches terminate the request and log a <strong>PRIVILEGE_VIOLATION</strong>.
        </div>

        {{-- Role table --}}
        <table style="width:100%;border-collapse:collapse;font-size:.72rem;">
          <thead>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <th style="padding:5px 6px;text-align:left;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.05em;font-size:.65rem;">Role</th>
              <th style="padding:5px 6px;text-align:center;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.05em;font-size:.65rem;">Access</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom:1px solid var(--gray-50);">
              <td style="padding:6px;font-weight:600;color:var(--navy);">Admin (R03)</td>
              <td style="padding:6px;text-align:center;"><span class="enc-badge enc-badge--navy">Full</span></td>
            </tr>
            <tr style="border-bottom:1px solid var(--gray-50);">
              <td style="padding:6px;font-weight:600;color:var(--navy);">Faculty (R02)</td>
              <td style="padding:6px;text-align:center;"><span class="enc-badge enc-badge--info">Grades Only</span></td>
            </tr>
            <tr>
              <td style="padding:6px;font-weight:600;color:var(--navy);">Student (R01)</td>
              <td style="padding:6px;text-align:center;"><span class="enc-badge enc-badge--neutral">View Only</span></td>
            </tr>
          </tbody>
        </table>

        <div style="margin-top:10px;font-size:.72rem;font-family:var(--font-mono);color:var(--danger);">
          {{ $stats['privilege_escalations'] ?? 0 }} violations logged
        </div>
      </div>
    </div>

  </div><!-- /.right-col -->
</div><!-- /.grid -->

{{-- Security Audit Log --}}
<div class="enc-card" style="margin-top:30px;">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Security Events Log
    </div>
    <a href="{{ route('admin.audit.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">
      View full log
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
      </svg>
    </a>
  </div>
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th>Actor</th>
            <th>Event</th>
            <th>Details</th>
            <th>IP Address</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          @forelse($auditLogs as $log)
            <tr>
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.8rem;">{{ $log->actor_name ?? 'System' }}</div>
                <div class="mono" style="font-size:.7rem;">ID:{{ $log->user_id ?? '-' }}</div>
              </td>
              <td>
                @php
                  $actionClass = match(true) {
                    str_starts_with($log->action_type, 'LOGIN')  => 'login',
                    str_starts_with($log->action_type, 'CREATE') => 'create',
                    str_starts_with($log->action_type, 'UPDATE') => 'update',
                    str_starts_with($log->action_type, 'DELETE') => 'delete',
                    str_starts_with($log->action_type, 'LOCK')   => 'lock',
                    str_contains($log->action_type, 'VIOLATION') => 'violation',
                    default => 'login',
                  };
                @endphp
                <span class="enc-action-tag enc-action-tag--{{ $actionClass }}">{{ $log->action_type }}</span>
              </td>
              <td style="font-size:.78rem;color:var(--gray-500);max-width:220px;">
                {{ $log->data_payload ? \Illuminate\Support\Str::limit($log->data_payload, 60) : '-' }}
              </td>
              <td class="mono mono--ip">{{ $log->source_ip ?? '-' }}</td>
              <td class="mono" style="font-size:.72rem;white-space:nowrap;">
                {{ \Carbon\Carbon::parse($log->created_at)->format('m/d H:i') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="enc-empty" style="padding:28px;">
                  <div class="enc-empty__title">No security events</div>
                  <div class="enc-empty__sub">Logs will appear here as security incidents occur</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // Client-side threat type filter
  const select = document.getElementById('threat-filter');
  if (select) {
    select.addEventListener('change', function () {
      const val = this.value;
      document.querySelectorAll('.enc-timeline-item').forEach(function (item) {
        if (val === 'all' || item.dataset.type === val) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }
</script>
@endpush
