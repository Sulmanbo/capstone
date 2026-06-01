{{-- Shared form partial for Schedule create/edit --}}
{{-- Required compact variables: $academicYears, $sections, $classrooms, $faculty, $yearId, $schedule (nullable) --}}

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  <strong>Cannot save:</strong>
  @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

<div class="enc-card" style="max-width:800px;">
  <form method="POST" action="{{ $action }}">
    @csrf
    @if($method === 'PUT')@method('PUT')@endif

    <div class="enc-card__header">
      <div class="enc-card__title">{{ $formTitle }}</div>
    </div>

    <div class="enc-card__body" style="padding:24px;display:flex;flex-direction:column;gap:18px;">

      {{-- Step 1: Academic Year (TOP per adviser) --}}
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
          1. Academic Year *
        </label>
        <select name="academic_year_id" id="academic_year_id" required onchange="reloadForYear()"
                style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— Select Academic Year —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} ({{ ucfirst($yr->status) }}, {{ ucfirst($yr->term_type ?? 'quarterly') }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        {{-- Step 2: Section --}}
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">2. Section *</label>
          <select name="section_id" id="section_id" required onchange="loadSubjectsForSection(this.value)"
                  {{ $sections->isEmpty() ? 'disabled' : '' }}
                  style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
            <option value="">— Select Section —</option>
            @foreach($sections as $s)
              <option value="{{ $s->id }}" data-grade="{{ $s->grade_level }}" {{ old('section_id', $schedule?->section_id) == $s->id ? 'selected' : '' }}>{{ $s->grade_level }} — {{ $s->section_name }}</option>
            @endforeach
          </select>
          @if($sections->isEmpty())
            <p style="font-size:.75rem;color:#92400e;margin:6px 0 0;">Pick an academic year first.</p>
          @endif
        </div>

        {{-- Step 3: Subject (cascades from section) --}}
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">3. Subject *</label>
          <select name="subject_id" id="subject_id" required
                  style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
            <option value="">— Select Subject —</option>
            @if(isset($subjects))
              @foreach($subjects as $subj)
                <option value="{{ $subj->id }}" {{ old('subject_id', $schedule?->subject_id) == $subj->id ? 'selected' : '' }}>
                  {{ $subj->subject_code }} — {{ $subj->subject_name }}@if($subj->year_level) ({{ $subj->year_level }})@endif
                </option>
              @endforeach
            @endif
          </select>
          <p style="font-size:.72rem;color:#94a3b8;margin:6px 0 0;">Subjects are filtered by the section's grade level.</p>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        {{-- Step 4: Classroom --}}
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">4. Classroom</label>
          <select name="classroom_id" id="classroom_id" {{ $classrooms->isEmpty() ? 'disabled' : '' }}
                  style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
            <option value="">— No Room —</option>
            @foreach($classrooms as $cr)
              <option value="{{ $cr->id }}" {{ old('classroom_id', $schedule?->classroom_id) == $cr->id ? 'selected' : '' }}>
                {{ $cr->room_name }}@if($cr->building) — {{ $cr->building }}@endif (cap. {{ $cr->capacity }})
              </option>
            @endforeach
          </select>
        </div>

        {{-- Step 5: Faculty (OPTIONAL — TBA allowed per adviser) --}}
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">5. Faculty <span style="color:#92400e;font-weight:normal;text-transform:none;">(optional — leave blank for TBA)</span></label>
          <select name="faculty_id" id="faculty_id"
                  style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
            <option value="">— TBA / Unassigned —</option>
            @foreach($faculty as $f)
              <option value="{{ $f->id }}" {{ old('faculty_id', $schedule?->faculty_id) == $f->id ? 'selected' : '' }}>{{ $f->last_name }}, {{ $f->first_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Days --}}
      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">6. Schedule Days *</label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
          @foreach(['monday'=>'Mon','tuesday'=>'Tue','wednesday'=>'Wed','thursday'=>'Thu','friday'=>'Fri','saturday'=>'Sat'] as $val => $lbl)
            @php
              $currentDays = old('schedule_days', $schedule?->schedule_days ?? []);
              $checked = in_array($val, $currentDays ?? []);
            @endphp
            <label style="cursor:pointer;">
              <input type="checkbox" name="schedule_days[]" value="{{ $val }}" {{ $checked ? 'checked' : '' }} style="display:none;" class="day-cb" onchange="updateDayLabel(this)">
              <span class="day-pill" data-val="{{ $val }}" style="display:inline-block;padding:.45rem .9rem;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;color:{{ $checked ? '#fff' : '#64748b' }};background:{{ $checked ? '#1d4ed8' : '#f8fafc' }};border:1px solid {{ $checked ? '#1d4ed8' : '#e2e8f0' }};">{{ $lbl }}</span>
            </label>
          @endforeach
        </div>
      </div>

      {{-- Time range --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">7. Start Time *</label>
          <input type="time" name="start_time" required
                 value="{{ old('start_time', $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '') }}"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">8. End Time *</label>
          <input type="time" name="end_time" required
                 value="{{ old('end_time', $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '') }}"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
      </div>

      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;font-size:.82rem;color:#1e40af;">
        <strong>Duration rule:</strong> minimum {{ config('academic.schedule_min_hours', 2) }} hours per session (no maximum).
        <br><strong>Conflict checks:</strong> the system will reject the form if the chosen faculty or room is already booked for an overlapping time on any selected day. The same subject cannot be scheduled twice in one section per year.
      </div>
    </div>

    <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
      <a href="{{ route('admin.schedules.index') }}" style="padding:.6rem 1.4rem;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#475569;text-decoration:none;font-size:.875rem;font-weight:600;">Cancel</a>
      <button type="submit" style="padding:.6rem 1.4rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;">
        {{ $submitLabel }}
      </button>
    </div>
  </form>
</div>

<script>
function reloadForYear() {
  const yid = document.getElementById('academic_year_id').value;
  if (yid) {
    window.location.href = '{{ $reloadRoute }}?academic_year_id=' + yid;
  }
}

function loadSubjectsForSection(sectionId) {
  const subjSel = document.getElementById('subject_id');
  if (!sectionId) {
    subjSel.innerHTML = '<option value="">— Select Section first —</option>';
    return;
  }
  fetch('{{ url("/admin/schedules/subjects-for-section") }}/' + sectionId, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(r => r.json())
    .then(rows => {
      subjSel.innerHTML = '<option value="">— Select Subject —</option>';
      rows.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.subject_code + ' — ' + s.subject_name + (s.year_level ? ' (' + s.year_level + ')' : '');
        subjSel.appendChild(opt);
      });
    })
    .catch(() => { subjSel.innerHTML = '<option value="">— Failed to load subjects —</option>'; });
}

function updateDayLabel(cb) {
  const pill = document.querySelector('.day-pill[data-val="' + cb.value + '"]');
  if (!pill) return;
  if (cb.checked) {
    pill.style.color = '#fff';
    pill.style.background = '#1d4ed8';
    pill.style.borderColor = '#1d4ed8';
  } else {
    pill.style.color = '#64748b';
    pill.style.background = '#f8fafc';
    pill.style.borderColor = '#e2e8f0';
  }
}
</script>
