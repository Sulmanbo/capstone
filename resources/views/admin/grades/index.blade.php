@extends('layouts.app')
@section('title', 'Grades & Records — Admin')
@section('breadcrumb', 'Grades & Records')

@push('head')
<style>
.ag-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
@media(max-width:700px){ .ag-stats { grid-template-columns: repeat(2,1fr); } }
.ag-stat { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px; }
.ag-stat__label { font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
.ag-stat__num { font-size:1.8rem; font-weight:800; line-height:1; }

.ag-table-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; }
.ag-table { width:100%; border-collapse:collapse; }
.ag-table th { padding:10px 14px; background:#f8fafc; font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; text-align:left; border-bottom:1px solid #e2e8f0; }
.ag-table td { padding:11px 14px; font-size:.84rem; color:#334155; border-bottom:1px solid #f8fafc; vertical-align:middle; }
.ag-table tr:last-child td { border-bottom:none; }
.ag-table tr:hover td { background:#f8fafc; }

.ag-badge { display:inline-block; padding:2px 8px; border-radius:99px; font-size:.7rem; font-weight:700; }
.ag-badge--draft     { background:#f1f5f9; color:#64748b; }
.ag-badge--submitted { background:#fef3c7; color:#92400e; }
.ag-badge--finalized { background:#dbeafe; color:#1e40af; }
.ag-badge--locked    { background:#d1fae5; color:#065f46; }

.ag-prog { height:6px; border-radius:3px; background:#e2e8f0; overflow:hidden; margin-top:4px; }
.ag-prog__fill { height:100%; border-radius:3px; background:#7c3aed; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
  <div>
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0;">Grades & Records</h1>
    <p style="color:#64748b;font-size:.85rem;margin:4px 0 0;">
      Grade submission overview
      @if($activeYear) — {{ $activeYear->school_year }} @endif
    </p>
  </div>
  <a href="{{ route('admin.grade-lock.index') }}"
     style="display:inline-flex;align-items:center;gap:6px;background:#7c3aed;color:#fff;border-radius:10px;padding:9px 16px;font-size:.85rem;font-weight:700;text-decoration:none;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
    </svg>
    Manage Grade Locks
  </a>
</div>

@if(!$activeYear)
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:48px;text-align:center;color:#94a3b8;">
    <div style="font-size:2.5rem;margin-bottom:10px;">📊</div>
    <p style="font-weight:600;font-size:.95rem;margin:0 0 6px;color:#475569;">No Active Academic Year</p>
    <p style="font-size:.84rem;margin:0;">Set an academic year to active to view grade records.</p>
  </div>
@else

{{-- Quarter Filter --}}
@if($quarters->isNotEmpty())
<form method="GET" style="margin-bottom:20px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
  <label style="font-size:.84rem;font-weight:600;color:#475569;">Quarter:</label>
  <select name="quarter_id" onchange="this.form.submit()"
          style="padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.84rem;background:#fff;cursor:pointer;">
    @foreach($quarters as $q)
      <option value="{{ $q->id }}" {{ $selectedQuarter?->id == $q->id ? 'selected' : '' }}>
        Quarter {{ $q->quarter_number }}
        @if($q->status === 'active') (Active) @endif
      </option>
    @endforeach
  </select>
</form>
@endif

{{-- Stats --}}
<div class="ag-stats">
  <div class="ag-stat" style="border-left:4px solid #94a3b8;">
    <div class="ag-stat__label">Draft</div>
    <div class="ag-stat__num" style="color:#64748b;">{{ $stats['draft'] }}</div>
  </div>
  <div class="ag-stat" style="border-left:4px solid #f59e0b;">
    <div class="ag-stat__label">Submitted</div>
    <div class="ag-stat__num" style="color:#d97706;">{{ $stats['submitted'] }}</div>
  </div>
  <div class="ag-stat" style="border-left:4px solid #3b82f6;">
    <div class="ag-stat__label">Finalized</div>
    <div class="ag-stat__num" style="color:#2563eb;">{{ $stats['finalized'] }}</div>
  </div>
  <div class="ag-stat" style="border-left:4px solid #10b981;">
    <div class="ag-stat__label">Locked</div>
    <div class="ag-stat__num" style="color:#059669;">{{ $stats['locked'] }}</div>
  </div>
</div>

{{-- Section Table --}}
<div class="ag-table-card">
  @if($sectionSummaries->isEmpty())
    <div style="text-align:center;padding:50px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📋</div>
      <p style="font-weight:600;margin:0 0 4px;">No sections found</p>
      <p style="font-size:.82rem;margin:0;">No section-subjects are assigned for this academic year.</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="ag-table">
        <thead>
          <tr>
            <th>Section</th>
            <th>Subject</th>
            <th>Faculty</th>
            <th style="text-align:center;">Draft</th>
            <th style="text-align:center;">Submitted</th>
            <th style="text-align:center;">Finalized</th>
            <th style="text-align:center;">Locked</th>
            <th>Progress</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sectionSummaries as $ss)
          @php
            $total    = $ss->total_students;
            $locked   = $ss->grade_counts['locked'];
            $progress = $total > 0 ? round($locked / $total * 100) : 0;
          @endphp
          <tr>
            <td>
              <div style="font-weight:700;color:#1e293b;">{{ $ss->section?->section_name ?? 'N/A' }}</div>
              <div style="font-size:.74rem;color:#64748b;">{{ $ss->section?->grade_level ?? '' }}</div>
            </td>
            <td style="font-weight:600;font-size:.84rem;">{{ $ss->subject?->subject_name ?? 'N/A' }}</td>
            <td style="font-size:.82rem;color:#475569;">
              {{ $ss->faculty?->first_name }} {{ $ss->faculty?->last_name ?? '—' }}
            </td>
            <td style="text-align:center;">
              @if($ss->grade_counts['draft'] > 0)
                <span class="ag-badge ag-badge--draft">{{ $ss->grade_counts['draft'] }}</span>
              @else
                <span style="color:#cbd5e1;font-size:.8rem;">—</span>
              @endif
            </td>
            <td style="text-align:center;">
              @if($ss->grade_counts['submitted'] > 0)
                <span class="ag-badge ag-badge--submitted">{{ $ss->grade_counts['submitted'] }}</span>
              @else
                <span style="color:#cbd5e1;font-size:.8rem;">—</span>
              @endif
            </td>
            <td style="text-align:center;">
              @if($ss->grade_counts['finalized'] > 0)
                <span class="ag-badge ag-badge--finalized">{{ $ss->grade_counts['finalized'] }}</span>
              @else
                <span style="color:#cbd5e1;font-size:.8rem;">—</span>
              @endif
            </td>
            <td style="text-align:center;">
              @if($ss->grade_counts['locked'] > 0)
                <span class="ag-badge ag-badge--locked">{{ $ss->grade_counts['locked'] }}</span>
              @else
                <span style="color:#cbd5e1;font-size:.8rem;">—</span>
              @endif
            </td>
            <td style="min-width:100px;">
              <div style="font-size:.74rem;color:#64748b;margin-bottom:3px;">{{ $progress }}% locked</div>
              <div class="ag-prog">
                <div class="ag-prog__fill" style="width:{{ $progress }}%;"></div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

@endif

@endsection
