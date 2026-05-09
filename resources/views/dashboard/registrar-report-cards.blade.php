@extends('layouts.app')
@section('title', 'Report Cards')
@section('breadcrumb', 'Report Cards')

@section('content')
<div style="max-width:960px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Report Cards</h1>
      <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Generate, review, and release student report cards by grading period.</p>
    </div>
  </div>

  <div class="sd-card" style="overflow:hidden;">
    <div style="background:linear-gradient(135deg,#1e3a8a,#312e81,#1e1b4b);padding:40px 32px;text-align:center;">
      <div style="width:60px;height:60px;border-radius:18px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;color:#fff;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
      </div>
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">Report Cards — Coming Soon</div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.75);max-width:440px;margin:0 auto;line-height:1.6;">
        The report card module is under development. You will be able to generate, preview, print, and distribute student report cards for each grading quarter from this page.
      </div>
    </div>
    <div style="padding:24px 32px;background:#f8fafc;">
      <div style="font-size:.8rem;font-weight:600;color:var(--sd-navy);margin-bottom:12px;">Planned features:</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        @foreach([
          'Report card generation per quarter',
          'Class & section batch printing',
          'Grade consolidation & review',
          'Parent/guardian release tracking',
          'Digital report card delivery',
          'Honors & recognition flagging',
          'Failed subject indicators',
          'Export to PDF / print layout',
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
