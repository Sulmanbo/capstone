@extends('layouts.app')

@section('title', 'Academic Holds')
@section('breadcrumb', 'Academic Holds')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Academic Holds</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — {{ $studentInfo['grade_level'] }}</p>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Hold Summary</div>
    <span class="enc-card__meta">Active and resolved holds on your account</span>
  </div>
  <div class="enc-card__body">
    @if(empty($holds))
      <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.25)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
        <p style="font-size:.95rem;font-weight:600;">No Academic Holds</p>
        <p style="font-size:.82rem;margin-top:.25rem;">Your account is in good standing with no active holds.</p>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:1px solid rgba(255,255,255,.1);">
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Hold Type</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Description</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Date Placed</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($holds as $hold)
            <tr style="border-bottom:1px solid rgba(255,255,255,.06);">
              <td style="padding:.65rem .75rem;font-size:.88rem;">{{ $hold['type'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.88rem;color:rgba(255,255,255,.7);">{{ $hold['description'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.85rem;color:rgba(255,255,255,.5);">{{ $hold['date'] }}</td>
              <td style="padding:.65rem .75rem;">
                <span style="padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;background:rgba(239,68,68,.15);color:#f87171;">Active</span>
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
