@extends('layouts.app')
@section('title', 'Gradebook')
@section('breadcrumb', 'Gradebook')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Gradebook</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">Select a class below to enter or review grades for the current grading period.</p>
  </div>

  @if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#166534;">
    {{ session('success') }}
  </div>
  @endif

  @if($allSchedules->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:48px 32px;text-align:center;">
    <div style="font-size:1rem;font-weight:700;color:#374151;margin-bottom:8px;">No Classes Assigned</div>
    <div style="font-size:.875rem;color:#94a3b8;">You have no classes assigned for the active academic year.</div>
  </div>
  @else
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @foreach($allSchedules as $sched)
    <a href="{{ route('faculty.gradebook.show', $sched->id) }}"
       style="display:block;text-decoration:none;background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px 22px;transition:box-shadow .15s,border-color .15s;"
       onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.borderColor='#6366f1';"
       onmouseout="this.style.boxShadow='none';this.style.borderColor='#e5e7eb';">

      {{-- Subject & Section --}}
      <div style="font-size:.93rem;font-weight:700;color:#0f172a;margin-bottom:4px;line-height:1.3;">
        {{ $sched->subject_name ?? '—' }}
      </div>
      <div style="font-size:.8rem;color:#6366f1;font-weight:600;margin-bottom:12px;">
        {{ $sched->section_name ?? 'No Section' }}
      </div>

      {{-- Schedule chip --}}
      <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
        <span style="font-size:.72rem;background:#f1f5f9;color:#475569;border-radius:6px;padding:3px 8px;font-weight:500;">
          {{ $sched->days_label }}
        </span>
        <span style="font-size:.72rem;background:#f1f5f9;color:#475569;border-radius:6px;padding:3px 8px;font-weight:500;">
          {{ $sched->time_range }}
        </span>
        @if($sched->room)
        <span style="font-size:.72rem;background:#f1f5f9;color:#475569;border-radius:6px;padding:3px 8px;font-weight:500;">
          {{ $sched->room }}
        </span>
        @endif
      </div>

      {{-- CTA --}}
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:.78rem;font-weight:600;color:#6366f1;">Enter Grades</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2" style="width:16px;height:16px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </div>
    </a>
    @endforeach
  </div>
  @endif

</div>
@endsection
