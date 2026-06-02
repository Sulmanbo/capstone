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
              @php
                $rn = strtolower($cr->room_name);
                $roomType = match(true) {
                    str_contains($rn, 'science lab')                     => 'science',
                    str_contains($rn, 'computer lab')                    => 'computer',
                    str_contains($rn, 'home ec') || $rn === 'workshop'   => 'tle',
                    $rn === 'gymnasium'                                   => 'pe',
                    $rn === 'avr' || $rn === 'library'                   => 'special',
                    default                                               => 'regular',
                };
              @endphp
              <option value="{{ $cr->id }}"
                      data-type="{{ $roomType }}"
                      {{ old('classroom_id', $schedule?->classroom_id) == $cr->id ? 'selected' : '' }}>
                {{ $cr->room_name }}@if($cr->building) — {{ $cr->building }}@endif (cap. {{ $cr->capacity }})
              </option>
            @endforeach
          </select>
          <p id="room-filter-hint" style="font-size:.72rem;color:#94a3b8;margin:5px 0 0;display:none;">
            Showing rooms suitable for the selected subject.
            <a href="#" onclick="clearRoomFilter(event)" style="color:#1d4ed8;">Show all</a>
          </p>
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
          <input type="time" name="start_time" id="start_time" required
                 value="{{ old('start_time', $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '') }}"
                 onchange="autoSetEndTime(this.value)"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">8. End Time *</label>
          <input type="time" name="end_time" id="end_time" required
                 value="{{ old('end_time', $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '') }}"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
      </div>

      {{-- Live conflict status banner --}}
      <div id="conflict-banner" style="display:none;border-radius:8px;padding:12px 16px;font-size:.82rem;"></div>

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

function loadSubjectsForSection(sectionId, preselectId) {
  const subjSel = document.getElementById('subject_id');
  if (!sectionId) {
    subjSel.innerHTML = '<option value="">— Select Section first —</option>';
    return;
  }
  subjSel.innerHTML = '<option value="">Loading subjects…</option>';
  fetch('{{ url("/admin/schedules/subjects-for-section") }}/' + sectionId, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(r => r.json())
    .then(rows => {
      subjSel.innerHTML = '<option value="">— Select Subject —</option>';
      rows.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.dataset.code = s.subject_code;
        opt.textContent = s.subject_code + ' — ' + s.subject_name + (s.year_level ? ' (' + s.year_level + ')' : '');
        if (preselectId && String(preselectId) === String(s.id)) opt.selected = true;
        subjSel.appendChild(opt);
      });
      // Re-apply room filter if a subject is already selected after AJAX reload
      const selected = subjSel.options[subjSel.selectedIndex];
      if (selected && selected.dataset.code) filterRoomsBySubject(selected.dataset.code);
    })
    .catch(() => { subjSel.innerHTML = '<option value="">— Failed to load subjects —</option>'; });
}

// On page load (e.g. after a validation error with old input), if a section is
// already selected, re-fetch its subjects and re-select the previously chosen one
// so the dropdown is never left empty.
document.addEventListener('DOMContentLoaded', function () {
  const sectionSel = document.getElementById('section_id');
  const presetSubject = @json(old('subject_id', $schedule?->subject_id));
  if (sectionSel && sectionSel.value) {
    loadSubjectsForSection(sectionSel.value, presetSubject);
  }
});

// ── Room filter by subject ────────────────────────────────────────────────
// Maps subject code prefixes → allowed room data-type values.
const SUBJECT_ROOM_MAP = {
  SCI:      ['science', 'regular'],
  EARTH:    ['science', 'regular'],
  PE:       ['pe'],
  MAPEH:    ['pe', 'regular'],
  TLE:      ['tle', 'regular'],
  ORALCOM:  ['special', 'regular'],
  CONTEMP:  ['special', 'regular'],
  AVR:      ['special', 'regular'],
  PRACRES:  ['special', 'regular'],
  COMPUTER: ['computer', 'regular'],
};

function getRoomTypesForCode(code) {
  code = (code || '').toUpperCase();
  for (const prefix in SUBJECT_ROOM_MAP) {
    if (code.startsWith(prefix)) return SUBJECT_ROOM_MAP[prefix];
  }
  return ['regular', 'special']; // default: academic subjects get regular + special rooms
}

function filterRoomsBySubject(code) {
  const allowed = getRoomTypesForCode(code);
  const sel = document.getElementById('classroom_id');
  const hint = document.getElementById('room-filter-hint');
  if (!sel) return;

  let hiddenAny = false;
  Array.from(sel.options).forEach(function(opt) {
    if (!opt.value) return; // always keep "— No Room —"
    const type = opt.dataset.type || 'regular';
    const show = allowed.includes(type);
    opt.hidden = !show;
    opt.disabled = !show;
    if (!show) {
      hiddenAny = true;
      if (opt.selected) { sel.value = ''; } // reset if currently selected room is incompatible
    }
  });

  if (hint) hint.style.display = hiddenAny ? '' : 'none';
}

