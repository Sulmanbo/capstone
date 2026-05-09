@extends('layouts.app')

@section('title', 'Program Curriculum')
@section('breadcrumb', 'Program Curriculum')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Program Curriculum</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — {{ $studentInfo['grade_level'] }}</p>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Curriculum Overview</div>
    <span class="enc-card__meta">Required and elective subjects for your program</span>
  </div>
  <div class="enc-card__body">
    @if(empty($curriculum))
      <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.25)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
        <p style="font-size:.95rem;font-weight:600;">No Curriculum Data</p>
        <p style="font-size:.82rem;margin-top:.25rem;">Your program curriculum will be displayed here once it has been configured.</p>
      </div>
    @else
      @foreach($curriculum as $year)
      <div style="margin-bottom:1.5rem;">
        <div style="font-size:.82rem;text-transform:uppercase;letter-spacing:.06em;color:rgba(255,255,255,.4);font-weight:600;margin-bottom:.75rem;">{{ $year['label'] }}</div>
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="border-bottom:1px solid rgba(255,255,255,.1);">
                <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Code</th>
                <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Subject</th>
                <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Category</th>
                <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($year['subjects'] as $subject)
              <tr style="border-bottom:1px solid rgba(255,255,255,.06);">
                <td style="padding:.65rem .75rem;font-size:.82rem;color:rgba(255,255,255,.5);font-family:monospace;">{{ $subject['code'] }}</td>
                <td style="padding:.65rem .75rem;font-size:.88rem;font-weight:500;">{{ $subject['name'] }}</td>
                <td style="padding:.65rem .75rem;font-size:.85rem;color:rgba(255,255,255,.6);">{{ $subject['category'] }}</td>
                <td style="padding:.65rem .75rem;">
                  @php $s = $subject['status'] ?? 'pending'; @endphp
                  <span style="padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;
                    background:{{ $s==='completed' ? 'rgba(34,197,94,.15)' : ($s==='in-progress' ? 'rgba(99,102,241,.2)' : 'rgba(255,255,255,.07)') }};
                    color:{{ $s==='completed' ? '#4ade80' : ($s==='in-progress' ? '#a5b4fc' : 'rgba(255,255,255,.4)') }};">
                    {{ ucfirst($s) }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endforeach
    @endif
  </div>
</div>
@endsection
