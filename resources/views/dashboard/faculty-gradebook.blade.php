@extends('layouts.app')
@section('title', 'Gradebook')
@section('breadcrumb', 'Gradebook')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Gradebook</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">Enter and manage grades for your classes each grading period.</p>
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
    {{-- Coming soon banner --}}
    <div style="background:linear-gradient(135deg,#1e3a5f,#2d5fa8);padding:40px 32px;text-align:center;">
      <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:#fff;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
        </svg>
      </div>
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">Gradebook — Coming Soon</div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.7);max-width:420px;margin:0 auto;line-height:1.6;">
        The grade entry module is under development. You'll be able to input quarterly grades per student, per subject directly from this page.
      </div>
    </div>
    <div style="padding:24px 32px;background:#f8fafc;">
      <div style="font-size:.8rem;font-weight:600;color:#374151;margin-bottom:12px;">Planned features:</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach(['Per-student grade entry by quarter','Automatic average computation','Grade submission & locking','Class performance summary','Export to Excel / PDF','Incomplete grade flagging'] as $feat)
        <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:#64748b;">
          <div style="width:6px;height:6px;border-radius:50%;background:#6366f1;flex-shrink:0;"></div>
          {{ $feat }}
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection
