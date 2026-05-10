@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('breadcrumb', 'Student Dashboard')

@push('head')
<style>
/* ── Student-specific components ─ */
.sd-subject-row { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 12px; border: 1px solid var(--sd-border); margin-bottom: 8px; transition: background .15s; }
.sd-subject-row:last-child { margin-bottom: 0; }
.sd-subject-row:hover { background: #f8fafc; }
.sd-subject-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.sd-subject-name { flex: 1; font-weight: 600; color: var(--sd-navy); font-size: .88rem; }
.sd-subject-code { font-size: .75rem; color: var(--sd-muted); margin-top: 2px; }
.sd-subject-grade { font-size: 1.05rem; font-weight: 800; }
.sd-subject-status { font-size: .82rem; color: var(--sd-muted); text-align: right; margin-top: 1px; }
.g--excellent { color: var(--sd-success); }
.g--good      { color: #3b82f6; }
.g--fair      { color: var(--sd-warning); }
.g--poor      { color: var(--sd-danger); }

.sd-progress-item { margin-bottom: 16px; }
.sd-progress-item:last-child { margin-bottom: 0; }
.sd-progress-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
.sd-progress-label { font-size: .85rem; font-weight: 600; color: var(--sd-navy); }
.sd-progress-score { font-size: 1rem; font-weight: 800; color: var(--sd-navy); }
.sd-bar-track { height: 8px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
.sd-bar-fill  { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #4f46e5, #10b981); transition: width .6s cubic-bezier(.22,1,.36,1); }

.sd-assign-item { display: flex; align-items: flex-start; gap: 10px; padding: 11px 13px; border-radius: 12px; border: 1px solid var(--sd-border); margin-bottom: 8px; transition: background .15s; }
.sd-assign-item:last-child { margin-bottom: 0; }
.sd-assign-item:hover { background: #f8fafc; }
.sd-assign-priority { width: 6px; border-radius: 3px; align-self: stretch; flex-shrink: 0; min-height: 38px; }
.p--high   { background: var(--sd-danger);  }
.p--medium { background: var(--sd-warning); }
.p--low    { background: var(--sd-primary); }
.sd-assign-info { flex: 1; min-width: 0; }
.sd-assign-title { font-size: .86rem; font-weight: 700; color: var(--sd-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sd-assign-sub   { font-size: .75rem; color: var(--sd-muted); margin-top: 2px; }
.sd-assign-due   { font-size: .72rem; color: #94a3b8; white-space: nowrap; padding-top: 2px; }

.sd-report-banner { background: linear-gradient(135deg, #eff6ff, #f0fdf4); border: 1px solid rgba(79,70,229,.12); border-radius: 14px; padding: 18px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
.sd-report-gpa { font-size: 2.2rem; font-weight: 900; color: var(--sd-primary); line-height: 1; }
.sd-report-label { font-size: .75rem; color: var(--sd-muted); margin-top: 3px; }
.sd-report-actions { display: flex; gap: 8px; flex-wrap: wrap; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════
     1. ANNOUNCEMENTS — first thing students see
════════════════════════════════════════════════════ --}}
<div class="sd-announce-wrap">
  <div class="sd-announce-header">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#4f46e5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
    </svg>
    <h2>School Announcements</h2>
    <span class="sd-announce-count">{{ count($announcements) }}</span>
  </div>

  <div class="sd-announce-list">
    @foreach($announcements as $i => $ann)
    <div class="sd-announce-item sd-announce-item--{{ $ann['priority'] }}" style="animation-delay: {{ $i * 0.08 }}s" data-ann>
      <div class="sd-announce-icon">
        @if($ann['priority'] === 'high')
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        @elseif($ann['priority'] === 'medium')
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
          </svg>
        @else
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/>
          </svg>
        @endif
      </div>
      <div class="sd-announce-body">
        <div class="sd-announce-title">{{ $ann['title'] }}</div>
        <div class="sd-announce-msg">{{ $ann['message'] }}</div>
        <div class="sd-announce-date">{{ $ann['date'] }}</div>
      </div>
      <span class="sd-priority-badge badge--{{ $ann['priority'] }}">{{ ucfirst($ann['priority']) }}</span>
      <button class="sd-dismiss-btn" onclick="this.closest('[data-ann]').style.display='none'" title="Dismiss">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    @endforeach
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     2. HERO — Welcome + quick pills
════════════════════════════════════════════════════ --}}
<div class="sd-hero">
  <div class="sd-hero__left">
    <h1>Welcome back, {{ auth()->user()->first_name }} 👋</h1>
    <p>{{ now()->format('l, F d, Y') }} &nbsp;·&nbsp; Student Portal</p>
  </div>
  <div class="sd-hero__pills">
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      {{ $studentInfo['grade_level'] }}
    </div>
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
      Section {{ $studentInfo['section'] ?? 'N/A' }}
    </div>
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v10a2 2 0 002 2h5m0 0h5a2 2 0 002-2V8a2 2 0 00-2-2h-5m0 0V5a2 2 0 10-4 0v1m4 0a2 2 0 104 0v-1"/>
      </svg>
      LRN: {{ $studentInfo['lrn'] ?? 'N/A' }}
    </div>
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Standing: {{ $stats['standing'] }}
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     3. STAT STRIP
════════════════════════════════════════════════════ --}}
<div class="sd-stats">
  <div class="sd-stat">
    <div class="sd-stat__icon si--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['gpa'] }}</div>
      <div class="sd-stat__label">Current GPA</div>
    </div>
  </div>

  <div class="sd-stat">
    <div class="sd-stat__icon si--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['attendance_rate'] }}</div>
      <div class="sd-stat__label">Attendance Rate</div>
    </div>
  </div>

  <div class="sd-stat">
    <div class="sd-stat__icon si--orange">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['total_subjects'] }}</div>
      <div class="sd-stat__label">Subjects Enrolled</div>
    </div>
  </div>

  <div class="sd-stat">
    <div class="sd-stat__icon si--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $stats['active_quarter'] ? $stats['active_quarter']->quarter_name : 'N/A' }}</div>
      <div class="sd-stat__label">Current Quarter</div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     4. MAIN 2-COLUMN GRID
════════════════════════════════════════════════════ --}}
<div class="sd-main-grid">

  {{-- LEFT COLUMN ──────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Subjects & Grades --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Current Subjects &amp; Grades</div>
        <span class="sd-card__meta">This term's performance</span>
      </div>
      <div class="sd-card__body">
        @php
          $dots = ['#4f46e5','#10b981','#f59e0b','#ef4444','#06b6d4','#8b5cf6'];
        @endphp
        @foreach($currentSubjects as $idx => $subject)
          @php
            $g = (int)$subject['grade'];
            $gClass = $g >= 90 ? 'g--excellent' : ($g >= 85 ? 'g--good' : ($g >= 75 ? 'g--fair' : 'g--poor'));
          @endphp
          <div class="sd-subject-row">
            <div class="sd-subject-dot" style="background:{{ $dots[$idx % count($dots)] }}"></div>
            <div style="flex:1;min-width:0;">
              <div class="sd-subject-name">{{ $subject['name'] }}</div>
              <div class="sd-subject-code">{{ $subject['code'] }}</div>
            </div>
            <div style="text-align:right;">
              <div class="sd-subject-grade {{ $gClass }}">{{ $subject['grade'] }}%</div>
              <div class="sd-subject-status">{{ $subject['status'] }}</div>
            </div>
          </div>
        @endforeach
        <div style="margin-top:.85rem;padding-top:.75rem;border-top:1px solid rgba(15,23,42,.06);text-align:right;">
          <a href="{{ route('complaints.create') }}" style="font-size:.78rem;color:var(--primary);font-weight:700;text-decoration:none;">File a Grade Complaint →</a>
        </div>
      </div>
    </div>

    {{-- Assessment Breakdown --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Assessment Breakdown</div>
        <span class="sd-card__meta">Midterms · Quizzes · Projects</span>
      </div>
      <div class="sd-card__body">
        @foreach($assessmentBreakdown as $item)
          <div class="sd-progress-item">
            <div class="sd-progress-top">
              <div>
                <div class="sd-progress-label">{{ $item['label'] }}</div>
                <div style="font-size:.74rem;color:#94a3b8;">{{ $item['description'] }}</div>
              </div>
              <div class="sd-progress-score">{{ $item['score'] }}%</div>
            </div>
            <div class="sd-bar-track">
              <div class="sd-bar-fill" style="width:{{ $item['score'] }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Report Card --}}
    @foreach($reportCards as $report)
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Official Report Card</div>
        <span class="sd-card__meta">{{ $report['term'] }} · {{ $report['year'] }}</span>
      </div>
      <div class="sd-card__body">
        <div class="sd-report-banner">
          <div>
            <div class="sd-report-gpa">{{ $report['gpa'] }}</div>
            <div class="sd-report-label">Grade Point Average</div>
            <div style="font-size:.75rem;color:#94a3b8;margin-top:4px;">{{ $report['remarks'] }}</div>
          </div>
          <div>
            <span style="display:inline-block;margin-bottom:10px;font-size:.74rem;font-weight:700;padding:.25rem .7rem;border-radius:999px;background:rgba(16,185,129,.1);color:#059669;">{{ $report['status'] }}</span>
            <div class="sd-report-actions">
              <a href="{{ route('student.report-card') }}" class="enc-button enc-button--primary enc-button--sm">View Full Report</a>
              <button type="button" class="enc-button enc-button--secondary enc-button--sm" onclick="alert('Download coming soon.')">Download PDF</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach

  </div>

  {{-- RIGHT COLUMN ─────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Today's Schedule --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Today's Schedule</div>
        <span class="sd-card__meta">{{ now()->format('l') }}</span>
      </div>
      <div class="sd-card__body">
        @foreach($todaySchedule as $class)
          <div class="sd-schedule-item">
            <div class="sd-schedule-time">{{ $class['time'] }}</div>
            <div class="sd-schedule-dot-col">
              <div class="sd-schedule-dot"></div>
              <div class="sd-schedule-line"></div>
            </div>
            <div class="sd-schedule-info">
              <div class="sd-schedule-subj">{{ $class['subject'] }}</div>
              <div class="sd-schedule-detail">{{ $class['teacher'] }}</div>
            </div>
            <div class="sd-schedule-room">{{ $class['room'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Upcoming Assignments --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Upcoming Assignments</div>
        <span class="sd-card__meta">Due soon</span>
      </div>
      <div class="sd-card__body">
        @foreach($upcomingAssignments as $assignment)
          <div class="sd-assign-item">
            <div class="sd-assign-priority p--{{ $assignment['priority'] }}"></div>
            <div class="sd-assign-info">
              <div class="sd-assign-title">{{ $assignment['title'] }}</div>
              <div class="sd-assign-sub">{{ $assignment['subject'] }}</div>
            </div>
            <div class="sd-assign-due">Due {{ $assignment['due_date'] }}</div>
          </div>
        @endforeach
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
          <a href="{{ route('password.force-reset') }}" class="enc-button enc-button--primary enc-button--sm">Update</a>
        </div>

        @if($recentLogins->isEmpty())
          <div style="text-align:center;padding:1rem;color:#94a3b8;font-size:.83rem;">No login activity available yet.</div>
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

    {{-- Academic Standing --}}
    <div class="sd-card">
      <div class="sd-card__head">
        <div class="sd-card__title">Academic Standing</div>
        <span class="sd-card__meta">Current term overview</span>
      </div>
      <div class="sd-card__body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          @php
            $panels = [
              ['label' => 'Academic Year', 'value' => $stats['active_academic_year'] ? $stats['active_academic_year']->year_label : 'N/A'],
              ['label' => 'Quarter',       'value' => $stats['active_quarter'] ? $stats['active_quarter']->quarter_name : 'N/A'],
              ['label' => 'Standing',      'value' => $stats['standing']],
              ['label' => 'Subjects',      'value' => $stats['total_subjects']],
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

  </div>
</div>

@endsection

@push('scripts')
<script>
function downloadReportCard() {
  alert('Report card download is coming soon.');
}
</script>
@endpush
