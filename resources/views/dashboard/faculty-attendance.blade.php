@extends('layouts.app')
@section('title', 'Attendance')
@section('breadcrumb', 'Attendance')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Attendance</h1>
      <p class="enc-page__subtitle">Mark daily attendance for each of your assigned classes.</p>
    </div>
  </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;font-weight:500;">
  @foreach($errors->all() as $err)
    <div>{{ $err }}</div>
  @endforeach
</div>
@endif

{{-- No assigned classes --}}
@if($allSchedules->isEmpty())
<div class="enc-card">
  <div class="enc-card__body" style="padding:48px 32px;text-align:center;">
    <div style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:8px;">No assigned classes</div>
    <p style="font-size:.875rem;color:#64748b;margin:0 auto;max-width:420px;">
      You don't have any section-subject assignments for the active academic year yet.
      Please contact the Registrar to set up your teaching load.
    </p>
  </div>
</div>
@else

{{-- Class & date picker --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__header">
    <div class="enc-card__title">Select Class &amp; Date</div>
  </div>
  <div class="enc-card__body" style="padding:20px 24px;">
    <form method="GET" action="{{ route('faculty.attendance') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:6px;min-width:280px;flex:1;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Class</label>
        <select name="section_subject_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— Select a class —</option>
          @foreach($allSchedules as $sched)
            <option value="{{ $sched->id }}" {{ optional($selectedSchedule)->id == $sched->id ? 'selected' : '' }}>
              {{ $sched->subject->subject_name ?? $sched->subject_name ?? 'Subject' }}
              — {{ $sched->section->section_name ?? 'No Section' }}
              ({{ $sched->schedule_days_label ?? '' }} {{ $sched->time_range ?? '' }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:6px;min-width:180px;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Date</label>
        <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
               onchange="this.form.submit()"
               style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
      </div>
    </form>
  </div>
</div>

{{-- Roster --}}
@if($selectedSchedule)
  @if($roster->isEmpty())
  <div class="enc-card">
    <div class="enc-card__body" style="padding:40px 32px;text-align:center;">
      <div style="font-size:.95rem;color:#64748b;">
        No students are currently enrolled in this section.
      </div>
    </div>
  </div>
  @else
  <form method="POST" action="{{ route('faculty.attendance.store') }}">
    @csrf
    <input type="hidden" name="section_subject_id" value="{{ $selectedSchedule->id }}">
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="enc-card">
      <div class="enc-card__header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div>
          <div class="enc-card__title">Roster — {{ \Carbon\Carbon::parse($date)->format('F j, Y (l)') }}</div>
          <div style="font-size:.8rem;color:#64748b;margin-top:4px;">
            {{ $roster->count() }} student(s) enrolled
            @php
              $marked = $roster->filter(fn($r) => $r->has_record)->count();
            @endphp
            @if($marked > 0) · {{ $marked }} already marked for this date @endif
          </div>
        </div>

        {{-- Bulk-action helper buttons --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <button type="button" onclick="markAll('present')" style="padding:.4rem .85rem;border:1px solid #86efac;border-radius:8px;background:#f0fdf4;color:#166534;font-size:.8rem;font-weight:600;cursor:pointer;">All Present</button>
          <button type="button" onclick="markAll('absent')"  style="padding:.4rem .85rem;border:1px solid #fca5a5;border-radius:8px;background:#fef2f2;color:#991b1b;font-size:.8rem;font-weight:600;cursor:pointer;">All Absent</button>
        </div>
      </div>

      <div class="enc-card__body" style="padding:0;">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
              <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <th style="padding:12px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Student</th>
                <th style="padding:12px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">LRN</th>
                <th style="padding:12px 16px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                <th style="padding:12px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Remarks (optional)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roster as $i => $row)
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px 16px;color:#0f172a;font-weight:500;">
                  {{ $row->student->last_name }}, {{ $row->student->first_name }}
                  <input type="hidden" name="attendance[{{ $i }}][enrollment_id]" value="{{ $row->enrollment->id }}">
                </td>
                <td style="padding:12px 16px;color:#64748b;font-family:monospace;font-size:.82rem;">
                  {{ $row->student->lrn }}
                </td>
                <td style="padding:12px 16px;text-align:center;">
                  <div style="display:inline-flex;gap:6px;flex-wrap:wrap;justify-content:center;">
                    @foreach(['present' => ['#166534','#86efac','#f0fdf4'], 'absent' => ['#991b1b','#fca5a5','#fef2f2'], 'late' => ['#92400e','#fcd34d','#fffbeb'], 'excused' => ['#1e40af','#93c5fd','#eff6ff']] as $opt => $colors)
                      <label style="cursor:pointer;">
                        <input type="radio" name="attendance[{{ $i }}][status]" value="{{ $opt }}"
                               {{ ($row->status ?? '') === $opt ? 'checked' : '' }}
                               required class="att-status att-status-{{ $i }}"
                               style="display:none;"
                               onchange="updateLabel({{ $i }})">
                        <span class="att-label att-label-{{ $i }}-{{ $opt }}"
                              data-status="{{ $opt }}"
                              style="display:inline-block;padding:.3rem .7rem;border-radius:6px;font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:{{ ($row->status ?? '') === $opt ? $colors[0] : '#94a3b8' }};background:{{ ($row->status ?? '') === $opt ? $colors[2] : '#f8fafc' }};border:1px solid {{ ($row->status ?? '') === $opt ? $colors[1] : '#e2e8f0' }};">{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                </td>
                <td style="padding:12px 16px;">
                  <input type="text" name="attendance[{{ $i }}][remarks]" value="{{ $row->remarks }}"
                         maxlength="255" placeholder="e.g. doctor's note"
                         style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:.82rem;">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
        <button type="submit" style="padding:.6rem 1.4rem;border:none;border-radius:8px;background:#065f46;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;">
          Save Attendance
        </button>
      </div>
    </div>
  </form>

  <script>
    function markAll(status) {
      document.querySelectorAll('.att-status').forEach((el, idx) => {
        // Find each radio in this row's group with the matching value
        const name = el.name;
        const radio = document.querySelector(`input[name="${name}"][value="${status}"]`);
        if (radio) {
          radio.checked = true;
          // Find the row index from the name pattern attendance[N][status]
          const m = name.match(/attendance\[(\d+)\]/);
          if (m) updateLabel(parseInt(m[1], 10));
        }
      });
    }

    function updateLabel(rowIdx) {
      const colors = {
        present: ['#166534','#86efac','#f0fdf4'],
        absent:  ['#991b1b','#fca5a5','#fef2f2'],
        late:    ['#92400e','#fcd34d','#fffbeb'],
        excused: ['#1e40af','#93c5fd','#eff6ff'],
      };
      ['present','absent','late','excused'].forEach(opt => {
        const labels = document.querySelectorAll(`.att-label-${rowIdx}-${opt}`);
        const radio  = document.querySelector(`input[name="attendance[${rowIdx}][status]"][value="${opt}"]`);
        labels.forEach(lbl => {
          if (radio && radio.checked) {
            lbl.style.color      = colors[opt][0];
            lbl.style.background = colors[opt][2];
            lbl.style.borderColor= colors[opt][1];
          } else {
            lbl.style.color      = '#94a3b8';
            lbl.style.background = '#f8fafc';
            lbl.style.borderColor= '#e2e8f0';
          }
        });
      });
    }
  </script>
  @endif
@else
  <div class="enc-card">
    <div class="enc-card__body" style="padding:40px 32px;text-align:center;">
      <div style="font-size:.95rem;color:#64748b;">
        Select a class above to start marking attendance.
      </div>
    </div>
  </div>
@endif

@endif

@endsection
