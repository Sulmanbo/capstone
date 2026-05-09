@extends('layouts.app')
@section('title', 'Faculty Dashboard')
@section('breadcrumb', 'Faculty Dashboard')

{{-- All sd-* styles are loaded from layouts/app.blade.php --}}

@section('content')

{{-- ═══════════════════════════════════════════════════
     1. ANNOUNCEMENTS — first thing faculty sees
════════════════════════════════════════════════════ --}}
@if($announcements->isNotEmpty())
<div class="sd-announce-wrap">
  <div class="sd-announce-header">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#4f46e5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
    </svg>
    <h2>School Announcements</h2>
    <span class="sd-announce-count">{{ $announcements->count() }}</span>
    <a href="{{ route('faculty.announcements') }}" class="enc-button enc-button--secondary enc-button--sm" style="margin-left:auto;">View all</a>
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
    <p>{{ now()->format('l, F d, Y') }} &nbsp;·&nbsp; Faculty Portal</p>
  </div>
  <div class="sd-hero__pills">
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
      ID: {{ $user->employee_number ?? 'N/A' }}
    </div>
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ ucfirst($user->status) }}
    </div>
    @if($activeAcademicYear)
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      {{ $activeAcademicYear->year_label }}
    </div>
    @endif
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
      {{ $allSchedules->count() }} {{ Str::plural('Section', $allSchedules->count()) }}
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     3. STAT STRIP
════════════════════════════════════════════════════ --}}
<div class="sd-stats">
  <div class="sd-stat">
    <div class="sd-stat__icon si--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $todayClasses->count() }}</div>
      <div class="sd-stat__label">Classes Today</div>
    </div>
  </div>
  <div class="sd-stat">
    <div class="sd-stat__icon si--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $allSchedules->count() }}</div>
      <div class="sd-stat__label">Total Sections</div>
    </div>
  </div>
  <div class="sd-stat">
    <div class="sd-stat__icon si--orange">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $activeQuarter ? $activeQuarter->quarter_name : '—' }}</div>
      <div class="sd-stat__label">Current Quarter</div>
    </div>
  </div>
  <div class="sd-stat">
    <div class="sd-stat__icon si--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $announcements->count() }}</div>
      <div class="sd-stat__label">Announcements</div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     4. MAIN 2-COLUMN GRID
