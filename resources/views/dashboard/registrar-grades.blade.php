@extends('layouts.app')
@section('title', 'Grades & Records')
@section('breadcrumb', 'Grades & Records')

@push('head')
<style>
.gr-tab-bar { display:flex; gap:4px; border-bottom:2px solid var(--sd-border); margin-bottom:20px; }
.gr-tab { padding:.5rem 1.1rem; font-size:.82rem; font-weight:600; color:var(--sd-muted); border-bottom:2px solid transparent; margin-bottom:-2px; cursor:pointer; transition:color .15s,border-color .15s; }
.gr-tab.active { color:var(--sd-primary); border-bottom-color:var(--sd-primary); }
</style>
@endpush

@section('content')
<div style="max-width:960px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Grades & Records</h1>
      <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Access and verify official academic grade records for all students.</p>
    </div>
  </div>

  <div class="gr-tab-bar">
    <div class="gr-tab active">Grade Verification</div>
    <div class="gr-tab">Grade Submission</div>
    <div class="gr-tab">Academic Standing</div>
    <div class="gr-tab">Transcripts</div>
  </div>

  <div class="sd-card" style="overflow:hidden;">
    <div style="background:linear-gradient(135deg,#1e3a8a,#312e81,#1e1b4b);padding:40px 32px;text-align:center;">
      <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:#fff;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
      </div>
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">Grades & Records — Coming Soon</div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.75);max-width:440px;margin:0 auto;line-height:1.6;">
        The grades and records module is under development. Registrar staff will be able to verify, lock, and manage official grade records, and generate transcript of records from this page.
      </div>
    </div>
    <div style="padding:24px 32px;background:#f8fafc;">
      <div style="font-size:.8rem;font-weight:600;color:var(--sd-navy);margin-bottom:12px;">Planned features:</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach([
          'Official grade verification',
          'Teacher grade submission review',
          'Grade locking per quarter',
          'Academic standing computation',
          'Transcript of records generation',
          'Incomplete & failing grade flags',
          'Grade correction request handling',
          'Grade history audit trail',
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
