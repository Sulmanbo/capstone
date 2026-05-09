@extends('layouts.app')
@section('title', 'Document Requests')
@section('breadcrumb', 'Document Requests')

@push('head')
<style>
.req-filter-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.req-filter-btn { padding:.35rem .85rem; border:1px solid var(--sd-border); border-radius:999px; font-size:.78rem; font-weight:600; background:#fff; color:var(--sd-muted); cursor:pointer; transition:all .15s; }
.req-filter-btn.active, .req-filter-btn:hover { background:var(--sd-primary); color:#fff; border-color:var(--sd-primary); }
</style>
@endpush

@section('content')
<div style="max-width:960px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Document Requests</h1>
      <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Review and process student document and record requests.</p>
    </div>
  </div>

  <div class="req-filter-bar">
    <button class="req-filter-btn active">All Requests</button>
    <button class="req-filter-btn">Pending</button>
    <button class="req-filter-btn">Under Review</button>
    <button class="req-filter-btn">Ready for Release</button>
    <button class="req-filter-btn">Completed</button>
  </div>

  <div class="sd-card" style="overflow:hidden;">
    <div style="background:linear-gradient(135deg,#1e3a8a,#312e81,#1e1b4b);padding:40px 32px;text-align:center;">
      <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:#fff;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
      </div>
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">Document Requests — Coming Soon</div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.75);max-width:440px;margin:0 auto;line-height:1.6;">
        The document request queue is being built. You will be able to receive, track, approve, and release student-requested records from one place.
      </div>
    </div>
    <div style="padding:24px 32px;background:#f8fafc;">
      <div style="font-size:.8rem;font-weight:600;color:var(--sd-navy);margin-bottom:12px;">Planned features:</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach([
          'Transcript of records requests',
          'Enrollment certification issuance',
          'Good moral character certificates',
          'Clearance form processing',
          'Request status notifications',
          'Document release & claim tracking',
          'Digital signature & approval flow',
          'Request history & audit trail',
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
