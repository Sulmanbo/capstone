@extends('layouts.app')
@section('title', ($ss->subject_name ?? 'Gradebook') . ' — Grade Entry')
@section('breadcrumb', 'Gradebook')

@section('content')
<div style="max-width:1100px;">

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
  @php
    $subjectWeights = $ss->subject?->getGradeWeights() ?? ['ww' => 0.30, 'pt' => 0.50, 'qa' => 0.20];
    $wwPct = round($subjectWeights['ww'] * 100);
    $ptPct = round($subjectWeights['pt'] * 100);
    $qaPct = round($subjectWeights['qa'] * 100);
  @endphp

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
                <div style="font-size:.68rem;font-weight:500;color:#94a3b8;">{{ $wwPct }}%</div>
              </th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">
                Performance Task
                <div style="font-size:.68rem;font-weight:500;color:#94a3b8;">{{ $ptPct }}%</div>
              </th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">
                Quarterly Assessment
                <div style="font-size:.68rem;font-weight:500;color:#94a3b8;">{{ $qaPct }}%</div>
              </th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Final Grade</th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Descriptor</th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Status</th>
              <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;white-space:nowrap;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($enrollments as $i => $enrollment)
            @php
              $grade   = $grades->get($enrollment->id);
              $locked  = $grade && in_array($grade->status, ['finalized', 'locked']);
              $dropped = $grade?->isDropped();
              $student = $enrollment->student;
            @endphp
            <tr class="grade-row" style="border-bottom:1px solid #f1f5f9;{{ $dropped ? 'background:#fef2f2;opacity:.85;' : '' }}"
                data-enrollment="{{ $enrollment->id }}">
              <td style="padding:10px 16px;color:#94a3b8;">{{ $i + 1 }}</td>
              <td style="padding:10px 16px;">
                <div style="font-weight:600;color:{{ $dropped ? '#991b1b' : '#0f172a' }};{{ $dropped ? 'text-decoration:line-through;' : '' }}">
                  {{ $student?->full_name ?? '—' }}
                </div>
                @if($student?->lrn)
                <div style="font-size:.72rem;color:#94a3b8;">LRN: {{ $student->lrn }}</div>
                @endif
                @if($dropped)
                <div style="font-size:.72rem;color:#dc2626;margin-top:2px;">
                  Dropped {{ $grade->dropped_at?->format('M d, Y') }}
                  @if($grade->drop_reason) · {{ Str::limit($grade->drop_reason, 50) }} @endif
                </div>
                @endif
              </td>

              {{-- Written Work --}}
              <td style="padding:8px 12px;text-align:center;">
                <input type="number" name="grades[{{ $enrollment->id }}][written_work]"
                       class="score-input ww-input"
                       value="{{ old("grades.{$enrollment->id}.written_work", $grade?->written_work) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ ($locked || $dropped) ? 'disabled' : '' }}
                       style="width:80px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;text-align:center;{{ ($locked || $dropped) ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
              </td>

              {{-- Performance Task --}}
              <td style="padding:8px 12px;text-align:center;">
                <input type="number" name="grades[{{ $enrollment->id }}][performance_task]"
                       class="score-input pt-input"
                       value="{{ old("grades.{$enrollment->id}.performance_task", $grade?->performance_task) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ ($locked || $dropped) ? 'disabled' : '' }}
                       style="width:80px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;text-align:center;{{ ($locked || $dropped) ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
              </td>

              {{-- Quarterly Assessment --}}
              <td style="padding:8px 12px;text-align:center;">
                <input type="number" name="grades[{{ $enrollment->id }}][quarterly_assessment]"
                       class="score-input qa-input"
                       value="{{ old("grades.{$enrollment->id}.quarterly_assessment", $grade?->quarterly_assessment) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ ($locked || $dropped) ? 'disabled' : '' }}
                       style="width:80px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;text-align:center;{{ ($locked || $dropped) ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
              </td>

              {{-- Final Grade (live) --}}
              <td style="padding:10px 12px;text-align:center;">
                @if($dropped)
                <span style="font-size:.78rem;font-weight:600;color:#dc2626;">Dropped</span>
                @else
                <span class="final-grade-display" style="font-size:1rem;font-weight:800;color:#0f172a;">
                  {{ $grade?->final_grade !== null ? number_format($grade->final_grade, 2) : '—' }}
                </span>
                @endif
              </td>

              {{-- Descriptor (live) --}}
              <td style="padding:10px 12px;text-align:center;">
                @if($dropped)
                <span style="font-size:.75rem;font-weight:600;padding:3px 8px;border-radius:6px;background:#fee2e2;color:#991b1b;">—</span>
                @else
                <span class="descriptor-display" style="font-size:.75rem;font-weight:600;padding:3px 8px;border-radius:6px;
                  {{ $grade?->final_grade !== null
                     ? ($grade->final_grade >= 75 ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;')
                     : 'background:#f1f5f9;color:#94a3b8;' }}">
                  {{ $grade?->descriptor ?? '—' }}
                </span>
                @endif
              </td>

              {{-- Status --}}
              <td style="padding:10px 12px;text-align:center;">
                @if($dropped)
                  <span style="font-size:.72rem;font-weight:700;color:#dc2626;">Dropped</span>
                @elseif(!$grade)
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

              {{-- Drop / Reinstate --}}
              <td style="padding:8px 12px;text-align:center;">
                @if($dropped)
                  {{-- Reinstate --}}
                  <form method="POST" action="{{ route('faculty.gradebook.reinstate', $ss) }}"
                        data-confirm="Reinstate this student? Their grades will become editable again." data-confirm-type="success" data-confirm-title="Reinstate Student" data-confirm-ok="Reinstate">
                    @csrf
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                    <button type="submit"
                            style="padding:.3rem .75rem;background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;border-radius:7px;font-size:.75rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                      Reinstate
                    </button>
                  </form>
                @elseif(!$locked)
                  {{-- Mark as Dropped --}}
                  <button type="button" onclick="toggleDropForm({{ $enrollment->id }})"
                          style="padding:.3rem .75rem;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:7px;font-size:.75rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                    Mark Dropped
                  </button>
                @else
                  <span style="font-size:.72rem;color:#94a3b8;">—</span>
                @endif
              </td>
            </tr>

            {{-- Inline drop reason form (hidden by default) --}}
            @if(!$dropped && !$locked)
            <tr id="drop-form-{{ $enrollment->id }}" style="display:none;background:#fff7ed;">
              <td colspan="9" style="padding:12px 20px;">
                <form method="POST" action="{{ route('faculty.gradebook.drop', $ss) }}"
                      data-confirm="Mark this student as dropped? This is logged and reversible." data-confirm-type="warning" data-confirm-title="Drop Student" data-confirm-ok="Mark as Dropped">
                  @csrf
                  <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                  <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:1;min-width:260px;">
                      <label style="display:block;font-size:.75rem;font-weight:600;color:#92400e;margin-bottom:4px;">
                        Drop Reason <span style="color:#dc2626;">*</span> (min 10 characters)
                      </label>
                      <textarea name="drop_reason" rows="2" required minlength="10" maxlength="500"
                                placeholder="Reason the student is being dropped from this subject..."
                                style="width:100%;padding:7px 10px;border:1px solid #fed7aa;border-radius:8px;font-size:.83rem;resize:vertical;box-sizing:border-box;"></textarea>
                    </div>
                    <div style="display:flex;gap:8px;">
                      <button type="submit"
                              style="padding:.45rem 1rem;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                        Confirm Drop
                      </button>
                      <button type="button" onclick="toggleDropForm({{ $enrollment->id }})"
                              style="padding:.45rem 1rem;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;">
                        Cancel
                      </button>
                    </div>
                  </div>
                </form>
              </td>
            </tr>
            @endif

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
          &nbsp;·&nbsp; Weights: WW {{ $wwPct }}% / PT {{ $ptPct }}% / QA {{ $qaPct }}%
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
                  data-confirm="Submit all draft grades for registrar review? This cannot be undone." data-confirm-type="warning" data-confirm-title="Submit Grades" data-confirm-ok="Submit">
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
  const WW_W = {{ $subjectWeights['ww'] }};
  const PT_W = {{ $subjectWeights['pt'] }};
  const QA_W = {{ $subjectWeights['qa'] }};

  const descriptors = [
    { min: 90, max: 100, label: 'Outstanding',                pass: true  },
    { min: 85, max:  89, label: 'Very Satisfactory',          pass: true  },
    { min: 80, max:  84, label: 'Satisfactory',               pass: true  },
    { min: 75, max:  79, label: 'Fairly Satisfactory',        pass: true  },
    { min:  0, max:  74, label: 'Did Not Meet Expectations',  pass: false },
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

    const finalDisplay = row.querySelector('.final-grade-display');
    const descDisplay  = row.querySelector('.descriptor-display');

    if (!finalDisplay || !descDisplay) return;

    if (isNaN(ww) || isNaN(pt) || isNaN(qa)) {
      finalDisplay.textContent      = '—';
      descDisplay.textContent       = '—';
      descDisplay.style.background  = '#f1f5f9';
      descDisplay.style.color       = '#94a3b8';
      return;
    }

    const final   = Math.round((ww * WW_W) + (pt * PT_W) + (qa * QA_W));
    const clamped = Math.min(100, Math.max(0, final));
    const desc    = getDescriptor(clamped);

    finalDisplay.textContent = clamped.toFixed(2);

    if (desc) {
      descDisplay.textContent      = desc.label;
      descDisplay.style.background = desc.pass ? '#dcfce7' : '#fee2e2';
      descDisplay.style.color      = desc.pass ? '#166534' : '#991b1b';
    } else {
      descDisplay.textContent      = '—';
      descDisplay.style.background = '#f1f5f9';
      descDisplay.style.color      = '#94a3b8';
    }
  }

  document.querySelectorAll('.grade-row').forEach(function (row) {
    recalcRow(row);
    row.querySelectorAll('.score-input').forEach(function (input) {
      input.addEventListener('input', function () { recalcRow(row); });
    });
  });
})();

function toggleDropForm(enrollmentId) {
  const row = document.getElementById('drop-form-' + enrollmentId);
  if (row) row.style.display = row.style.display === 'none' ? '' : 'none';
}
</script>
@endsection
