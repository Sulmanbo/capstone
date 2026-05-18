@extends('layouts.app')
@section('title', 'Student Promotion')
@section('breadcrumb', 'Student Promotion')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Student Promotion & Advancement</h1>
      <p class="enc-page__subtitle">Advance students to the next grade level for the active academic year.</p>
    </div>
  </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="enc-alert enc-alert--success" style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">
  {{ session('success') }}
</div>
@endif

@if($errors->has('promotion'))
<div class="enc-alert enc-alert--danger" style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;font-weight:500;">
  {{ $errors->first('promotion') }}
</div>
@endif

{{-- Step 1: Select Source Year and Section --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__header">
    <div class="enc-card__title">Step 1 — Select Source Year &amp; Section</div>
  </div>
  <div class="enc-card__body" style="padding:20px 24px;">
    <form method="GET" action="{{ route('registrar.promotion') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:6px;min-width:220px;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" class="enc-select" onchange="this.form.submit()" style="min-width:220px;">
          <option value="">— Select Year —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ optional($selectedYear)->id == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} ({{ ucfirst($yr->status) }})
            </option>
          @endforeach
        </select>
      </div>

      @if($selectedYear && $sections->isNotEmpty())
      <div style="display:flex;flex-direction:column;gap:6px;min-width:220px;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Section</label>
        <select name="section_id" class="enc-select" onchange="this.form.submit()" style="min-width:220px;">
          <option value="">— Select Section —</option>
          @foreach($sections as $sec)
            <option value="{{ $sec->id }}" {{ optional($selectedSection)->id == $sec->id ? 'selected' : '' }}>
              {{ $sec->grade_level }} — {{ $sec->section_name }}
            </option>
          @endforeach
        </select>
      </div>
      @endif
    </form>
  </div>
</div>

{{-- Step 2: Student List --}}
@if($selectedSection)
@php
  $nextGradeMap = [
    'Grade 7'  => 'Grade 8',
    'Grade 8'  => 'Grade 9',
    'Grade 9'  => 'Grade 10',
    'Grade 10' => null,
  ];
  $nextGrade = $nextGradeMap[$selectedSection->grade_level] ?? null;
  $passingGrade = config('academic.passing_grade', 75);
@endphp

<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      Step 2 — Review &amp; Confirm Promotion
      <span style="font-weight:400;color:#64748b;font-size:.85rem;margin-left:8px;">
        {{ $selectedSection->grade_level }} — {{ $selectedSection->section_name }}
        · {{ $selectedYear->year_label }}
        @if($nextGrade)
          → Advancing to <strong>{{ $nextGrade }}</strong>
        @else
          → Graduating (final grade level)
        @endif
      </span>
    </div>
  </div>

  @if($students->isEmpty())
    <div class="enc-card__body" style="text-align:center;padding:40px;color:#94a3b8;">
      No enrolled students found in this section.
    </div>
  @else
  <form method="POST" action="{{ route('registrar.promotion.promote') }}"
        onsubmit="return confirm('Confirm promotion of selected students? This action will update their grade levels and create new enrollment records.')">
    @csrf
    <input type="hidden" name="source_year_id"    value="{{ $selectedYear->id }}">
    <input type="hidden" name="source_section_id" value="{{ $selectedSection->id }}">

    <div class="enc-card__body enc-card__body--no-pad">
      <div class="enc-table-wrap">
        <table class="enc-table">
          <thead>
            <tr>
              <th style="width:40px;">
                <input type="checkbox" id="selectAll" title="Select all promotable"
                       style="cursor:pointer;"
                       onchange="document.querySelectorAll('.student-cb:not(:disabled)').forEach(cb => cb.checked = this.checked)">
              </th>
              <th>Student</th>
              <th>LRN</th>
              <th style="text-align:center;">Locked Grades</th>
              <th style="text-align:center;">General Average</th>
              <th style="text-align:center;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $row)
            @php
              $avg = $row->average;
              $promotable = $row->is_promotable;
              $statusLabel = match(true) {
                !$row->has_locked_grades          => 'No Locked Grades',
                $avg === null                     => 'Incomplete',
                $promotable                       => $nextGrade ? 'Promotable' : 'For Graduation',
                default                           => 'For Retention',
              };
              $statusClass = match($statusLabel) {
                'Promotable', 'For Graduation' => 'pill--success',
                'For Retention'               => 'pill--danger',
                default                       => 'pill--neutral',
              };
            @endphp
            <tr>
              <td>
                <input type="checkbox"
                       name="student_ids[]"
                       value="{{ $row->student->id }}"
                       class="student-cb"
                       {{ $promotable ? '' : 'disabled' }}
                       {{ $promotable ? 'checked' : '' }}
                       style="cursor:{{ $promotable ? 'pointer' : 'not-allowed' }};">
              </td>
              <td>
                <div style="font-weight:600;color:#1e293b;">
                  {{ $row->student->last_name }}, {{ $row->student->first_name }}
                </div>
              </td>
              <td style="font-size:.84rem;color:#64748b;">{{ $row->student->lrn ?? '—' }}</td>
              <td style="text-align:center;font-size:.88rem;">{{ $row->grades_count }}</td>
              <td style="text-align:center;">
                @if($avg !== null)
                  <span style="font-weight:700;color:{{ $promotable ? '#16a34a' : '#dc2626' }};">{{ number_format($avg, 2) }}</span>
                @else
                  <span style="color:#94a3b8;">—</span>
                @endif
              </td>
              <td style="text-align:center;">
                <span class="sd-badge-pill {{ $statusClass }}">{{ $statusLabel }}</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div style="padding:16px 24px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e2e8f0;background:#f8fafc;">
      <div style="font-size:.84rem;color:#64748b;">
        @php $promotableCount = $students->where('is_promotable', true)->count(); @endphp
        <strong>{{ $promotableCount }}</strong> of <strong>{{ $students->count() }}</strong> students are promotable
        (general average ≥ {{ $passingGrade }}).
        @if(!$activeYear)
          <span style="color:#dc2626;font-weight:600;"> No active academic year — promotion requires an active year.</span>
        @endif
      </div>
      <button type="submit"
              class="enc-btn enc-btn--primary"
              {{ (!$activeYear || $promotableCount === 0) ? 'disabled' : '' }}
              style="{{ (!$activeYear || $promotableCount === 0) ? 'opacity:.5;cursor:not-allowed;' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
        Confirm Promotion
      </button>
    </div>
  </form>
  @endif
</div>
@endif

@endsection
