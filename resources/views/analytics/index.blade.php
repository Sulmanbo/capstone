@extends('layouts.app')
@section('title', 'Analytics Dashboard')
@section('breadcrumb', 'Analytics')

@push('head')
<style>
.an-grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 22px; }
.an-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 22px; }
@media(max-width:900px){ .an-grid-4 { grid-template-columns: repeat(2,1fr); } .an-grid-2 { grid-template-columns: 1fr; } }
@media(max-width:540px){ .an-grid-4 { grid-template-columns: 1fr 1fr; } }

.an-kpi { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px 22px; }
.an-kpi-label { font-size: .73rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
.an-kpi-num { font-size: 2.2rem; font-weight: 900; line-height: 1; color: #1e293b; }
.an-kpi-sub { font-size: .78rem; color: #94a3b8; margin-top: 4px; }

.an-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px 24px; }
.an-card h3 { font-size: .96rem; font-weight: 800; color: #1e293b; margin: 0 0 18px; }

.an-bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.an-bar-label { font-size: .8rem; color: #334155; min-width: 220px; }
.an-bar-track { flex: 1; height: 10px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.an-bar-fill { height: 100%; border-radius: 99px; transition: width .6s ease; }
.an-bar-val { font-size: .8rem; font-weight: 700; color: #1e293b; min-width: 30px; text-align: right; }

.an-pipeline { display: flex; gap: 0; }
.an-pipe-seg { flex: 1; text-align: center; padding: 12px 8px; font-size: .78rem; font-weight: 700; }
.an-pipe-seg:first-child { border-radius: 10px 0 0 10px; }
.an-pipe-seg:last-child  { border-radius: 0 10px 10px 0; }

.an-table { width: 100%; border-collapse: collapse; }
.an-table th { padding: 8px 12px; background: #f8fafc; font-size: .72rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: left; border-bottom: 1px solid #e2e8f0; }
.an-table td { padding: 10px 12px; font-size: .83rem; color: #334155; border-bottom: 1px solid #f8fafc; }
.an-table tr:last-child td { border-bottom: none; }

.an-donut-wrap { display: flex; align-items: center; gap: 24px; }
.an-legend { flex: 1; }
.an-legend-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: .82rem; color: #475569; }
.an-legend-dot { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }
</style>
@endpush

@section('content')

<div style="margin-bottom:22px;">
  <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0;">Analytics Dashboard</h1>
  <p style="color:#64748b;font-size:.85rem;margin:4px 0 0;">
    {{ $activeYear ? 'Academic Year: '.$activeYear->year_label : 'No active academic year' }}
  </p>
</div>

{{-- KPI Row --}}
<div class="an-grid-4">
  <div class="an-kpi" style="border-top:4px solid #3b82f6;">
    <div class="an-kpi-label">Total Students</div>
    <div class="an-kpi-num">{{ number_format($totalStudents) }}</div>
    <div class="an-kpi-sub">active accounts</div>
  </div>
  <div class="an-kpi" style="border-top:4px solid #10b981;">
    <div class="an-kpi-label">Enrolled</div>
    <div class="an-kpi-num" style="color:#059669;">{{ number_format($enrolledCount) }}</div>
    <div class="an-kpi-sub">this academic year</div>
  </div>
  <div class="an-kpi" style="border-top:4px solid #ef4444;">
    <div class="an-kpi-label">Dropped</div>
    <div class="an-kpi-num" style="color:#dc2626;">{{ number_format($droppedCount) }}</div>
    <div class="an-kpi-sub">this academic year</div>
  </div>
  <div class="an-kpi" style="border-top:4px solid #8b5cf6;">
    <div class="an-kpi-label">Faculty</div>
    <div class="an-kpi-num" style="color:#7c3aed;">{{ number_format($totalFaculty) }}</div>
    <div class="an-kpi-sub">{{ $activeSections }} active sections</div>
  </div>
</div>

<div class="an-grid-2">
  {{-- Enrollment by Grade --}}
  <div class="an-card">
    <h3>📊 Enrollment by Grade Level</h3>
    @if($enrollmentByGrade->isEmpty())
      <p style="color:#94a3b8;font-size:.85rem;text-align:center;margin:20px 0;">No enrollment data available</p>
    @else
      @php $maxEnroll = $enrollmentByGrade->max() ?: 1; @endphp
      @foreach($enrollmentByGrade as $grade => $count)
        <div class="an-bar-row">
          <div class="an-bar-label">Grade {{ $grade }}</div>
          <div class="an-bar-track">
            <div class="an-bar-fill" style="width:{{ ($count/$maxEnroll)*100 }}%;background:#3b82f6;"></div>
          </div>
          <div class="an-bar-val">{{ $count }}</div>
        </div>
      @endforeach
    @endif
  </div>

  {{-- Grade Distribution --}}
  <div class="an-card">
    <h3>📈 Grade Distribution (Finalized Grades)</h3>
    @if($gradeDistribution->isEmpty())
      <p style="color:#94a3b8;font-size:.85rem;text-align:center;margin:20px 0;">No finalized grades yet</p>
    @else
      @php
        $total = $gradeDistribution->sum();
        $colors = ['#22c55e','#84cc16','#f59e0b','#f97316','#ef4444'];
        $ci = 0;
      @endphp
      @foreach($gradeDistribution as $band => $cnt)
        @php $pct = $total > 0 ? round(($cnt/$total)*100,1) : 0; @endphp
        <div class="an-bar-row">
          <div class="an-bar-label" style="min-width:260px;font-size:.76rem;">{{ $band }}</div>
          <div class="an-bar-track">
            <div class="an-bar-fill" style="width:{{ $pct }}%;background:{{ $colors[$ci++] ?? '#3b82f6' }};"></div>
          </div>
          <div class="an-bar-val">{{ $cnt }}</div>
        </div>
      @endforeach
      <div style="margin-top:14px;padding-top:14px;border-top:1px solid #f1f5f9;display:flex;gap:20px;flex-wrap:wrap;">
        <div style="font-size:.83rem;color:#475569;">
          <strong>Average Grade:</strong>
          <span style="color:{{ ($avgGrade ?? 0) >= 75 ? '#059669' : '#dc2626' }};font-weight:700;">
            {{ $avgGrade ? number_format($avgGrade,2) : 'N/A' }}
          </span>
        </div>
        <div style="font-size:.83rem;color:#475569;">
          <strong>Below Passing:</strong>
          <span style="color:#dc2626;font-weight:700;">{{ $belowPassingCount }}</span>
        </div>
      </div>
    @endif
  </div>
</div>

<div class="an-grid-2">
  {{-- Grade Pipeline --}}
  <div class="an-card">
    <h3>⚙️ Grade Submission Pipeline</h3>
    @php
      $pipeline = ['draft'=>['Draft','#94a3b8'],'submitted'=>['Submitted','#f59e0b'],'finalized'=>['Finalized','#3b82f6'],'locked'=>['Locked','#059669']];
      $pipeTotal = $gradePipeline->sum() ?: 1;
    @endphp
    <div class="an-pipeline" style="height:56px;border-radius:10px;overflow:hidden;margin-bottom:14px;">
      @foreach($pipeline as $key => [$label,$color])
        @php $cnt = $gradePipeline[$key] ?? 0; $pct = round(($cnt/$pipeTotal)*100); @endphp
        @if($cnt > 0)
        <div class="an-pipe-seg" style="background:{{ $color }};color:#fff;flex:{{ $cnt }};display:flex;flex-direction:column;justify-content:center;">
          <div>{{ $label }}</div>
          <div style="font-size:1.1rem;">{{ $cnt }}</div>
        </div>
        @endif
      @endforeach
    </div>
    <div style="display:flex;gap:16px;flex-wrap:wrap;">
      @foreach($pipeline as $key => [$label,$color])
        <div style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:#475569;">
          <span style="width:12px;height:12px;border-radius:3px;background:{{ $color }};display:inline-block;"></span>
          {{ $label }}: <strong>{{ $gradePipeline[$key] ?? 0 }}</strong>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Attendance & At-Risk --}}
  <div class="an-card">
    <h3>📅 Attendance Overview</h3>
    @if($attendanceRate !== null)
      <div style="text-align:center;margin-bottom:16px;">
        <div style="font-size:3rem;font-weight:900;color:{{ $attendanceRate >= 90 ? '#059669' : ($attendanceRate >= 75 ? '#d97706' : '#dc2626') }};">
          {{ $attendanceRate }}%
        </div>
        <div style="font-size:.82rem;color:#64748b;">Overall Attendance Rate (Active Year)</div>
      </div>
      <div style="background:#f8fafc;border-radius:10px;padding:10px 14px;">
        <div class="an-bar-track" style="height:14px;">
          <div class="an-bar-fill" style="width:{{ $attendanceRate }}%;background:{{ $attendanceRate >= 90 ? '#22c55e' : ($attendanceRate >= 75 ? '#f59e0b' : '#ef4444') }};"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#94a3b8;margin-top:4px;">
          <span>0%</span><span>75%</span><span>100%</span>
        </div>
      </div>
    @else
      <p style="color:#94a3b8;font-size:.85rem;text-align:center;margin:20px 0;">No attendance data available</p>
    @endif
  </div>
</div>

{{-- At-Risk Students --}}
@if($atRisk->isNotEmpty())
<div class="an-card" style="margin-bottom:22px;">
  <h3>⚠️ At-Risk Students (Failing 2+ Subjects)</h3>
  <div style="overflow-x:auto;">
    <table class="an-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Failing Subjects</th>
          <th>Count</th>
        </tr>
      </thead>
      <tbody>
        @foreach($atRisk as $r)
        <tr>
          <td>
            <div style="font-weight:700;color:#1e293b;">{{ $r['student']?->first_name }} {{ $r['student']?->last_name }}</div>
            <div style="font-size:.74rem;color:#64748b;">LRN: {{ $r['student']?->lrn ?? 'N/A' }}</div>
          </td>
          <td style="font-size:.81rem;color:#475569;">{{ $r['subjects'] }}</td>
          <td>
            <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:99px;font-weight:700;font-size:.75rem;">
              {{ $r['count'] }} failing
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
