@extends('layouts.app')
@section('title', 'Enrollment')
@section('breadcrumb', 'Enrollment')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Enrollment</h1>
    <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Manage student enrollment status for the current academic year.</p>
  </div>

  @if($activeAcademicYear)
  <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:999px;padding:.35rem 1rem;margin-bottom:20px;">
    <div style="width:7px;height:7px;border-radius:50%;background:#10b981;"></div>
    <span style="font-size:.8rem;font-weight:700;color:#059669;">Active Year: {{ $activeAcademicYear->year_label }}</span>
  </div>
  @endif

  <div class="sd-card" style="overflow:hidden;margin-bottom:16px;">
    <div style="background:linear-gradient(135deg,#1e3a8a,#312e81,#1e1b4b);padding:40px 32px;text-align:center;">
      <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:#fff;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
        </svg>
      </div>
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">Enrollment Management — Coming Soon</div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.75);max-width:440px;margin:0 auto;line-height:1.6;">
        The enrollment module is being developed. You will be able to process new enrollments, update student statuses, and generate enrollment certifications from this page.
      </div>
    </div>
    <div style="padding:24px 32px;background:#f8fafc;">
      <div style="font-size:.8rem;font-weight:600;color:var(--sd-navy);margin-bottom:12px;">Planned features:</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach([
          'New student enrollment intake',
          'Enrollment status tracking',
          'Enrollment certification printing',
          'Bulk enrollment updates',
          'Transfer student processing',
          'Enrollment period configuration',
          'Section assignment management',
          'Enrollment analytics & reports',
        ] as $feat)
        <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--sd-muted);">
          <div style="width:6px;height:6px;border-radius:50%;background:var(--sd-primary);flex-shrink:0;"></div>
          {{ $feat }}
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection
