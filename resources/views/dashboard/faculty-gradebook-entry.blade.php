@extends('layouts.app')
@section('title', ($ss->subject_name ?? 'Gradebook') . ' — Grade Entry')
@section('breadcrumb', 'Gradebook')

@section('content')
<div style="max-width:1080px;">

  {{-- Back link --}}
  <a href="{{ route('faculty.gradebook') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;color:#6366f1;text-decoration:none;margin-bottom:20px;font-weight:600;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
    </svg>
    All Classes
  </a>

  {{-- Header --}}
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
      <h1 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin:0 0 4px;">{{ $ss->subject_name ?? '—' }}</h1>
      <div style="font-size:.875rem;color:#64748b;">
        {{ $ss->section_name ?? 'No Section' }}
        @if($ss->room) &nbsp;·&nbsp; {{ $ss->room }} @endif
        @if($quarter) &nbsp;·&nbsp; <span style="font-weight:600;color:#6366f1;">{{ $quarter->quarter_name }}</span> @endif
      </div>
    </div>

    @if($anyFinalized)
    <span style="padding:.35rem .9rem;background:#dcfce7;color:#166534;border-radius:20px;font-size:.78rem;font-weight:700;">Finalized</span>
    @elseif($allSubmitted)
    <span style="padding:.35rem .9rem;background:#dbeafe;color:#1d4ed8;border-radius:20px;font-size:.78rem;font-weight:700;">Submitted — Awaiting Registrar</span>
    @elseif($enrollments->isEmpty())
    <span style="padding:.35rem .9rem;background:#f1f5f9;color:#64748b;border-radius:20px;font-size:.78rem;font-weight:700;">No Students</span>
    @else
    <span style="padding:.35rem .9rem;background:#fef9c3;color:#713f12;border-radius:20px;font-size:.78rem;font-weight:700;">Draft</span>
    @endif
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#166534;">
    {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#991b1b;">
    {{ session('error') }}
  </div>
  @endif

  @if(!$quarter)
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:40px;text-align:center;">
    <div style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:8px;">No Active Grading Quarter</div>
    <div style="font-size:.85rem;color:#94a3b8;">Ask the admin to activate a grading quarter before entering grades.</div>
  </div>

  @elseif($enrollments->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:40px;text-align:center;">
    <div style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:8px;">No Enrolled Students</div>
    <div style="font-size:.85rem;color:#94a3b8;">No students are currently enrolled in this section.</div>
  </div>

  @else
  {{-- Grade entry form --}}
  <form method="POST" action="{{ route('faculty.gradebook.save-draft', $ss) }}" id="grade-form">
    @csrf

    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">

      {{-- Table --}}
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
          <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
              <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;white-space:nowrap;">#</th>
              <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;white-space:nowrap;">Student</th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">
                Written Work
                <div style="font-size:.68rem;font-weight:500;color:#94a3b8;">30%</div>
              </th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">
                Performance Task
                <div style="font-size:.68rem;font-weight:500;color:#94a3b8;">50%</div>
              </th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">
                Quarterly Assessment
                <div style="font-size:.68rem;font-weight:500;color:#94a3b8;">20%</div>
              </th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Final Grade</th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Descriptor</th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($enrollments as $i => $enrollment)
            @php
              $grade   = $grades->get($enrollment->id);
              $locked  = $grade && in_array($grade->status, ['finalized', 'locked']);
              $student = $enrollment->student;
            @endphp
            <tr class="grade-row" style="border-bottom:1px solid #f1f5f9;"
                data-enrollment="{{ $enrollment->id }}">
              <td style="padding:10px 16px;color:#94a3b8;">{{ $i + 1 }}</td>
              <td style="padding:10px 16px;">
                <div style="font-weight:600;color:#0f172a;">{{ $student?->full_name ?? '—' }}</div>
                @if($student?->lrn)
                <div style="font-size:.72rem;color:#94a3b8;">LRN: {{ $student->lrn }}</div>
                @endif
              </td>

              {{-- Written Work --}}
              <td style="padding:8px 12px;text-align:center;">
                <input type="number" name="grades[{{ $enrollment->id }}][written_work]"
                       class="score-input ww-input"
                       value="{{ old("grades.{$enrollment->id}.written_work", $grade?->written_work) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ $locked ? 'disabled' : '' }}
                       style="width:80px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;text-align:center;{{ $locked ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
              </td>

              {{-- Performance Task --}}
              <td style="padding:8px 12px;text-align:center;">
                <input type="number" name="grades[{{ $enrollment->id }}][performance_task]"
                       class="score-input pt-input"
                       value="{{ old("grades.{$enrollment->id}.performance_task", $grade?->performance_task) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ $locked ? 'disabled' : '' }}
                       style="width:80px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;text-align:center;{{ $locked ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
              </td>

              {{-- Quarterly Assessment --}}
              <td style="padding:8px 12px;text-align:center;">
                <input type="number" name="grades[{{ $enrollment->id }}][quarterly_assessment]"
                       class="score-input qa-input"
                       value="{{ old("grades.{$enrollment->id}.quarterly_assessment", $grade?->quarterly_assessment) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ $locked ? 'disabled' : '' }}
                       style="width:80px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;text-align:center;{{ $locked ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
              </td>

              {{-- Final Grade (live) --}}
              <td style="padding:10px 12px;text-align:center;">
                <span class="final-grade-display" style="font-size:1rem;font-weight:800;color:#0f172a;">
                  {{ $grade?->final_grade !== null ? number_format($grade->final_grade, 2) : '—' }}
                </span>
              </td>

              {{-- Descriptor (live) --}}
              <td style="padding:10px 12px;text-align:center;">
                <span class="descriptor-display" style="font-size:.75rem;font-weight:600;padding:3px 8px;border-radius:6px;
                  {{ $grade?->final_grade !== null
                     ? ($grade->final_grade >= 75 ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;')
                     : 'background:#f1f5f9;color:#94a3b8;' }}">
                  {{ $grade?->descriptor ?? '—' }}
                </span>
              </td>

              {{-- Status --}}
              <td style="padding:10px 12px;text-align:center;">
                @if(!$grade)
                  <span style="font-size:.72rem;color:#94a3b8;">No entry</span>
                @elseif($grade->status === 'locked')
                  <span style="font-size:.72rem;font-weight:700;color:#dc2626;">Locked</span>
                @elseif($grade->status === 'finalized')
                  <span style="font-size:.72rem;font-weight:700;color:#059669;">Finalized</span>
                @elseif($grade->status === 'submitted')
                  <span style="font-size:.72rem;font-weight:700;color:#1d4ed8;">Submitted</span>
                @else
                  <span style="font-size:.72rem;font-weight:700;color:#d97706;">Draft</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Actions --}}
      @if(!$anyFinalized)
      <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-size:.78rem;color:#64748b;">
          {{ $enrollments->count() }} student(s) &nbsp;·&nbsp;
          @if($quarter) {{ $quarter->quarter_name }} @endif
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          @if(!$allSubmitted)
          <button type="submit" form="grade-form"
                  style="padding:.5rem 1.2rem;background:#6366f1;color:#fff;border:none;border-radius:9px;font-size:.84rem;font-weight:700;cursor:pointer;">
            Save Draft
          </button>
          @endif

          @if(!$allSubmitted && $grades->isNotEmpty())
          <button type="submit" form="submit-form"
                  style="padding:.5rem 1.2rem;background:#0f172a;color:#fff;border:none;border-radius:9px;font-size:.84rem;font-weight:700;cursor:pointer;"
                  onclick="return confirm('Submit all draft grades for registrar review? This cannot be undone.')">
            Submit Grades
          </button>
          @endif
        </div>
      </div>
      @endif

      {{-- Unlock request panel (shown only when all grades are locked) --}}
      @if($anyLocked && !$anyFinalized)
      <div style="padding:20px 24px;background:#fef2f2;border-top:1px solid #fecaca;">
        <div style="font-size:.85rem;font-weight:700;color:#991b1b;margin-bottom:10px;">
          Grades are locked — Request an Unlock
        </div>
        <form method="POST" action="{{ route('faculty.gradebook.request-unlock', $ss) }}">
          @csrf
          <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
              <textarea name="reason" rows="2" required minlength="10" maxlength="1000"
                        placeholder="Explain why you need to edit these grades..."
                        style="width:100%;padding:8px 10px;border:1px solid #fca5a5;border-radius:8px;font-size:.83rem;resize:vertical;box-sizing:border-box;">{{ old('reason') }}</textarea>
              @error('reason')
              <div style="font-size:.75rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>
              @enderror
            </div>
            <button type="submit"
                    style="padding:.5rem 1.1rem;background:#dc2626;color:#fff;border:none;border-radius:9px;font-size:.83rem;font-weight:700;cursor:pointer;white-space:nowrap;">
              Submit Unlock Request
            </button>
          </div>
        </form>
      </div>
      @endif
    </div>
  </form>

  {{-- Hidden submit form --}}
  <form id="submit-form" method="POST" action="{{ route('faculty.gradebook.submit', $ss) }}" style="display:none;">
    @csrf
  </form>
  @endif

</div>

<script>
(function () {
  const descriptors = [
    { min: 90, max: 100, label: 'Outstanding',           pass: true },
    { min: 85, max:  89, label: 'Very Satisfactory',     pass: true },
    { min: 80, max:  84, label: 'Satisfactory',          pass: true },
    { min: 75, max:  79, label: 'Fairly Satisfactory',   pass: true },
    { min:  0, max:  74, label: 'Did Not Meet Expectations', pass: false },
  ];

  function getDescriptor(rounded) {
    for (const d of descriptors) {
      if (rounded >= d.min && rounded <= d.max) return d;
    }
    return null;
  }

  function recalcRow(row) {
    const ww = parseFloat(row.querySelector('.ww-input')?.value);
    const pt = parseFloat(row.querySelector('.pt-input')?.value);
    const qa = parseFloat(row.querySelector('.qa-input')?.value);

    const finalDisplay  = row.querySelector('.final-grade-display');
    const descDisplay   = row.querySelector('.descriptor-display');

    if (!finalDisplay || !descDisplay) return;

    if (isNaN(ww) || isNaN(pt) || isNaN(qa)) {
      finalDisplay.textContent = '—';
      descDisplay.textContent  = '—';
      descDisplay.style.background = '#f1f5f9';
      descDisplay.style.color      = '#94a3b8';
      return;
    }

    const final   = Math.round((ww * 0.30) + (pt * 0.50) + (qa * 0.20));
    const clamped = Math.min(100, Math.max(0, final));
    const desc    = getDescriptor(clamped);

    finalDisplay.textContent = clamped.toFixed(2);

    if (desc) {
      descDisplay.textContent     = desc.label;
      descDisplay.style.background = desc.pass ? '#dcfce7' : '#fee2e2';
      descDisplay.style.color      = desc.pass ? '#166534' : '#991b1b';
    } else {
      descDisplay.textContent      = '—';
      descDisplay.style.background = '#f1f5f9';
      descDisplay.style.color      = '#94a3b8';
    }
  }

  document.querySelectorAll('.grade-row').forEach(function (row) {
    // initial calc for rows with pre-filled values
    recalcRow(row);

    row.querySelectorAll('.score-input').forEach(function (input) {
      input.addEventListener('input', function () { recalcRow(row); });
    });
  });
})();
</script>
@endsection
