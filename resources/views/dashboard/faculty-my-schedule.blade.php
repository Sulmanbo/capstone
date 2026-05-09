@extends('layouts.app')
@section('title', 'My Schedule')
@section('breadcrumb', 'My Schedule')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:24px;">
    <h1 style="font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 4px;">My Schedule</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">Your weekly teaching schedule at a glance.</p>
  </div>

  @php
    $dayOrder = ['monday','tuesday','wednesday','thursday','friday','saturday'];
    $dayLabels = ['monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday'];
    $grouped = [];
    foreach ($allSchedules as $s) {
      foreach ($s->days ?? [] as $d) {
        $grouped[$d][] = $s;
      }
    }
  @endphp

  @if($allSchedules->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:60px 24px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:#94a3b8;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
    </div>
    <div style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:6px;">No schedule assigned yet</div>
    <div style="font-size:.82rem;color:#94a3b8;">Your schedule will appear here once the admin assigns classes to you.</div>
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($dayOrder as $day)
    @php $classes = $grouped[$day] ?? []; @endphp
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;
                @if($day === $todayName) border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.08); @endif">
      <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;
                  @if($day === $todayName) background:#eef2ff; @else background:#f8fafc; @endif">
        <div style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
                    color:@if($day === $todayName)#4338ca @else #94a3b8 @endif;width:100px;flex-shrink:0;">
          {{ $dayLabels[$day] }}
        </div>
        @if($day === $todayName)
          <span style="font-size:.65rem;font-weight:700;padding:.15rem .55rem;border-radius:20px;background:#6366f1;color:#fff;">TODAY</span>
        @endif
        @if(empty($classes))
          <span style="font-size:.78rem;color:#cbd5e1;">No classes</span>
        @else
          <span style="font-size:.78rem;color:#64748b;">{{ count($classes) }} {{ Str::plural('class', count($classes)) }}</span>
        @endif
      </div>
      @if(!empty($classes))
      <div style="padding:4px 0;">
        @foreach(collect($classes)->sortBy('start_time') as $cls)
        <div style="display:flex;align-items:center;gap:16px;padding:10px 20px;border-bottom:1px solid #f8fafc;">
          <div style="font-size:.78rem;color:#64748b;white-space:nowrap;width:120px;flex-shrink:0;">{{ $cls->time_range }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.875rem;font-weight:600;color:#0f172a;">{{ $cls->subject_name }}</div>
            @if($cls->section)
            <div style="font-size:.75rem;color:#94a3b8;margin-top:1px;">{{ $cls->section_name }}</div>
            @endif
          </div>
          @if($cls->room)
          <div style="font-size:.75rem;font-weight:600;padding:.2rem .65rem;background:#f1f5f9;color:#475569;border-radius:6px;flex-shrink:0;">
            {{ $cls->room }}
          </div>
          @endif
        </div>
        @endforeach
      </div>
      @endif
    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection
