{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('head')
<style>
/* ── Dashboard layout tokens ─────────────────────────── */
.adm-section-title {
  font-size:.82rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.07em; color:#64748b; margin:0 0 14px;
  display:flex; align-items:center; gap:8px;
}
.adm-section-title svg { width:15px; height:15px; }

/* ── Two-column widget row ───────────────────────────── */
.adm-widget-row {
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
  margin-top:20px;
}
@media(max-width:860px){ .adm-widget-row { grid-template-columns:1fr; } }

.adm-widget {
  background:#fff;
  border:1px solid #e2e8f0;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 2px 12px rgba(15,23,42,.05);
}
.adm-widget__head {
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 18px;
  border-bottom:1px solid #f1f5f9;
}
.adm-widget__title {
  font-size:.92rem; font-weight:700; color:#0f172a;
  display:flex; align-items:center; gap:8px;
}
.adm-widget__title svg { width:16px; height:16px; color:#4f46e5; }
.adm-widget__meta { font-size:.76rem; color:#94a3b8; }
.adm-widget__action {
  font-size:.78rem; font-weight:700; color:#4f46e5;
  text-decoration:none; padding:.3rem .75rem;
  border:1px solid rgba(79,70,229,.2); border-radius:7px;
  transition:background .15s;
}
.adm-widget__action:hover { background:#eef2ff; }
.adm-widget__body { padding:14px 18px; }

/* ── Announcement mini items ─────────────────────────── */
.adm-ann-item {
  display:flex; align-items:flex-start; gap:10px;
  padding:10px 0; border-bottom:1px solid #f8fafc;
}
.adm-ann-item:last-child { border-bottom:none; padding-bottom:0; }
.adm-ann-bar {
  width:4px; border-radius:2px; align-self:stretch;
  min-height:36px; flex-shrink:0;
}
.bar-high   { background:#ef4444; }
.bar-medium { background:#f59e0b; }
.bar-low    { background:#4f46e5; }
.adm-ann-info { flex:1; min-width:0; }
.adm-ann-title { font-size:.86rem; font-weight:700; color:#0f172a;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.adm-ann-sub { font-size:.74rem; color:#94a3b8; margin-top:2px; }
.adm-ann-badge {
  font-size:.68rem; font-weight:700; padding:.15rem .5rem;
  border-radius:999px; white-space:nowrap; align-self:flex-start; flex-shrink:0;
}
.ab-all      { background:rgba(16,185,129,.1); color:#059669; }
.ab-student  { background:rgba(99,102,241,.1); color:#4338ca; }
.ab-faculty  { background:rgba(245,158,11,.1); color:#d97706; }
.ab-registrar{ background:rgba(14,165,233,.1); color:#0284c7; }

/* ── Schedule mini items ─────────────────────────────── */
.adm-sch-item {
  display:flex; align-items:center; gap:10px;
  padding:9px 0; border-bottom:1px solid #f8fafc;
}
.adm-sch-item:last-child { border-bottom:none; padding-bottom:0; }
.adm-sch-avatar {
  width:32px; height:32px; border-radius:9px;
  background:linear-gradient(135deg,#0f766e,#0d9488);
  display:flex; align-items:center; justify-content:center;
  font-size:.7rem; font-weight:800; color:#fff; flex-shrink:0;
}
.adm-sch-info { flex:1; min-width:0; }
.adm-sch-subject { font-size:.86rem; font-weight:700; color:#0f172a; }
.adm-sch-detail  { font-size:.74rem; color:#94a3b8; margin-top:1px; }
.adm-sch-time    { font-size:.74rem; color:#475569; white-space:nowrap; font-family:monospace; }

.adm-empty {
  text-align:center; padding:24px 12px; color:#94a3b8;
  font-size:.84rem;
}

/* ── Summary chips on widget head ────────────────────── */
.adm-chip {
  display:inline-flex; align-items:center; gap:4px;
  font-size:.74rem; font-weight:600; padding:.2rem .6rem;
  border-radius:6px; background:#f1f5f9; color:#475569;
}
.adm-chip--green { background:rgba(16,185,129,.1); color:#059669; }
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────────────── --}}
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Welcome back, {{ auth()->user()->first_name }}</h1>
      <p class="enc-page__subtitle">Phil. Academy of Sakya — Admin Portal — {{ now()->format('F d, Y') }}</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.announcements.index') }}" class="enc-btn enc-btn--secondary" style="margin-right:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Post Announcement
      </a>
      <a href="{{ route('admin.users.create') }}" class="enc-btn enc-btn--primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
        New User
      </a>
    </div>
  </div>
</div>

{{-- ── Stat strip ───────────────────────────────────────────────────── --}}
<div class="enc-stats">
  <a href="{{ route('admin.students.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $studentCount }}</div>
      <div class="enc-stat-label">Total Students</div>
    </div>
  </a>

  <a href="{{ route('admin.faculty.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $facultyCount }}</div>
      <div class="enc-stat-label">Faculty</div>
    </div>
  </a>

  <a href="{{ route('admin.registrars.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $registrarCount }}</div>
      <div class="enc-stat-label">Registrars</div>
    </div>
  </a>

  <a href="{{ route('admin.announcements.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon" style="background:rgba(99,102,241,.1);color:#4f46e5;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $activeAnnouncements }}</div>
      <div class="enc-stat-label">Active Announcements</div>
    </div>
  </a>

  <a href="{{ route('admin.schedules.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon" style="background:rgba(15,118,110,.1);color:#0f766e;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $totalSchedules }}</div>
      <div class="enc-stat-label">Faculty Schedules</div>
    </div>
  </a>

  <a href="{{ route('admin.threat.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $activeThreats }}</div>
      <div class="enc-stat-label">Active Threats</div>
    </div>
  </a>

  <a href="{{ route('admin.locked-accounts.index') }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $lockedAccounts }}</div>
      <div class="enc-stat-label">Locked Accounts</div>
    </div>
  </a>
</div>

{{-- ── Announcements + Schedules widgets ───────────────────────────── --}}
<div class="adm-widget-row">

  {{-- Announcements widget --}}
  <div class="adm-widget">
    <div class="adm-widget__head">
      <div class="adm-widget__title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Announcements
        <span class="adm-chip adm-chip--green">{{ $activeAnnouncements }} active</span>
      </div>
      <a href="{{ route('admin.announcements.index') }}" class="adm-widget__action">Manage →</a>
    </div>
    <div class="adm-widget__body">
      @if($recentAnnouncements->isEmpty())
        <div class="adm-empty">No announcements yet. <a href="{{ route('admin.announcements.index') }}" style="color:#4f46e5;font-weight:600;">Post one now</a></div>
      @else
        @foreach($recentAnnouncements as $ann)
        <div class="adm-ann-item">
          <div class="adm-ann-bar bar-{{ $ann->priority }}"></div>
          <div class="adm-ann-info">
            <div class="adm-ann-title">{{ $ann->title }}</div>
            <div class="adm-ann-sub">{{ $ann->created_at->diffForHumans() }} · {{ $ann->author->first_name ?? 'Admin' }}</div>
          </div>
          <span class="adm-ann-badge ab-{{ $ann->target_audience }}">{{ $ann->audience_label }}</span>
        </div>
        @endforeach
        @if($totalAnnouncements > 4)
          <div style="text-align:center;padding-top:10px;font-size:.78rem;color:#94a3b8;">
            <a href="{{ route('admin.announcements.index') }}" style="color:#4f46e5;font-weight:600;">View all {{ $totalAnnouncements }} announcements →</a>
          </div>
        @endif
      @endif
    </div>
  </div>

  {{-- Faculty Schedules widget --}}
  <div class="adm-widget">
    <div class="adm-widget__head">
      <div class="adm-widget__title" style="color:#0f172a;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#0f766e;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        Faculty Schedules
        <span class="adm-chip">{{ $totalSchedules }} assigned</span>
      </div>
      <a href="{{ route('admin.schedules.index') }}" class="adm-widget__action" style="color:#0f766e;border-color:rgba(15,118,110,.2);">Manage →</a>
    </div>
    <div class="adm-widget__body">
      @if($recentSchedules->isEmpty())
        <div class="adm-empty">No schedules assigned yet. <a href="{{ route('admin.schedules.index') }}" style="color:#0f766e;font-weight:600;">Assign one now</a></div>
      @else
        @foreach($recentSchedules as $sch)
        <div class="adm-sch-item">
          <div class="adm-sch-avatar">
            {{ strtoupper(substr($sch->faculty->first_name ?? 'F', 0, 1)) }}{{ strtoupper(substr($sch->faculty->last_name ?? '', 0, 1)) }}
          </div>
          <div class="adm-sch-info">
            <div class="adm-sch-subject">{{ $sch->subject_name }} @if($sch->section) · <span style="font-weight:500;color:#475569;">{{ $sch->section }}</span>@endif</div>
            <div class="adm-sch-detail">{{ $sch->faculty->last_name ?? '—' }}, {{ $sch->faculty->first_name ?? '' }} @if($sch->room) · {{ $sch->room }}@endif</div>
          </div>
          <div class="adm-sch-time">{{ $sch->days_label }}</div>
        </div>
        @endforeach
        @if($totalSchedules > 4)
          <div style="text-align:center;padding-top:10px;font-size:.78rem;color:#94a3b8;">
            <a href="{{ route('admin.schedules.index') }}" style="color:#0f766e;font-weight:600;">View all {{ $totalSchedules }} schedules →</a>
          </div>
        @endif
      @endif
    </div>
  </div>

</div>

{{-- ── Security & Activity Overview ────────────────────────────────── --}}
<div class="enc-card" style="margin-top:20px;">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Security & Activity Overview
    </div>
    <div class="enc-card__meta">Fast access to your most important admin tools</div>
  </div>
  <div class="enc-card__body">
    <div class="enc-report-grid">
      <a href="{{ route('admin.threat.index') }}" class="enc-report-card">
        <div class="enc-report-card__icon enc-report-card__icon--audit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </div>
        <div class="enc-report-card__title">Threat Events</div>
        <div class="enc-report-card__desc">Monitor active threats and take action on ongoing security events.</div>
        <div class="enc-report-card__footer"><span>Active items</span><strong>{{ $activeThreats }}</strong></div>
      </a>

      <a href="{{ route('admin.audit.index') }}" class="enc-report-card">
        <div class="enc-report-card__icon enc-report-card__icon--audit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </div>
        <div class="enc-report-card__title">Audit Log</div>
        <div class="enc-report-card__desc">Review trail information for compliance and incident investigation.</div>
        <div class="enc-report-card__footer"><span>Audit history</span><strong>Open</strong></div>
      </a>

      <a href="{{ route('admin.compliance.index') }}" class="enc-report-card">
        <div class="enc-report-card__icon enc-report-card__icon--full">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
          </svg>
        </div>
        <div class="enc-report-card__title">Compliance Reports</div>
        <div class="enc-report-card__desc">Export incident reports and RA 10173 compliance records.</div>
        <div class="enc-report-card__footer"><span>Ready</span><strong>View</strong></div>
      </a>
    </div>
  </div>
</div>

{{-- ── Quick Actions ────────────────────────────────────────────────── --}}
<div class="enc-report-grid" style="margin-top:20px;">
  <a href="{{ route('admin.users.create') }}" class="enc-report-card">
    <div class="enc-report-card__icon enc-report-card__icon--audit">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Create User</div>
    <div class="enc-report-card__desc">Add a new student, faculty, registrar, or admin account quickly.</div>
  </a>

  <a href="{{ route('admin.announcements.index') }}" class="enc-report-card">
    <div class="enc-report-card__icon" style="background:rgba(99,102,241,.08);color:#4f46e5;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Post Announcement</div>
    <div class="enc-report-card__desc">Send a targeted announcement to students, faculty, registrars, or everyone.</div>
  </a>

  <a href="{{ route('admin.schedules.index') }}" class="enc-report-card">
    <div class="enc-report-card__icon" style="background:rgba(15,118,110,.08);color:#0f766e;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Assign Schedule</div>
    <div class="enc-report-card__desc">Assign subjects, sections, and classrooms to faculty members.</div>
  </a>

  <a href="{{ route('admin.audit.index') }}" class="enc-report-card">
    <div class="enc-report-card__icon enc-report-card__icon--audit">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Audit Log</div>
    <div class="enc-report-card__desc">Open your full event log and security history.</div>
  </a>
</div>

@endsection
