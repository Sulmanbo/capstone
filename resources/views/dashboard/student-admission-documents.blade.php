@extends('layouts.app')

@section('title', 'Admission Documents')
@section('breadcrumb', 'Admission Documents')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Admission Documents</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — Document Requirements</p>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Required Documents</div>
    <span class="enc-card__meta">Submission status for enrollment requirements</span>
  </div>
  <div class="enc-card__body">
    @if(empty($documents))
      <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.25)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        <p style="font-size:.95rem;font-weight:600;">No Documents on Record</p>
        <p style="font-size:.82rem;margin-top:.25rem;">Admission document tracking will appear here once records are available.</p>
      </div>
    @else
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
        @foreach($documents as $doc)
        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:1rem;display:flex;gap:.75rem;align-items:flex-start;">
          <div style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $doc['submitted'] ? 'rgba(34,197,94,.15)' : 'rgba(239,68,68,.15)' }};">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;color:{{ $doc['submitted'] ? '#4ade80' : '#f87171' }}">
              @if($doc['submitted'])
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
              @endif
            </svg>
          </div>
          <div>
            <div style="font-size:.9rem;font-weight:600;color:#fff;">{{ $doc['name'] }}</div>
            <div style="font-size:.8rem;color:rgba(255,255,255,.5);margin-top:.2rem;">{{ $doc['submitted'] ? 'Submitted' : 'Not yet submitted' }}</div>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