════════════════════════════════════════════════════ --}}
<div class="sd-main-grid">

  {{-- LEFT COLUMN ────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Today's Classes --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Today's Classes</div>
        <span class="sd-card__meta">{{ now()->format('l') }}</span>
      </div>
      <div class="sd-card__body">
        @if($todayClasses->isEmpty())
          <div style="text-align:center;padding:28px;color:#94a3b8;font-size:.85rem;font-weight:500;">No classes scheduled for today.</div>
        @else
          @foreach($todayClasses->sortBy('start_time') as $cls)
          <div class="sd-schedule-item">
            <div class="sd-schedule-time">{{ date('g:i A', strtotime($cls->start_time)) }}</div>
            <div class="sd-schedule-dot-col">
              <div class="sd-schedule-dot"></div>
              <div class="sd-schedule-line"></div>
            </div>
            <div class="sd-schedule-info">
              <div class="sd-schedule-subj">{{ $cls->subject_name }}</div>
              <div class="sd-schedule-detail">{{ $cls->section_name ?? '—' }} &nbsp;·&nbsp; {{ date('g:i A', strtotime($cls->start_time)) }}–{{ date('g:i A', strtotime($cls->end_time)) }}</div>
            </div>
            @if($cls->room)
            <div class="sd-schedule-room">{{ $cls->room }}</div>
            @endif
          </div>
          @endforeach
        @endif
      </div>
    </div>

    {{-- Teaching Load --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">My Teaching Load</div>
        <a href="{{ route('faculty.classes') }}" class="enc-button enc-button--secondary enc-button--sm">View all</a>
      </div>
      <div class="sd-card__body">
        @if($allSchedules->isEmpty())
          <div style="text-align:center;padding:28px;color:#94a3b8;font-size:.85rem;font-weight:500;">No classes assigned yet.</div>
        @else
          @foreach($allSchedules->take(5) as $sched)
          <div class="sd-login-row">
            <div>
              <div class="sd-login-type">{{ $sched->subject_name }}</div>
              <div class="sd-login-time">{{ $sched->section_name ?? '—' }} &nbsp;·&nbsp; {{ $sched->days_label }} &nbsp;·&nbsp; {{ $sched->time_range }}</div>
            </div>
            @if($sched->room)
            <span class="sd-badge-pill pill--neutral">{{ $sched->room }}</span>
            @endif
          </div>
          @endforeach
        @endif
      </div>
    </div>

  </div>

  {{-- RIGHT COLUMN ────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Quick Actions --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Quick Actions</div>
      </div>
      <div class="sd-card__body">
        <div class="sd-quick-grid">
          <a href="{{ route('faculty.classes') }}" class="sd-quick-btn">
            <div class="sd-quick-icon" style="background:rgba(79,70,229,.1);color:#4f46e5;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
            </div>
            <div class="sd-quick-label">Teaching Load</div>
            <div class="sd-quick-desc">All my classes</div>
          </a>
          <a href="{{ route('faculty.gradebook') }}" class="sd-quick-btn">
            <div class="sd-quick-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
            </div>
            <div class="sd-quick-label">Gradebook</div>
            <div class="sd-quick-desc">Enter grades</div>
          </a>
          <a href="{{ route('faculty.attendance') }}" class="sd-quick-btn">
            <div class="sd-quick-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            </div>
            <div class="sd-quick-label">Attendance</div>
            <div class="sd-quick-desc">Mark present/absent</div>
          </a>
          <a href="{{ route('faculty.my-schedule') }}" class="sd-quick-btn">
            <div class="sd-quick-icon" style="background:rgba(239,68,68,.1);color:#ef4444;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
            </div>
            <div class="sd-quick-label">My Schedule</div>
            <div class="sd-quick-desc">Weekly timetable</div>
          </a>
          <a href="{{ route('faculty.announcements') }}" class="sd-quick-btn">
            <div class="sd-quick-icon" style="background:rgba(6,182,212,.1);color:#0891b2;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
            </div>
            <div class="sd-quick-label">Announcements</div>
            <div class="sd-quick-desc">School notices</div>
          </a>
          <a href="{{ route('faculty.settings.index') }}" class="sd-quick-btn">
            <div class="sd-quick-icon" style="background:rgba(139,92,246,.1);color:#7c3aed;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="sd-quick-label">Settings</div>
            <div class="sd-quick-desc">Preferences</div>
          </a>
        </div>
      </div>
    </div>

    {{-- Academic Status --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Academic Status</div>
        <span class="sd-card__meta">Current term overview</span>
      </div>
      <div class="sd-card__body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          @php
            $panels = [
              ['label' => 'Academic Year', 'value' => $activeAcademicYear ? $activeAcademicYear->year_label : 'N/A'],
              ['label' => 'Quarter',       'value' => $activeQuarter ? $activeQuarter->quarter_name : 'N/A'],
              ['label' => 'Teaching Load', 'value' => $allSchedules->count().' '.Str::plural('Section', $allSchedules->count())],
              ['label' => 'Today',         'value' => $todayClasses->count().' '.Str::plural('Class', $todayClasses->count())],
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

    {{-- Security Hub --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Security Hub</div>
        <span class="sd-card__meta">Login activity</span>
      </div>
      <div class="sd-card__body">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px;padding:12px;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
          <div>
            <div style="font-size:.85rem;font-weight:700;color:var(--sd-navy);">Keep your account safe</div>
            <div style="font-size:.75rem;color:#94a3b8;margin-top:2px;">Review and update your password</div>
          </div>
          <a href="{{ route('faculty.settings.index') }}" class="enc-button enc-button--primary enc-button--sm">Update</a>
        </div>
        @if($recentLogins->isEmpty())
          <div style="text-align:center;padding:1rem;color:#94a3b8;font-size:.83rem;">No login activity yet.</div>
        @else
          @foreach($recentLogins as $activity)
          <div class="sd-login-row">
            <div>
              <div class="sd-login-type">{{ $activity->action_type === \App\Models\AuditLog::LOGIN_SUCCESS ? 'Successful login' : 'Failed login attempt' }}</div>
              <div class="sd-login-time">{{ $activity->created_at->format('M d, Y • h:i A') }}</div>
            </div>
            <span class="sd-badge-pill {{ $activity->action_type === \App\Models\AuditLog::LOGIN_SUCCESS ? 'pill--success' : 'pill--danger' }}">
              {{ $activity->action_type === \App\Models\AuditLog::LOGIN_SUCCESS ? 'Success' : 'Failed' }}
            </span>
          </div>
          @endforeach
        @endif
      </div>
    </div>

  </div>
</div>

@endsection