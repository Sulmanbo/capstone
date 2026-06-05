@extends('layouts.app')
@section('title', 'SF10 — Permanent Record')
@section('breadcrumb', 'SF Forms / SF10 Permanent Record')

@push('head')
<style>
.sf-header { background: linear-gradient(135deg,#7f1d1d,#dc2626); border-radius:16px; padding:24px 28px; color:#fff; margin-bottom:22px; }
.sf-header h2 { margin:0 0 4px; font-size:1.2rem; font-weight:800; }
.sf-search-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 22px; margin-bottom:22px; }
.sf-search-row { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
.sf-search-row label { display:block; font-size:.78rem; font-weight:600; color:#475569; margin-bottom:5px; }
.sf-search-row input { padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:.86rem; background:#f8fafc; min-width:260px; }
.sf-btn { background:#7f1d1d; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; }
.sf-btn-dl { background:#059669; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; text-decoration:none; display:inline-block; }
.sf-student-list { margin-top:14px; max-height:200px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:9px; }
.sf-student-row { padding:9px 14px; cursor:pointer; font-size:.85rem; color:#334155; border-bottom:1px solid #f1f5f9; transition:background .12s; text-decoration:none; display:block; }
.sf-student-row:last-child { border-bottom:none; }
.sf-student-row:hover { background:#f0f9ff; color:#0369a1; }
.sf-student-row.active { background:#dbeafe; font-weight:700; color:#1e40af; }
.perm-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; margin-bottom:20px; overflow:hidden; }
.perm-yr-header { background:#7f1d1d; color:#fff; padding:12px 20px; font-weight:700; font-size:.9rem; display:flex; align-items:center; justify-content:space-between; }
.perm-table { width:100%; border-collapse:collapse; }
.perm-table th { padding:8px 12px; background:#f8fafc; font-size:.73rem; font-weight:700; color:#64748b; text-transform:uppercase; text-align:center; border-bottom:1px solid #e2e8f0; }
.perm-table th:first-child { text-align:left; }
.perm-table td { padding:9px 12px; font-size:.83rem; color:#334155; border-bottom:1px solid #f8fafc; text-align:center; }
.perm-table td:first-child { text-align:left; font-weight:600; }
.perm-table tr:last-child td { border-bottom:none; }
</style>
@endpush

@section('content')
<div class="sf-header">
  <h2>📚 SF10 — Learner's Permanent Academic Record</h2>
  <p>Complete academic history across all school years enrolled.</p>
</div>

<div class="sf-search-card">
  <form method="GET" action="{{ route('sf.sf10') }}">
    <div class="sf-search-row">
      <div>
        <label>Search Student</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="Name or LRN…">
      </div>
      <button type="submit" class="sf-btn">Search</button>
      @if($student)
        <a href="{{ route('sf.sf10') }}?student_id={{ $studentId }}&search={{ $search }}&download=1" class="sf-btn-dl">⬇ Download PDF</a>
      @endif
      <input type="hidden" name="student_id" value="{{ $studentId }}">
    </div>
  </form>

  @if($students->isNotEmpty())
    <div class="sf-student-list">
      @foreach($students as $s)
        <a href="{{ route('sf.sf10') }}?student_id={{ $s->id }}&search={{ $search }}"
           class="sf-student-row {{ $studentId == $s->id ? 'active' : '' }}">
          <strong>{{ $s->last_name }}, {{ $s->first_name }}</strong>
          <span style="color:#94a3b8;font-size:.78rem;margin-left:8px;">LRN: {{ $s->lrn ?? 'N/A' }}</span>
        </a>
      @endforeach
    </div>
  @endif
</div>

@if($student)
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 22px;margin-bottom:18px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
  <div><span style="font-size:.74rem;font-weight:700;color:#64748b;display:block;">Name</span><strong>{{ $student->last_name }}, {{ $student->first_name }}</strong></div>
  <div><span style="font-size:.74rem;font-weight:700;color:#64748b;display:block;">LRN</span><strong>{{ $student->lrn ?? 'N/A' }}</strong></div>
  <div><span style="font-size:.74rem;font-weight:700;color:#64748b;display:block;">Gender</span><strong>{{ ucfirst($student->gender ?? '—') }}</strong></div>
  <div><span style="font-size:.74rem;font-weight:700;color:#64748b;display:block;">Years Enrolled</span><strong>{{ $history->count() }}</strong></div>
</div>

@forelse($history as $rec)
  @php $enrollment = $rec['enrollment']; $grades = $rec['grades']; @endphp
  <div class="perm-card">
    <div class="perm-yr-header">
      <span>{{ $enrollment->section?->academicYear?->year_label }} — Grade {{ $enrollment->section?->grade_level }} · {{ $enrollment->section?->section_name }}</span>
      <span style="font-size:.78rem;opacity:.8;">{{ ucfirst($enrollment->status) }}</span>
    </div>
    @if($grades->isEmpty())
      <div style="padding:14px 18px;color:#94a3b8;font-size:.83rem;">No finalized grades on record.</div>
    @else
      @php
        $subjects = $grades->groupBy(fn($g) => $g->sectionSubject?->subject?->subject_name);
        $quarters = $grades->pluck('gradingQuarter')->filter()->unique('quarter_number')->sortBy('quarter_number');
      @endphp
      <div style="overflow-x:auto;">
        <table class="perm-table">
          <thead>
            <tr>
              <th style="text-align:left;padding:10px 14px;">Learning Area</th>
              @foreach($quarters as $q)<th>Q{{ $q->quarter_number }}</th>@endforeach
              <th>Final</th>
            </tr>
          </thead>
          <tbody>
            @foreach($subjects as $subj => $subGrades)
              @php
                $qMap = $subGrades->pluck('final_grade','gradingQuarter.quarter_number');
                $vals = $qMap->filter()->values();
                $final = $vals->isNotEmpty() ? round($vals->avg(),2) : null;
              @endphp
              <tr>
                <td>{{ $subj }}</td>
                @foreach($quarters as $q)
                  @php $g = $qMap[$q->quarter_number] ?? null; @endphp
                  <td style="color:{{ $g !== null ? ($g>=75?'#059669':'#dc2626') : '#94a3b8' }};font-weight:{{ $g!==null?'700':'400' }};">
                    {{ $g ?? '—' }}
                  </td>
                @endforeach
                <td style="color:{{ $final!==null?($final>=75?'#059669':'#dc2626'):'#94a3b8' }};font-weight:700;">
                  {{ $final ?? '—' }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@empty
  <div style="text-align:center;padding:40px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;color:#94a3b8;">
    <p style="font-weight:600;">No enrollment history found.</p>
  </div>
@endforelse
@endif
@endsection
