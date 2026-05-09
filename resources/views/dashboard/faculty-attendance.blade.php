@extends('layouts.app')
@section('title', 'Attendance')
@section('breadcrumb', 'Attendance')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Attendance</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">Track and record daily student attendance per class.</p>
  </div>

  @if($allSchedules->isNotEmpty())
  <div style="margin-bottom:20px;">
    <label style="display:block;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#374151;margin-bottom:8px;">Select Class</label>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
      @foreach($allSchedules as $sched)
      <button style="padding:.45rem 1rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:.845rem;font-weight:500;color:#374151;cursor:pointer;">
        {{ $sched->subject_name }} – {{ $sched->section_name ?? 'No Section' }}
      </button>
      @endforeach
    </div>
  </div>
  @endif

  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">
    <div style="background:linear-gradient(135deg,#064e3b,#065f46);padding:40px 32px;text-align:center;">
      <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:#fff;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
      </div>
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">Attendance Tracking — Coming Soon</div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.7);max-width:420px;margin:0 auto;line-height:1.6;">
        The attendance module is under development. Mark present, absent, or late for each student directly from this page.
      </div>
    </div>
    <div style="padding:24px 32px;background:#f8fafc;">
      <div style="font-size:.8rem;font-weight:600;color:#374151;margin-bottom:12px;">Planned features:</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach(['Daily attendance per class','Present / Absent / Late / Excused statuses','Monthly attendance summary','Auto-flag students below threshold','Export attendance reports','Integration with grade computation'] as $feat)
        <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:#64748b;">
          <div style="width:6px;height:6px;border-radius:50%;background:#10b981;flex-shrink:0;"></div>
          {{ $feat }}
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection
