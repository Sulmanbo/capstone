{{-- resources/views/admin/threat/audit-log.blade.php --}}
@extends('layouts.app')

@section('title', 'Audit Log')
@section('breadcrumb', 'Audit Log')

@section('content')

{{-- Page Header --}}
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">System Audit Log</h1>
      <p class="enc-page__subtitle">
        Immutable transaction history · Last updated {{ now()->format('m/d/Y H:i:s') }}
      </p>
    </div>
    <div class="enc-page__actions">
      <span class="enc-live-dot">LIVE</span>
      <a href="{{ route('admin.compliance.export') }}" class="enc-btn enc-btn--export">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Export PDF
      </a>
    </div>
  </div>
</div>

{{-- Summary Stats --}}
<div class="enc-stats">
  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ number_format($stats['total_events'] ?? 0) }}</div>
      <div class="enc-stat-label">Total Events</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['failed_logins'] ?? 0 }}</div>
      <div class="enc-stat-label">Failed Logins</div>
      <div class="enc-stat-delta enc-stat-delta--up">↑ Today</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['privilege_violations'] ?? 0 }}</div>
      <div class="enc-stat-label">Violations</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['grade_updates'] ?? 0 }}</div>
      <div class="enc-stat-label">Grade Changes</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['locked_accounts'] ?? 0 }}</div>
      <div class="enc-stat-label">Locked Accounts</div>
    </div>
  </div>
</div>

