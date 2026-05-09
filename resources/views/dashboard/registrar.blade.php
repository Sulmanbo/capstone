@extends('layouts.app')
@section('title', 'Registrar Dashboard')
@section('breadcrumb', 'Registrar Dashboard')

@push('head')
<style>
/* ── Registrar-specific components ─ */
.sd-req-row { display: flex; align-items: flex-start; gap: 12px; padding: 11px 12px; border-radius: 12px; border: 1px solid var(--sd-border); margin-bottom: 8px; transition: background .15s; }
.sd-req-row:last-child { margin-bottom: 0; }
.sd-req-row:hover { background: #f8fafc; }
.sd-req-type { font-size: .86rem; font-weight: 700; color: var(--sd-navy); }
.sd-req-student { font-size: .76rem; color: var(--sd-muted); margin-top: 2px; }
.sd-req-dates { font-size: .72rem; color: #94a3b8; margin-top: 3px; }

.sd-deadline-item { padding: 12px 14px; border: 1px solid var(--sd-border); border-radius: 12px; margin-bottom: 10px; }
.sd-deadline-item:last-child { margin-bottom: 0; }
.sd-deadline-title { font-weight: 700; color: var(--sd-navy); font-size: .88rem; }
.sd-deadline-date  { font-size: .8rem; color: var(--sd-primary); margin: 3px 0; font-weight: 600; }
.sd-deadline-note  { font-size: .75rem; color: var(--sd-muted); }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════
     1. ANNOUNCEMENTS — first thing registrar sees
════════════════════════════════════════════════════ --}}
@if($announcements->isNotEmpty())
<div class="sd-announce-wrap">
  <div class="sd-announce-header">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#4f46e5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
    </svg>
    <h2>School Announcements</h2>
    <span class="sd-announce-count">{{ $announcements->count() }}</span>
  </div>
  <div class="sd-announce-list">
    @foreach($announcements->take(3) as $i => $ann)
    @php
      $p = $ann->priority ?? 'normal';
      $pClass = match($p) { 'urgent','high' => 'high', 'medium' => 'medium', default => 'low' };
      $pLabel = match($p) { 'urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', default => 'Notice' };
    @endphp
    <div class="sd-announce-item sd-announce-item--{{ $pClass }}" style="animation-delay:{{ $i * 0.08 }}s" data-ann>
      <div class="sd-announce-icon">
        @if($pClass === 'high')
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        @elseif($pClass === 'medium')
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        @else
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
        @endif
      </div>
      <div class="sd-announce-body">
        <div class="sd-announce-title">{{ $ann->title }}</div>
        <div class="sd-announce-msg">{{ $ann->message }}</div>
        <div class="sd-announce-date">{{ $ann->created_at->format('M d, Y') }}</div>
      </div>
      <span class="sd-priority-badge badge--{{ $pClass }}">{{ $pLabel }}</span>
      <button class="sd-dismiss-btn" onclick="this.closest('[data-ann]').style.display='none'" title="Dismiss">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     2. HERO — Welcome + quick pills
════════════════════════════════════════════════════ --}}
<div class="sd-hero">
  <div class="sd-hero__left" style="position:relative;z-index:1;">
    <h1>Welcome back, {{ $user->first_name }} 👋</h1>
    <p>{{ now()->format('l, F d, Y') }} &nbsp;·&nbsp; Registrar's Office Portal</p>
  </div>
  <div class="sd-hero__pills">
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
      Registrar Staff
    </div>
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ ucfirst($user->status ?? 'Active') }}
    </div>
    @if($stats['active_academic_year'])
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      {{ $stats['active_academic_year']->year_label }}
    </div>
    @endif
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/></svg>
      {{ $stats['pending_requests'] }} Pending
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     3. STAT STRIP
════════════════════════════════════════════════════ --}}
<div class="sd-stats">
  <div class="sd-stat" style="cursor:pointer;" onclick="window.location='{{ route('admin.students.index') }}'">
    <div class="sd-stat__icon si--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['pending_requests'] }}</div>
      <div class="sd-stat__label">Pending Requests</div>
    </div>
  </div>
  <div class="sd-stat" style="cursor:pointer;" onclick="window.location='{{ route('admin.audit.index') }}'">
    <div class="sd-stat__icon si--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['completed_requests'] }}</div>
      <div class="sd-stat__label">Completed Requests</div>
    </div>
  </div>
  <div class="sd-stat" style="cursor:pointer;" onclick="window.location='{{ route('admin.curriculum-mappings.index') }}'">
    <div class="sd-stat__icon si--orange">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['enrollment_verifications'] }}</div>
      <div class="sd-stat__label">Enrollment Verifications</div>
    </div>
  </div>
  <div class="sd-stat">
    <div class="sd-stat__icon si--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h8m-8 4h6M4 6h16M4 18h16"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['documents_in_review'] }}</div>
      <div class="sd-stat__label">Documents in Review</div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     4. MAIN 2-COLUMN GRID
