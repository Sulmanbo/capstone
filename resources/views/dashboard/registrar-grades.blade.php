@extends('layouts.app')
@section('title', 'Grades & Records')
@section('breadcrumb', 'Grades & Records')

@push('head')
<style>
.gr-stat { background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:16px;flex:1;min-width:140px;text-align:center; }
.gr-stat__label { font-size:.72rem;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:6px; }
.gr-stat__value { font-size:1.6rem;font-weight:800;color:#0f172a; }
.gr-table { width:100%;border-collapse:collapse;font-size:.85rem; }
.gr-table th { padding:10px 12px;text-align:left;font-weight:700;font-size:.7rem;text-transform:uppercase;color:#475569;border-bottom:1px solid #e2e8f0;background:#f8fafc; }
.gr-table td { padding:10px 12px;border-bottom:1px solid #f1f5f9; }
.grade-badge { display:inline-block;padding:.35rem .7rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase; }
.grade-badge.submitted { background:#fef3c7;color:#92400e; }
.grade-badge.finalized { background:#dbeafe;color:#1e40af; }
.grade-badge.locked { background:#dcfce7;color:#166534; }
.grade-value { font-weight:800;color:#0f172a;font-size:.95rem; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Grades & Records</h1>
      <p style="font-size:.82rem;color:#64748b;margin:0;">Review and verify faculty grade submissions for the active quarter.</p>
    </div>
  </div>

  @if(session('success'))
  <div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.85rem;">{{ session('success') }}</div>
  @endif

  @if($errors->any())
  <div style="margin-bottom:16px;padding:12px 16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.85rem;">
    @foreach($errors->all() as $err)
    <div>{{ $err }}</div>
    @endforeach
  </div>
  @endif

  {{-- Stats Cards --}}
  <div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
    <div class="gr-stat">
      <div class="gr-stat__label">Pending Review</div>
      <div class="gr-stat__value">{{ $stats['pending_review'] }}</div>
    </div>
    <div class="gr-stat">
      <div class="gr-stat__label">Finalized</div>
      <div class="gr-stat__value">{{ $stats['total_finalized'] }}</div>
    </div>
    <div class="gr-stat">
      <div class="gr-stat__label">Locked</div>
      <div class="gr-stat__value">{{ $stats['total_locked'] }}</div>
    </div>
    <div class="gr-stat">
      <div class="gr-stat__label">Active Quarter</div>
      <div class="gr-stat__value" style="font-size:1.1rem;">{{ $activeQuarter ? 'Q' . $activeQuarter->quarter_number : 'None' }}</div>
    </div>
  </div>

  {{-- Grade Submissions Table --}}
  @if($submittedGrades->isEmpty())
  <div class="enc-card" style="padding:40px;text-align:center;color:#94a3b8;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p style="margin:0;font-size:.9rem;">No submitted grades to review.</p>
    @if(!$activeQuarter)
    <p style="margin:8px 0 0;font-size:.8rem;color:#94a3b8;">No active grading quarter found.</p>
    @endif
  </div>
  @else
  <div class="enc-card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="gr-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Subject</th>
            <th>Section</th>
            <th>WW</th>
            <th>PT</th>
            <th>QA</th>
            <th>Final Grade</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($submittedGrades as $grade)
          <tr>
            <td style="font-weight:600;color:#0f172a;">
              {{ $grade->enrollment->student?->full_name ?? 'Unknown' }}
            </td>
            <td>{{ $grade->sectionSubject?->subject?->subject_name ?? 'Unknown' }}</td>
            <td>{{ $grade->sectionSubject?->section?->section_name ?? 'Unknown' }}</td>
            <td><span class="grade-value">{{ $grade->written_work ?? '—' }}</span></td>
            <td><span class="grade-value">{{ $grade->performance_task ?? '—' }}</span></td>
            <td><span class="grade-value">{{ $grade->quarterly_assessment ?? '—' }}</span></td>
            <td><span class="grade-value">{{ $grade->final_grade ?? '—' }}</span></td>
            <td>
              <span class="grade-badge grade-badge--{{ $grade->status }}">{{ ucfirst($grade->status) }}</span>
            </td>
            <td style="font-size:.8rem;color:#64748b;">{{ $grade->submitted_at?->format('M d, Y') }}</td>
            <td style="text-align:center;">
              <form method="POST" action="{{ route('registrar.grades.finalize', $grade) }}" style="display:inline;">
                @csrf
                <button type="submit" style="padding:.35rem .7rem;font-size:.75rem;background:#1d4ed8;color:#fff;border:none;border-radius:4px;cursor:pointer;font-weight:600;">Finalize</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($submittedGrades->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #e2e8f0;background:#f8fafc;">
      {{ $submittedGrades->links() }}
    </div>
    @endif
  </div>
  @endif

</div>
@endsection