{{-- Audit Log Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
      </svg>
      Event Log
    </div>
    <span class="enc-card__meta">Showing last 100 events · Write-only repository</span>
  </div>

  {{-- Filter Bar --}}
  <form method="GET" action="{{ route('admin.audit.index') }}">
    <div class="enc-filter-bar">

      {{-- Search Actor --}}
      <div class="enc-filter-group">
        <span class="enc-filter-label">Actor</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input
            type="text"
            name="actor"
            value="{{ request('actor') }}"
            placeholder="User ID or name..."
            class="enc-input enc-input--search"
            style="width:180px;">
        </div>
      </div>

      <div class="enc-filter-divider"></div>

      {{-- Action Type --}}
      <div class="enc-filter-group">
        <span class="enc-filter-label">Action</span>
        <select name="action_type" class="enc-select">
          <option value="">All Actions</option>
          <option value="LOGIN_SUCCESS"  {{ request('action_type') == 'LOGIN_SUCCESS'  ? 'selected' : '' }}>LOGIN_SUCCESS</option>
          <option value="LOGIN_FAILED"   {{ request('action_type') == 'LOGIN_FAILED'   ? 'selected' : '' }}>LOGIN_FAILED</option>
          <option value="ACCOUNT_LOCKED" {{ request('action_type') == 'ACCOUNT_LOCKED' ? 'selected' : '' }}>ACCOUNT_LOCKED</option>
          <option value="UPDATE_GRADE"   {{ request('action_type') == 'UPDATE_GRADE'   ? 'selected' : '' }}>UPDATE_GRADE</option>
          <option value="LOCK_SECTION"   {{ request('action_type') == 'LOCK_SECTION'   ? 'selected' : '' }}>LOCK_SECTION</option>
          <option value="CREATE_USER"    {{ request('action_type') == 'CREATE_USER'    ? 'selected' : '' }}>CREATE_USER</option>
          <option value="DELETE_RECORD"  {{ request('action_type') == 'DELETE_RECORD'  ? 'selected' : '' }}>DELETE_RECORD</option>
          <option value="PRIVILEGE_VIOLATION" {{ request('action_type') == 'PRIVILEGE_VIOLATION' ? 'selected' : '' }}>PRIVILEGE_VIOLATION</option>
          <option value="EXPORT_REPORT"  {{ request('action_type') == 'EXPORT_REPORT'  ? 'selected' : '' }}>EXPORT_REPORT</option>
        </select>
      </div>

      <div class="enc-filter-divider"></div>

      {{-- Date Range --}}
      <div class="enc-filter-group">
        <span class="enc-filter-label">From</span>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="enc-input" style="width:140px;">
      </div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">To</span>
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="enc-input" style="width:140px;">
      </div>

      <div class="enc-filter-divider"></div>

      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
        </svg>
        Filter
      </button>

      @if(request()->hasAny(['actor','action_type','date_from','date_to']))
        <a href="{{ route('admin.audit.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">
          Clear
        </a>
      @endif

    </div>
  </form>

  {{-- Table --}}
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th class="sortable">
              <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc']) }}"
                 style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;">
                #ID
                <span class="sort-icon">↕</span>
              </a>
            </th>
            <th>Actor (User ID)</th>
            <th>Action Type</th>
            <th>Data Payload</th>
            <th class="sortable">
              <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc']) }}"
                 style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;">
                Timestamp
                <span class="sort-icon">↕</span>
              </a>
            </th>
            <th>Source IP</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr class="{{ in_array($log->action_type, ['PRIVILEGE_VIOLATION','ACCOUNT_LOCKED']) ? 'enc-threat-row--high' : '' }}">

              {{-- ID --}}
              <td class="mono">#{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</td>

              {{-- Actor --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.8rem;">
                  {{ $log->actor_name ?? 'System' }}
                </div>
                <div class="mono" style="font-size:.7rem;">ID:{{ $log->user_id ?? '—' }}</div>
              </td>

              {{-- Action Type --}}
              <td>
                @php
                  $actionClass = match(true) {
                    str_starts_with($log->action_type, 'LOGIN')   => 'login',
                    str_starts_with($log->action_type, 'CREATE')  => 'create',
                    str_starts_with($log->action_type, 'UPDATE')  => 'update',
                    str_starts_with($log->action_type, 'DELETE')  => 'delete',
                    str_starts_with($log->action_type, 'LOCK')    => 'lock',
                    str_starts_with($log->action_type, 'EXPORT')  => 'export',
                    str_contains($log->action_type, 'VIOLATION')  => 'violation',
                    default                                        => 'login',
                  };
                @endphp
                <span class="enc-action-tag enc-action-tag--{{ $actionClass }}">
                  {{ $log->action_type }}
                </span>
              </td>

              {{-- Payload --}}
              <td style="max-width:260px;">
                @if($log->data_payload)
                  <div style="font-size:.78rem;color:var(--gray-500);line-height:1.4;">
                    {{ Str::limit($log->data_payload, 80) }}
                  </div>
                @else
                  <span style="color:var(--gray-200);font-size:.75rem;">—</span>
                @endif
              </td>

              {{-- Timestamp --}}
              <td class="mono" style="white-space:nowrap;">
                {{ \Carbon\Carbon::parse($log->created_at)->format('m/d/Y') }}<br>
                <span style="color:var(--gray-300);font-size:.7rem;">
                  {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                </span>
              </td>

              {{-- Source IP --}}
              <td class="mono mono--ip">{{ $log->source_ip ?? '—' }}</td>

              {{-- Severity Badge --}}
              <td>
                @if(in_array($log->action_type, ['PRIVILEGE_VIOLATION','ACCOUNT_LOCKED','LOGIN_FAILED']))
                  <span class="enc-badge enc-badge--danger">Critical</span>
                @elseif(in_array($log->action_type, ['UPDATE_GRADE','DELETE_RECORD']))
                  <span class="enc-badge enc-badge--warning">Sensitive</span>
                @elseif($log->action_type === 'LOGIN_SUCCESS')
                  <span class="enc-badge enc-badge--success">Normal</span>
                @else
                  <span class="enc-badge enc-badge--neutral">Info</span>
                @endif
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="enc-empty">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                  </svg>
                  <div class="enc-empty__title">No log entries found</div>
                  <div class="enc-empty__sub">Adjust your filters or check back later</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="enc-pagination">
      <div class="enc-pagination__info">
        Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() ?? 0 }} events
      </div>
      <div class="enc-pagination__pages">
        {{-- Previous --}}
        @if($logs->onFirstPage())
          <button class="enc-page-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
          </button>
        @else
          <a href="{{ $logs->previousPageUrl() }}" class="enc-page-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
          </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
          <a href="{{ $url }}" class="enc-page-btn {{ $page == $logs->currentPage() ? 'active' : '' }}">
            {{ $page }}
          </a>
        @endforeach

        {{-- Next --}}
        @if($logs->hasMorePages())
          <a href="{{ $logs->nextPageUrl() }}" class="enc-page-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </a>
        @else
          <button class="enc-page-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </button>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection
