@extends('layouts.app')
@section('title', 'Academic Calendar')
@section('breadcrumb', 'Academic Calendar')

@push('head')
<style>
.cal-year { margin-bottom:24px; }
.cal-year-header { display:flex; align-items:center; gap:12px; padding:16px 20px; background:var(--sd-card); border:1px solid var(--sd-border); border-radius:var(--sd-radius) var(--sd-radius) 0 0; border-bottom:none; }
.cal-year-label { font-size:1rem; font-weight:800; color:var(--sd-navy); flex:1; }
.cal-active-badge { background:rgba(16,185,129,.1); color:#059669; font-size:.7rem; font-weight:700; padding:.18rem .6rem; border-radius:999px; }
.cal-year-status { font-size:.75rem; color:var(--sd-muted); }
.cal-quarters { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:0; border:1px solid var(--sd-border); border-radius:0 0 var(--sd-radius) var(--sd-radius); overflow:hidden; background:var(--sd-card); }
.cal-quarter { padding:16px 18px; border-right:1px solid #f1f5f9; }
.cal-quarter:last-child { border-right:none; }
.cal-quarter-name { font-size:.82rem; font-weight:700; color:var(--sd-navy); margin-bottom:6px; }
.cal-quarter-dates { font-size:.75rem; color:var(--sd-muted); line-height:1.6; }
.cal-quarter-status { margin-top:8px; display:inline-block; font-size:.68rem; font-weight:700; padding:.15rem .55rem; border-radius:999px; }
.qs--active   { background:rgba(79,70,229,.1); color:#4338ca; }
.qs--upcoming { background:rgba(245,158,11,.1); color:#d97706; }
.qs--closed   { background:#f1f5f9; color:#94a3b8; }
.cal-empty { text-align:center; padding:24px; color:var(--sd-muted); font-size:.82rem; border:1px solid var(--sd-border); border-top:none; border-radius:0 0 var(--sd-radius) var(--sd-radius); background:#fafafa; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <div>
    <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 3px;">Academic Calendar</h1>
    <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Academic years and grading quarters</p>
  </div>
  @if($activeYear)
  <div style="font-size:.82rem;color:var(--sd-muted);background:#f1f5f9;padding:.35rem .9rem;border-radius:999px;font-weight:600;">
    Active: {{ $activeYear->year_label }}
  </div>
  @endif
</div>

@if($academicYears->isEmpty())
  <div class="sd-card">
    <div class="sd-card__body" style="text-align:center;padding:56px 24px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
      <div style="font-weight:600;font-size:.9rem;color:var(--sd-navy);">No academic years on record</div>
      <div style="font-size:.8rem;color:var(--sd-muted);margin-top:4px;">Contact the administrator to set up the academic calendar.</div>
    </div>
  </div>
@else
  @foreach($academicYears as $year)
  <div class="cal-year">
    <div class="cal-year-header">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;color:var(--sd-primary);flex-shrink:0;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
      <span class="cal-year-label">{{ $year->year_label ?? 'Academic Year '.$year->id }}</span>
      @if($year->status === 'active')
        <span class="cal-active-badge">Active</span>
      @else
        <span class="cal-year-status">{{ ucfirst($year->status ?? 'closed') }}</span>
      @endif
    </div>

    @if($year->quarters->isEmpty())
      <div class="cal-empty">No grading quarters defined for this year.</div>
    @else
      <div class="cal-quarters">
        @foreach($year->quarters->sortBy('id') as $q)
        <div class="cal-quarter">
          <div class="cal-quarter-name">{{ $q->quarter_name }}</div>
          <div class="cal-quarter-dates">
            @if($q->start_date)
              <div>Start: {{ \Carbon\Carbon::parse($q->start_date)->format('M d, Y') }}</div>
            @endif
            @if($q->end_date)
              <div>End: &nbsp; {{ \Carbon\Carbon::parse($q->end_date)->format('M d, Y') }}</div>
            @endif
          </div>
          @php
            $qStatus = $q->status ?? 'closed';
            $qClass  = match($qStatus) { 'active' => 'qs--active', 'upcoming' => 'qs--upcoming', default => 'qs--closed' };
          @endphp
          <span class="cal-quarter-status {{ $qClass }}">{{ ucfirst($qStatus) }}</span>
        </div>
        @endforeach
      </div>
    @endif
  </div>
  @endforeach
@endif

@endsection
