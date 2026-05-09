@extends('layouts.app')

@section('title', 'Course Offerings')
@section('breadcrumb', 'Course Offerings')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Course Offerings</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — {{ $studentInfo['grade_level'] }}</p>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Available Courses</div>
    <span class="enc-card__meta">Subjects offered for the current academic year</span>
  </div>
  <div class="enc-card__body">
    @if(empty($courses))
      <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.25)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
        </svg>
        <p style="font-size:.95rem;font-weight:600;">No Course Offerings Available</p>
        <p style="font-size:.82rem;margin-top:.25rem;">Course offerings will be displayed here once the registrar publishes them.</p>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:1px solid rgba(255,255,255,.1);">
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Code</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Subject</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Teacher</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Schedule</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($courses as $course)
            <tr style="border-bottom:1px solid rgba(255,255,255,.06);">
              <td style="padding:.65rem .75rem;font-size:.82rem;color:rgba(255,255,255,.5);font-family:monospace;">{{ $course['code'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.88rem;font-weight:500;">{{ $course['name'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.85rem;color:rgba(255,255,255,.7);">{{ $course['teacher'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.85rem;color:rgba(255,255,255,.5);">{{ $course['schedule'] }}</td>
              <td style="padding:.65rem .75rem;">
                <span style="padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;background:rgba(99,102,241,.2);color:#a5b4fc;">Enrolled</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
