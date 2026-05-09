@extends('layouts.app')

@section('title', 'Schedule & Assessment')
@section('breadcrumb', 'Schedule & Assessment')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Schedule & Assessment</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — {{ now()->format('l, F d, Y') }}</p>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Today's Schedule</div>
    <span class="enc-card__meta">{{ now()->format('l') }}'s class timetable</span>
  </div>
  <div class="enc-card__body">
    @if(empty($todaySchedule))
      <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.25)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        <p style="font-size:.95rem;font-weight:600;">No Classes Today</p>
        <p style="font-size:.82rem;margin-top:.25rem;">No scheduled classes found for today.</p>
      </div>
    @else
      <div style="display:flex;flex-direction:column;gap:.6rem;">
        @foreach($todaySchedule as $class)
        <div style="display:flex;align-items:center;gap:1rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:.85rem 1rem;">
          <div style="min-width:110px;font-size:.8rem;font-weight:600;color:rgba(255,255,255,.5);font-family:monospace;">{{ $class['time'] }}</div>
          <div style="flex:1;">
            <div style="font-size:.92rem;font-weight:600;color:#fff;">{{ $class['subject'] }}</div>
            <div style="font-size:.8rem;color:rgba(255,255,255,.45);margin-top:.15rem;">{{ $class['teacher'] }}</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:.82rem;color:rgba(255,255,255,.5);">{{ $class['room'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Upcoming Assessments</div>
    <span class="enc-card__meta">Exams, quizzes, and projects due soon</span>
  </div>
  <div class="enc-card__body">
    <div style="text-align:center;padding:2.5rem 1rem;color:rgba(255,255,255,.45);">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto .75rem;display:block;color:rgba(255,255,255,.2)">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"/>
      </svg>
      <p style="font-size:.9rem;font-weight:600;">No Upcoming Assessments</p>
      <p style="font-size:.8rem;margin-top:.2rem;">Assessment schedules will appear here once posted by your teachers.</p>
    </div>
  </div>
</div>
@endsection
