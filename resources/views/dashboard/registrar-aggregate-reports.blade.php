@extends('layouts.app')
@section('title', 'Aggregate Academic Reports')
@section('breadcrumb', 'Aggregate Reports')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Aggregate Academic Reports</h1>
      <p class="enc-page__subtitle">Honor roll and academic intervention lists based on locked final grades.</p>
    </div>
  </div>
</div>

{{-- Section selector --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__header">
    <div class="enc-card__title">Select Year &amp; Section</div>
  </div>
  <div class="enc-card__body" style="padding:20px 24px;">
    <form method="GET" action="{{ route('registrar.reports.aggregate') }}"
          style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:6px;min-width:220px;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" class="enc-select" onchange="this.form.submit()">
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
        <select name="section_id" class="enc-select" onchange="this.form.submit()">
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

@if($selectedSection)
@php $total = $honors->count() + $satisfactory->count() + $intervention->count(); @endphp

{{-- Summary strip --}}
<div class="sd-stats" style="margin-bottom:24px;">
  <div class="sd-stat">
    <div class="sd-stat__icon si--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $total }}</div>
      <div class="sd-stat__label">Students Evaluated</div>
    </div>
  </div>
  <div class="sd-stat">
    <div class="sd-stat__icon si--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $honors->count() }}</div>
      <div class="sd-stat__label">Honor Students (≥90)</div>
    </div>
  </div>
  <div class="sd-stat">
    <div class="sd-stat__icon si--orange">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
    </div>
    <div>
      <div class="sd-stat__val">{{ $intervention->count() }}</div>
      <div class="sd-stat__label">Need Intervention (&lt;75)</div>
    </div>
  </div>
</div>

@php $passingGrade = config('academic.passing_grade', 75); @endphp

{{-- Honor Roll --}}
@if($honors->isNotEmpty())
<div class="enc-card" style="margin-bottom:20px;">
  <div class="enc-card__header" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
    <div class="enc-card__title" style="color:#15803d;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;display:inline;margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
      Honor Roll — General Average ≥ 90
    </div>
  </div>
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th style="width:50px;text-align:center;">Rank</th>
            <th>Student</th>
            <th>LRN</th>
            <th style="text-align:center;">Subjects Graded</th>
            <th style="text-align:center;">General Average</th>
          </tr>
        </thead>
        <tbody>
          @foreach($honors as $i => $row)
          <tr>
            <td style="text-align:center;font-weight:700;color:#15803d;">{{ $i + 1 }}</td>
            <td style="font-weight:600;color:#1e293b;">
              {{ optional($row->student)->last_name }}, {{ optional($row->student)->first_name }}
            </td>
            <td style="font-size:.84rem;color:#64748b;">{{ optional($row->student)->lrn ?? '—' }}</td>
            <td style="text-align:center;">{{ $row->grade_count }}</td>
            <td style="text-align:center;">
              <span style="font-weight:700;color:#15803d;font-size:1rem;">{{ number_format($row->average, 2) }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- Academic Intervention --}}
@if($intervention->isNotEmpty())
<div class="enc-card" style="margin-bottom:20px;">
  <div class="enc-card__header" style="background:linear-gradient(135deg,#fff7ed,#fee2e2);">
    <div class="enc-card__title" style="color:#b91c1c;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;display:inline;margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
      Academic Intervention Required — General Average &lt; {{ $passingGrade }}
    </div>
  </div>
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>LRN</th>
            <th style="text-align:center;">Subjects Graded</th>
            <th style="text-align:center;">General Average</th>
            <th style="text-align:center;">Gap to Passing</th>
          </tr>
        </thead>
        <tbody>
          @foreach($intervention as $row)
          <tr>
            <td style="font-weight:600;color:#1e293b;">
              {{ optional($row->student)->last_name }}, {{ optional($row->student)->first_name }}
            </td>
            <td style="font-size:.84rem;color:#64748b;">{{ optional($row->student)->lrn ?? '—' }}</td>
            <td style="text-align:center;">{{ $row->grade_count }}</td>
            <td style="text-align:center;">
              <span style="font-weight:700;color:#dc2626;font-size:1rem;">{{ number_format($row->average, 2) }}</span>
            </td>
            <td style="text-align:center;font-size:.85rem;color:#ef4444;font-weight:600;">
              {{ number_format($passingGrade - $row->average, 2) }} pts below
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- Satisfactory (passing, not honors) — collapsible summary --}}
@if($satisfactory->isNotEmpty())
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">Satisfactory Standing — {{ $satisfactory->count() }} student(s)</div>
    <span class="enc-card__meta">Average {{ $passingGrade }}–89 · Passed</span>
  </div>
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>LRN</th>
            <th style="text-align:center;">General Average</th>
          </tr>
        </thead>
        <tbody>
          @foreach($satisfactory as $row)
          <tr>
            <td style="font-weight:600;">{{ optional($row->student)->last_name }}, {{ optional($row->student)->first_name }}</td>
            <td style="font-size:.84rem;color:#64748b;">{{ optional($row->student)->lrn ?? '—' }}</td>
            <td style="text-align:center;font-weight:600;color:#0369a1;">{{ number_format($row->average, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@if($total === 0)
<div style="text-align:center;padding:60px;color:#94a3b8;">
  No students with locked grades found in this section.
  Grades must be in <strong>locked</strong> status to appear in aggregate reports.
</div>
@endif

@endif

@endsection
