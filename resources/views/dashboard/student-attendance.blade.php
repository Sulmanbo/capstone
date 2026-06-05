@extends('layouts.app')
@section('title', 'My Attendance')
@section('content')

@push('head')
<style>
.att-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px; }
.att-stat { background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;flex:1;min-width:150px;text-align:center; }
.att-stat__label { font-size:.75rem;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:8px; }
.att-stat__value { font-size:1.8rem;font-weight:800;color:#0f172a; }
.att-stat__subtext { font-size:.75rem;color:#94a3b8;margin-top:4px; }
.att-bar { width:100%;height:8px;border-radius:4px;background:#f1f5f9;overflow:hidden;margin-top:8px; }
.att-bar__fill { height:100%;background:#22c55e;transition:width .3s; }
.att-table { width:100%;border-collapse:collapse;font-size:.87rem; }
.att-table th { padding:10px 12px;text-align:left;font-weight:700;font-size:.72rem;text-transform:uppercase;color:#475569;border-bottom:1px solid #e2e8f0;background:#f8fafc; }
.att-table td { padding:10px 12px;border-bottom:1px solid #f1f5f9; }
.att-status { display:inline-block;padding:.3rem .6rem;border-radius:6px;font-size:.72rem;font-weight:700;text-transform:uppercase; }
.att-status.present { background:#dcfce7;color:#166534; }
.att-status.absent { background:#fee2e2;color:#991b1b; }
.att-status.late { background:#fef3c7;color:#92400e; }
.att-status.excused { background:#dbeafe;color:#1e40af; }
</style>
@endpush

<div style="margin-bottom:24px;">
  <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">My Attendance</h1>
  <p style="font-size:.82rem;color:#64748b;margin:0;">Track your attendance records by subject.</p>
</div>

@if(!$enrollment)
  <div class="enc-card" style="padding:40px;text-align:center;color:#94a3b8;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p style="margin:0;font-size:.9rem;">You are not currently enrolled in any section.</p>
    <p style="margin:8px 0 0;font-size:.8rem;color:#94a3b8;">Contact your registrar to enroll in a section.</p>
  </div>
@else

  {{-- Overall stats --}}
  <div class="att-header">
    <div class="att-stat">
      <div class="att-stat__label">Total Sessions</div>
      <div class="att-stat__value">{{ $stats['total'] }}</div>
      <div class="att-bar">
        <div class="att-bar__fill" style="width:100%;"></div>
      </div>
    </div>
    <div class="att-stat">
      <div class="att-stat__label">Present</div>
      <div class="att-stat__value">{{ $stats['present'] }}</div>
      <div class="att-bar">
        <div class="att-bar__fill" style="width:{{ $stats['present'] / max(1, $stats['total']) * 100 }}%;background:#22c55e;"></div>
      </div>
    </div>
    <div class="att-stat">
      <div class="att-stat__label">Absent</div>
      <div class="att-stat__value">{{ $stats['absent'] }}</div>
      <div class="att-bar">
        <div class="att-bar__fill" style="width:{{ $stats['absent'] / max(1, $stats['total']) * 100 }}%;background:#ef4444;"></div>
      </div>
    </div>
    <div class="att-stat">
      <div class="att-stat__label">Attendance Rate</div>
      <div class="att-stat__value" style="color:{{ $stats['percentage'] >= 85 ? '#22c55e' : ($stats['percentage'] >= 75 ? '#f59e0b' : '#ef4444') }};">
        {{ $stats['percentage'] }}%
      </div>
      <div class="att-bar">
        <div class="att-bar__fill" style="width:{{ $stats['percentage'] }}%;background:{{ $stats['percentage'] >= 85 ? '#22c55e' : ($stats['percentage'] >= 75 ? '#f59e0b' : '#ef4444') }};"></div>
      </div>
    </div>
  </div>

  {{-- By-subject breakdown --}}
  @if($bySubject->isEmpty())
    <div class="enc-card" style="padding:40px;text-align:center;color:#94a3b8;">
      <p style="margin:0;">No attendance records yet. Check back once classes begin.</p>
    </div>
  @else
    @foreach($bySubject as $subject => $data)
      <div class="enc-card" style="margin-bottom:20px;overflow:hidden;">
        <div class="enc-card__header">
          <div class="enc-card__title">{{ $subject }}</div>
          <div style="font-size:.82rem;color:#64748b;">
            {{ $data['present'] }}/{{ $data['total'] }} present ({{ $data['percentage'] }}%)
          </div>
        </div>
        <div class="enc-card__body" style="padding:0;">
          <table class="att-table">
            <thead>
              <tr>
                <th style="width:120px;">Date</th>
                <th style="width:100px;">Status</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data['records'] as $record)
                <tr>
                  <td>{{ $record->date->format('M j, Y') }}</td>
                  <td>
                    <span class="att-status att-status--{{ $record->status }}">
                      {{ ucfirst($record->status) }}
                    </span>
                  </td>
                  <td style="color:#64748b;font-size:.82rem;">{{ $record->remarks ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  @endif

@endif

@endsection
