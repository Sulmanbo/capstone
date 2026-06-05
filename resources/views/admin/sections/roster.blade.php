@extends('layouts.app')
@section('title', 'Section Roster')
@section('breadcrumb', 'Sections / Roster')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $section->grade_level }} — {{ $section->section_name }}</h1>
      <p class="enc-page__subtitle">
        {{ $section->academicYear?->year_label ?? 'No academic year' }}
        @if($section->adviser) · Adviser: {{ $section->adviser->last_name }}, {{ $section->adviser->first_name }} @endif
        · Capacity: {{ $section->capacity }}
      </p>
    </div>
    <a href="{{ route('admin.sections.index', ['academic_year_id' => $section->academic_year_id]) }}" class="enc-btn enc-btn--secondary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      Back to Sections
    </a>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-left:4px solid #22c55e;border-radius:10px;color:#166534;font-size:.88rem;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="margin-bottom:20px;padding:12px 16px;background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:10px;color:#991b1b;font-size:.88rem;">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

  {{-- Enrolled students --}}
  <div class="enc-card">
    <div class="enc-card__header">
      <div class="enc-card__title">Enrolled Students</div>
      <div class="enc-card__meta">{{ $enrolled->count() }} / {{ $section->capacity }}</div>
    </div>
    <div class="enc-card__body" style="padding:0;">
      @if($enrolled->isEmpty())
        <div style="padding:36px 20px;text-align:center;color:#94a3b8;font-size:.85rem;">
          No students enrolled yet. Add some from the right →
        </div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <tbody>
            @foreach($enrolled as $en)
            <tr style="border-bottom:1px solid #f1f5f9;">
              <td style="padding:11px 16px;">
                <div style="font-weight:600;color:#0f172a;font-size:.88rem;">{{ $en->student->last_name }}, {{ $en->student->first_name }}</div>
                <div style="font-size:.74rem;color:#94a3b8;">LRN: {{ $en->student->lrn ?? '—' }}</div>
              </td>
              <td style="padding:11px 16px;text-align:right;">
                <form action="{{ route('admin.sections.remove-student', $section) }}" method="POST"
                      onsubmit="return confirm('Remove {{ $en->student->first_name }} from this section?');">
                  @csrf @method('DELETE')
                  <input type="hidden" name="enrollment_id" value="{{ $en->id }}">
                  <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.8rem;font-weight:600;cursor:pointer;">Remove</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>

  {{-- Available students to add --}}
  <div class="enc-card">
    <div class="enc-card__header">
      <div class="enc-card__title">Add Students</div>
      <div class="enc-card__meta">{{ $available->count() }} available</div>
    </div>
    <div class="enc-card__body" style="padding:20px;">
      @if($available->isEmpty())
        <div style="padding:24px 8px;text-align:center;color:#94a3b8;font-size:.85rem;">
          No unassigned students for this academic year.<br>
          <span style="font-size:.78rem;">(All active students are already in a section, or none exist yet.)</span>
        </div>
      @else
        <form action="{{ route('admin.sections.enroll', $section) }}" method="POST">
          @csrf
          <input type="text" id="student-search" placeholder="Search students…" onkeyup="filterStudents()"
                 class="enc-input" style="width:100%;margin-bottom:12px;">

          <div style="max-height:340px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;">
            @foreach($available as $stu)
            <label class="stu-row" style="display:flex;align-items:center;gap:10px;padding:9px 14px;border-bottom:1px solid #f1f5f9;cursor:pointer;"
                   data-name="{{ strtolower($stu->last_name . ' ' . $stu->first_name . ' ' . $stu->lrn) }}">
              <input type="checkbox" name="student_ids[]" value="{{ $stu->id }}" style="width:16px;height:16px;accent-color:var(--navy);">
              <div>
                <div style="font-weight:600;color:#0f172a;font-size:.86rem;">{{ $stu->last_name }}, {{ $stu->first_name }}</div>
                <div style="font-size:.73rem;color:#94a3b8;">LRN: {{ $stu->lrn ?? '—' }}</div>
              </div>
            </label>
            @endforeach
          </div>

          <div style="display:flex;gap:10px;align-items:center;margin-top:14px;">
            <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
              Enroll Selected
            </button>
            <button type="button" onclick="toggleAll()" class="enc-btn enc-btn--secondary enc-btn--sm">Select all</button>
          </div>
        </form>
      @endif
    </div>
  </div>

</div>

@push('scripts')
<script>
function filterStudents() {
  const q = document.getElementById('student-search').value.toLowerCase();
  document.querySelectorAll('.stu-row').forEach(function (row) {
    row.style.display = row.dataset.name.includes(q) ? '' : 'none';
  });
}
let allOn = false;
function toggleAll() {
  allOn = !allOn;
  document.querySelectorAll('.stu-row').forEach(function (row) {
    if (row.style.display !== 'none') row.querySelector('input[type=checkbox]').checked = allOn;
  });
}
</script>
@endpush
@endsection
