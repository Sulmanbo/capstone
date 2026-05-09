@extends('layouts.app')
@section('title', 'Teaching Load')
@section('breadcrumb', 'Teaching Load')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:24px;">
    <h1 style="font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Teaching Load</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">All subjects and sections assigned to you this academic year.</p>
  </div>

  @if($activeAcademicYear)
  <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:20px;font-size:.78rem;font-weight:600;color:#1d4ed8;margin-bottom:20px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
    </svg>
    {{ $activeAcademicYear->label ?? $activeAcademicYear->year }}
  </div>
  @endif

  @if($allSchedules->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:60px 24px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:#94a3b8;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:6px;">No classes assigned yet</div>
    <div style="font-size:.82rem;color:#94a3b8;">Your schedule will appear here once the admin assigns classes to you.</div>
  </div>
  @else
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">
    <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
      <div style="font-size:.9rem;font-weight:700;color:#0f172a;">Your Classes</div>
      <div style="font-size:.8rem;color:#94a3b8;">{{ $allSchedules->count() }} {{ Str::plural('class', $allSchedules->count()) }}</div>
    </div>
    <table style="width:100%;border-collapse:collapse;font-size:.845rem;">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 24px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Subject</th>
          <th style="text-align:left;padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Section</th>
          <th style="text-align:left;padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Room</th>
          <th style="text-align:left;padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Schedule</th>
          <th style="text-align:left;padding:10px 16px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Days</th>
        </tr>
      </thead>
      <tbody>
        @foreach($allSchedules as $sched)
        <tr style="border-bottom:1px solid #f8fafc;">
          <td style="padding:14px 24px;">
            <div style="font-weight:600;color:#0f172a;">{{ $sched->subject_name }}</div>
          </td>
          <td style="padding:14px 16px;color:#374151;">{{ $sched->section_name ?? '—' }}</td>
          <td style="padding:14px 16px;color:#374151;">{{ $sched->room ?? '—' }}</td>
          <td style="padding:14px 16px;color:#374151;white-space:nowrap;">{{ $sched->time_range }}</td>
          <td style="padding:14px 16px;">
            <span style="display:inline-block;padding:.2rem .6rem;background:#eff6ff;color:#1d4ed8;border-radius:6px;font-size:.75rem;font-weight:600;">
              {{ $sched->days_label }}
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Summary strip --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:16px;">
    @php
      $totalHoursPerWeek = $allSchedules->sum(function($s) {
        $start = strtotime($s->start_time);
        $end   = strtotime($s->end_time);
        $hrs   = ($end - $start) / 3600;
        return $hrs * count($s->days ?? []);
      });
    @endphp
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;">
      <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:4px;">Total Sections</div>
      <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">{{ $allSchedules->count() }}</div>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;">
      <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:4px;">Hrs / Week</div>
      <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">{{ number_format($totalHoursPerWeek, 1) }}</div>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 20px;">
      <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:4px;">Unique Subjects</div>
      <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">{{ $allSchedules->pluck('subject_name')->unique()->count() }}</div>
    </div>
  </div>
  @endif

</div>
@endsection
