{{-- resources/views/admin/threat/compliance.blade.php --}}
@extends('layouts.app')

@section('title', 'Compliance & Reports')
@section('breadcrumb', 'Compliance & Reports')

@section('content')

{{-- Page Header --}}
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Compliance & Reporting</h1>
      <p class="enc-page__subtitle">
        RA 10173 · Data Privacy Act of 2012 · Audit Documentation
      </p>
    </div>
    <div class="enc-page__actions">
      <span class="enc-badge enc-badge--success" style="font-size:.72rem;padding:4px 10px;">
        RA 10173 Compliant
      </span>
    </div>
  </div>
</div>

{{-- Compliance Status Banner --}}
<div class="enc-alert enc-alert--info" style="margin-bottom:24px;">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
  </svg>
  <div class="enc-alert__body">
    <div class="enc-alert__title">Audit Trail Active — Non-Repudiation Enforced</div>
    <div class="enc-alert__text">
      All exports are timestamped and logged. Generated reports include the
      <strong>Phil. Academy of Sakya</strong> institutional seal and serve as legal documentation
      for external security audits under the Data Privacy Act of 2012 (RA 10173).
    </div>
  </div>
</div>

{{-- Export Report Cards --}}
<div class="enc-report-grid">

  {{-- Full Audit Trail --}}
  <div class="enc-report-card">
    <div class="enc-report-card__icon enc-report-card__icon--pdf">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Full Audit Trail (PDF)</div>
    <div class="enc-report-card__desc">
      Complete chronological log of all system events. Includes actor IDs,
      action types, data payloads with before/after states, timestamps, and source IPs.
    </div>
    <div class="enc-report-card__footer">
      <span class="enc-report-card__compliance">RA 10173</span>
      <a href="{{ route('admin.compliance.export', ['type' => 'full']) }}"
         class="enc-btn enc-btn--danger enc-btn--sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Export PDF
      </a>
    </div>
  </div>

  {{-- Threat Incident Report --}}
  <div class="enc-report-card">
    <div class="enc-report-card__icon enc-report-card__icon--audit">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Threat Incident Report</div>
    <div class="enc-report-card__desc">
      Filtered report of security incidents only: brute force lockouts,
      injection attempts blocked (403), and privilege escalation violations.
    </div>
    <div class="enc-report-card__footer">
      <span class="enc-report-card__compliance">Security Audit</span>
      <a href="{{ route('admin.compliance.export', ['type' => 'threats']) }}"
         class="enc-btn enc-btn--export enc-btn--sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Export PDF
      </a>
    </div>
  </div>

  {{-- Grade Change Log --}}
  <div class="enc-report-card">
    <div class="enc-report-card__icon enc-report-card__icon--full">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Grade Change Audit Report</div>
    <div class="enc-report-card__desc">
      All UPDATE_GRADE events with before and after values, actor, section,
      and timestamp. Essential for academic integrity compliance.
    </div>
    <div class="enc-report-card__footer">
      <span class="enc-report-card__compliance">Academic Records</span>
      <a href="{{ route('admin.compliance.export', ['type' => 'grades']) }}"
         class="enc-btn enc-btn--outline enc-btn--sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Export PDF
      </a>
    </div>
  </div>

</div>

{{-- Custom Date Range Export --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
      </svg>
      Custom Range Export
    </div>
    <span class="enc-card__meta">Generates a scoped PDF for the selected date window</span>
  </div>
  <div class="enc-card__body">
    <form method="GET" action="{{ route('admin.compliance.export') }}">
      <input type="hidden" name="type" value="custom">

      <div class="enc-form-row">
        <div class="enc-form-group">
          <label class="enc-label" for="export_from">Date From</label>
          <input type="date" id="export_from" name="date_from"
                 value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                 class="enc-input enc-input--full">
        </div>
        <div class="enc-form-group">
          <label class="enc-label" for="export_to">Date To</label>
          <input type="date" id="export_to" name="date_to"
                 value="{{ request('date_to', now()->format('Y-m-d')) }}"
                 class="enc-input enc-input--full">
        </div>
        <div class="enc-form-group">
          <label class="enc-label" for="export_action">Action Type (Optional)</label>
          <select id="export_action" name="action_type" class="enc-select" style="height:38px;width:100%;">
            <option value="">All Actions</option>
            <option value="LOGIN_FAILED">LOGIN_FAILED</option>
            <option value="ACCOUNT_LOCKED">ACCOUNT_LOCKED</option>
            <option value="UPDATE_GRADE">UPDATE_GRADE</option>
            <option value="PRIVILEGE_VIOLATION">PRIVILEGE_VIOLATION</option>
            <option value="DELETE_RECORD">DELETE_RECORD</option>
          </select>
        </div>
        <div class="enc-form-group">
          <label class="enc-label" for="export_actor">Actor ID (Optional)</label>
          <input type="text" id="export_actor" name="actor"
                 placeholder="e.g. 10001"
                 class="enc-input enc-input--full">
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:10px;margin-top:4px;">
        <button type="submit" class="enc-btn enc-btn--primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
          </svg>
          Generate & Download PDF
        </button>
        <span style="font-size:.75rem;color:var(--gray-300);font-family:var(--font-mono);">
          This export will be logged in the audit trail.
        </span>
      </div>
    </form>
  </div>
</div>

{{-- Export History --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Recent Export History
    </div>
    <span class="enc-card__meta">All exports are audit-logged</span>
  </div>
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th>Report Type</th>
            <th>Generated By</th>
            <th>Date Range</th>
            <th>Timestamp</th>
            <th>Source IP</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($exportHistory ?? [] as $export)
            <tr>
              <td>
                <span class="enc-action-tag enc-action-tag--export">{{ $export->report_type }}</span>
              </td>
              <td>
                <div style="font-weight:600;font-size:.8rem;color:var(--navy);">
                  {{ $export->actor_name ?? 'Admin' }}
                </div>
                <div class="mono" style="font-size:.7rem;">ID:{{ $export->user_id }}</div>
              </td>
              <td class="mono" style="font-size:.75rem;">
                {{ $export->range_from ?? '—' }} → {{ $export->range_to ?? '—' }}
              </td>
              <td class="mono" style="white-space:nowrap;">
                {{ \Carbon\Carbon::parse($export->created_at)->format('m/d/Y H:i') }}
              </td>
              <td class="mono mono--ip">{{ $export->source_ip ?? '—' }}</td>
              <td>
                <span class="enc-badge enc-badge--success">Completed</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="enc-empty" style="padding:30px;">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                  </svg>
                  <div class="enc-empty__title">No exports generated yet</div>
                  <div class="enc-empty__sub">Your first export will appear here</div>
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