function clearRoomFilter(e) {
  e.preventDefault();
  const sel = document.getElementById('classroom_id');
  const hint = document.getElementById('room-filter-hint');
  Array.from(sel.options).forEach(function(opt) { opt.hidden = false; opt.disabled = false; });
  if (hint) hint.style.display = 'none';
}

// Wire subject dropdown change → room filter
document.addEventListener('DOMContentLoaded', function() {
  const subjSel = document.getElementById('subject_id');
  if (subjSel) {
    subjSel.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      filterRoomsBySubject(opt ? opt.dataset.code || '' : '');
    });
    // Apply on load for edit forms
    const preOpt = subjSel.options[subjSel.selectedIndex];
    if (preOpt && preOpt.dataset.code) filterRoomsBySubject(preOpt.dataset.code);
  }
});

const MIN_HOURS = {{ config('academic.schedule_min_hours', 2) }};
function autoSetEndTime(startVal) {
  if (!startVal) return;
  const [h, m] = startVal.split(':').map(Number);
  const totalMin = h * 60 + m + MIN_HOURS * 60;
  const endH = String(Math.floor(totalMin / 60) % 24).padStart(2, '0');
  const endM = String(totalMin % 60).padStart(2, '0');
  const endEl = document.getElementById('end_time');
  // Only auto-fill if end time is blank or hasn't been manually changed yet
  if (!endEl._manuallySet) endEl.value = endH + ':' + endM;
}
// Track manual edits to end time so auto-fill doesn't overwrite them
document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('end_time').addEventListener('change', function () {
    this._manuallySet = true;
  });
});

// ── Live conflict checker ─────────────────────────────────────────────────
const CONFLICT_URL  = '{{ route("admin.schedules.check-conflict") }}';
const CSRF_TOKEN    = '{{ csrf_token() }}';
const IGNORE_ID     = @json($schedule?->id);

let conflictTimer = null;

function scheduleConflictCheck() {
  clearTimeout(conflictTimer);
  conflictTimer = setTimeout(function () {
    const ayId     = document.getElementById('academic_year_id')?.value;
    const startVal = document.getElementById('start_time')?.value;
    const endVal   = document.getElementById('end_time')?.value;
    const days     = Array.from(document.querySelectorAll('.day-cb:checked')).map(cb => cb.value);
    const roomId   = document.getElementById('classroom_id')?.value;
    const facId    = document.getElementById('faculty_id')?.value;

    // Need at minimum: year, at least one day, both times
    if (!ayId || !startVal || !endVal || days.length === 0) {
      setBanner(null);
      return;
    }

    setBanner('checking');

    const body = new URLSearchParams();
    body.append('_token', CSRF_TOKEN);
    body.append('academic_year_id', ayId);
    body.append('start_time', startVal);
    body.append('end_time', endVal);
    days.forEach(d => body.append('schedule_days[]', d));
    if (roomId) body.append('classroom_id', roomId);
    if (facId)  body.append('faculty_id', facId);
    if (IGNORE_ID) body.append('ignore_id', IGNORE_ID);

    fetch(CONFLICT_URL, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => setBanner(data.conflicts))
      .catch(() => setBanner(null));
  }, 400); // debounce 400ms
}

function setBanner(state) {
  const el = document.getElementById('conflict-banner');
  if (!el) return;

  if (state === null) {
    el.style.display = 'none';
    return;
  }
  if (state === 'checking') {
    el.style.display = '';
    el.style.background = '#f8fafc';
    el.style.border = '1px solid #e2e8f0';
    el.style.color = '#64748b';
    el.innerHTML = '⏳ Checking availability…';
    return;
  }
  if (state.length === 0) {
    el.style.display = '';
    el.style.background = '#f0fdf4';
    el.style.border = '1px solid #86efac';
    el.style.color = '#166534';
    el.innerHTML = '✓ No conflicts — this time slot is available.';
  } else {
    el.style.display = '';
    el.style.background = '#fef2f2';
    el.style.border = '1px solid #fca5a5';
    el.style.color = '#991b1b';
    el.innerHTML = '<strong>⚠ Conflict detected:</strong><ul style="margin:6px 0 0;padding-left:18px;">'
      + state.map(s => '<li>' + s + '</li>').join('')
      + '</ul>';
  }
}

// Attach conflict check to all relevant inputs
document.addEventListener('DOMContentLoaded', function () {
  ['start_time', 'end_time', 'classroom_id', 'faculty_id', 'academic_year_id'].forEach(function (id) {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', scheduleConflictCheck);
  });
  document.querySelectorAll('.day-cb').forEach(function (cb) {
    cb.addEventListener('change', scheduleConflictCheck);
  });
  // Run once on load for edit forms
  scheduleConflictCheck();
});

function updateDayLabel(cb) {
  scheduleConflictCheck();
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