════════════════════════════════════════════════════ --}}
<div class="sd-main-grid">

  {{-- LEFT COLUMN ────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Pending Requests --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Pending Requests</div>
        <a href="{{ route('admin.students.index') }}" class="enc-button enc-button--secondary enc-button--sm">View all</a>
      </div>
      <div class="sd-card__body">
        @forelse($pendingRequests as $req)
        <div class="sd-req-row">
          <div style="flex:1;min-width:0;">
            <div class="sd-req-type">{{ $req['type'] }}</div>
            <div class="sd-req-student">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;display:inline;margin-right:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
              {{ $req['student'] }}
            </div>
            <div class="sd-req-dates">Submitted {{ $req['submitted'] }} · Due {{ $req['due'] }}</div>
          </div>
          @php
            $statusPill = match(true) {
              str_contains($req['status'], 'Waiting') => 'pill--warning',
              str_contains($req['status'], 'Ready')   => 'pill--success',
              str_contains($req['status'], 'Pending') => 'pill--danger',
              default => 'pill--neutral',
            };
          @endphp
          <span class="sd-badge-pill {{ $statusPill }}" style="white-space:nowrap;flex-shrink:0;">{{ $req['status'] }}</span>
        </div>
        @empty
        <div style="text-align:center;padding:28px;color:#94a3b8;font-size:.85rem;font-weight:500;">No pending requests.</div>
        @endforelse
      </div>
    </div>

    {{-- Deadlines & Notices --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Important Deadlines</div>
        <span class="sd-card__meta">Registrar schedule</span>
      </div>
      <div class="sd-card__body">
        @foreach($deadlines as $dl)
        <div class="sd-deadline-item">
          <div class="sd-deadline-title">{{ $dl['title'] }}</div>
          <div class="sd-deadline-date">{{ $dl['date'] }}</div>
          <div class="sd-deadline-note">{{ $dl['note'] }}</div>
        </div>
        @endforeach

        <div style="margin-top:20px;">
          <div style="font-size:.82rem;font-weight:700;color:var(--sd-navy);margin-bottom:10px;text-transform:uppercase;letter-spacing:.05em;">Office Notices</div>
          @foreach($notices as $notice)
          @php
            $nPill = match($notice['priority']) { 'high' => 'pill--danger', 'medium' => 'pill--warning', default => 'pill--neutral' };
          @endphp
          <div class="sd-login-row" style="align-items:flex-start;">
            <span style="font-size:.84rem;color:var(--sd-navy);flex:1;line-height:1.45;">{{ $notice['message'] }}</span>
            <span class="sd-badge-pill {{ $nPill }}">{{ ucfirst($notice['priority']) }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>

  {{-- RIGHT COLUMN ────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Quick Actions --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Quick Actions</div>
        <span class="sd-card__meta">Registrar tools</span>
      </div>
      <div class="sd-card__body">
        <div class="sd-quick-grid">
          @foreach($quickLinks as $link)
          <a href="{{ route($link['route']) }}" class="sd-quick-btn">
            @php
              $iconStyles = [
                'Review Requests'         => ['bg'=>'rgba(79,70,229,.1)',  'color'=>'#4f46e5'],
                'Academic Calendar'       => ['bg'=>'rgba(16,185,129,.1)', 'color'=>'#10b981'],
                'Enrollment Verifications'=> ['bg'=>'rgba(245,158,11,.1)', 'color'=>'#f59e0b'],
                'Registrar Reports'       => ['bg'=>'rgba(239,68,68,.1)',  'color'=>'#ef4444'],
              ];
              $style = $iconStyles[$link['title']] ?? ['bg'=>'rgba(99,102,241,.1)', 'color'=>'#6366f1'];
            @endphp
            <div class="sd-quick-icon" style="background:{{ $style['bg'] }};color:{{ $style['color'] }};">
              @if($link['title'] === 'Review Requests')
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/></svg>
              @elseif($link['title'] === 'Academic Calendar')
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              @elseif($link['title'] === 'Enrollment Verifications')
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
              @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-4a3 3 0 013-3h3m-5 7h8m0 0a3 3 0 01-3 3H9m8-3V7"/></svg>
              @endif
            </div>
            <div class="sd-quick-label">{{ $link['title'] }}</div>
            <div class="sd-quick-desc">{{ $link['description'] }}</div>
          </a>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Office Status --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Office Status</div>
        <span class="sd-card__meta">Current term overview</span>
      </div>
      <div class="sd-card__body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          @php
            $panels = [
              ['label' => 'Academic Year', 'value' => $stats['active_academic_year'] ? $stats['active_academic_year']->year_label : 'N/A'],
              ['label' => 'Quarter',       'value' => $stats['active_quarter'] ? $stats['active_quarter']->quarter_name : 'N/A'],
              ['label' => 'Pending',       'value' => $stats['pending_requests'].' Requests'],
              ['label' => 'Completed',     'value' => $stats['completed_requests'].' Requests'],
            ];
          @endphp
          @foreach($panels as $p)
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:12px;">
            <div style="font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">{{ $p['label'] }}</div>
            <div style="font-size:.92rem;font-weight:700;color:var(--sd-navy);margin-top:4px;">{{ $p['value'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Recent Activities --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Recent Activities</div>
        <span class="sd-card__meta">Your latest work log</span>
      </div>
      <div class="sd-card__body">
        @if($recentActivities->count() > 0)
          @foreach($recentActivities->take(6) as $activity)
          <div class="sd-login-row" style="align-items:flex-start;">
            <div style="flex:1;min-width:0;">
              <div class="sd-login-type">{{ $activity->action_type }}</div>
              <div class="sd-login-time">{{ $activity->description ?? 'No description available' }}</div>
              <div style="font-size:.72rem;color:#94a3b8;margin-top:3px;">{{ $activity->created_at->diffForHumans() }}</div>
            </div>
          </div>
          @endforeach
        @else
          <div style="text-align:center;padding:28px;color:#94a3b8;font-size:.85rem;font-weight:500;">No recent activities.</div>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection
