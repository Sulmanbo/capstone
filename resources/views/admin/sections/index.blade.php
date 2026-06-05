@extends('layouts.app')
@section('title', 'Sections')
@section('breadcrumb', 'Sections')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Sections</h1>
      <p class="enc-page__subtitle">Manage class sections (e.g. Grade 7 — St. Joseph). Sections are scoped per academic year.</p>
    </div>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">{{ session('success') }}</div>
@endif

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1.4fr;gap:24px;margin-bottom:24px;">

  <div class="enc-card">
    <div class="enc-card__header"><div class="enc-card__title">Filter by Academic Year</div></div>
    <div class="enc-card__body" style="padding:20px;">
      <form method="GET" style="display:flex;flex-direction:column;gap:6px;">
        <label style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()"
                style="padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— Select Year —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} ({{ ucfirst($yr->status) }})
            </option>
          @endforeach
        </select>
      </form>
    </div>
  </div>

  <div class="enc-card">
    <div class="enc-card__header"><div class="enc-card__title">+ Add Section</div></div>
    <div class="enc-card__body" style="padding:20px;">
      <form method="POST" action="{{ route('admin.sections.store') }}" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        @csrf
        <input type="hidden" name="academic_year_id" value="{{ $yearId }}">

        <select name="grade_level" id="grade_level" required onchange="loadSectionNames()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="">— Grade Level —</option>
          @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
            <option value="{{ $gl }}" {{ old('grade_level') === $gl ? 'selected' : '' }}>{{ $gl }}</option>
          @endforeach
        </select>

        <div>
          <select name="section_name" id="section_name_select" required onchange="toggleCustomName()" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
            <option value="">— Select grade level first —</option>
          </select>
          <input type="text" name="section_name_custom" id="section_name_custom" placeholder="Type section name" maxlength="100" style="display:none;width:100%;margin-top:8px;padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
        </div>

        <select name="adviser_id" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="">— Adviser (optional) —</option>
          @foreach($faculty as $f)
            <option value="{{ $f->id }}">{{ $f->last_name }}, {{ $f->first_name }}</option>
          @endforeach
        </select>

        <input type="number" name="capacity" placeholder="Capacity" required min="1" max="200" value="40" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">

        <select name="status" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <button type="submit" {{ !$yearId ? 'disabled' : '' }} style="padding:.5rem 1rem;border:none;border-radius:8px;background:{{ $yearId ? '#1d4ed8' : '#94a3b8' }};color:#fff;font-size:.875rem;font-weight:700;cursor:{{ $yearId ? 'pointer' : 'not-allowed' }};">Add Section</button>

        @if(!$yearId)
          <p style="font-size:.75rem;color:#92400e;margin:0;grid-column:1/-1;">Pick an academic year first.</p>
        @endif
      </form>
    </div>
  </div>
</div>

<div class="enc-card">
  <div class="enc-card__header"><div class="enc-card__title">Sections List</div></div>
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Grade</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Section</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Adviser</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Capacity</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Year</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sections as $s)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;color:#0f172a;font-weight:600;">{{ $s->grade_level }}</td>
            <td style="padding:12px 14px;color:#0f172a;">{{ $s->section_name }}</td>
            <td style="padding:12px 14px;color:#64748b;">
              @if($s->adviser)
                {{ $s->adviser->last_name }}, {{ $s->adviser->first_name }}
              @else
                <span style="color:#94a3b8;font-style:italic;">— None —</span>
              @endif
            </td>
            <td style="padding:12px 14px;color:#64748b;">{{ $s->capacity }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $s->academicYear?->year_label ?? '—' }}</td>
            <td style="padding:12px 14px;">
              @php $c = $s->status === 'active' ? ['#166534','#86efac','#f0fdf4'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $s->status }}</span>
            </td>
            <td style="padding:12px 14px;text-align:right;white-space:nowrap;">
              <a href="{{ route('admin.sections.roster', $s) }}" style="color:#1d4ed8;font-size:.82rem;font-weight:600;text-decoration:none;margin-right:14px;">Manage Students</a>
              <form action="{{ route('admin.sections.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete section \'{{ $s->grade_level }} — {{ $s->section_name }}\'?');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;">No sections in this academic year. Add one above.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="margin-top:16px;">{{ $sections->links() }}</div>

@push('scripts')
<script>
// Suggested section names per grade level (mirrors SectionSeeder).
const SECTION_NAMES = {
  'Grade 7':  ['St. Joseph', 'St. Mary', 'St. Michael', 'St. Peter', 'St. Paul'],
  'Grade 8':  ['Sampaguita', 'Gumamela', 'Rosal', 'Camia', 'Ilang-Ilang'],
  'Grade 9':  ['Emerald', 'Sapphire', 'Ruby', 'Diamond', 'Topaz'],
  'Grade 10': ['Newton', 'Einstein', 'Galileo', 'Darwin', 'Tesla'],
  'Grade 11': ['Mabini', 'Rizal', 'Bonifacio', 'Luna', 'Aguinaldo'],
  'Grade 12': ['Aristotle', 'Plato', 'Socrates', 'Descartes', 'Kant'],
};

function loadSectionNames() {
  const grade = document.getElementById('grade_level').value;
  const sel   = document.getElementById('section_name_select');
  const custom = document.getElementById('section_name_custom');

  custom.style.display = 'none';
  custom.removeAttribute('required');
  sel.setAttribute('name', 'section_name');

  if (!grade) {
    sel.innerHTML = '<option value="">— Select grade level first —</option>';
    return;
  }

  const names = SECTION_NAMES[grade] || [];
  let html = '<option value="">— Select Section —</option>';
  names.forEach(function (n) {
    html += '<option value="' + n + '">' + n + '</option>';
  });
  html += '<option value="__custom__">Other (type a name)…</option>';
  sel.innerHTML = html;
}

function toggleCustomName() {
  const sel    = document.getElementById('section_name_select');
  const custom = document.getElementById('section_name_custom');

  if (sel.value === '__custom__') {
    // Hand the field name over to the text input so it gets submitted.
    sel.removeAttribute('name');
    custom.setAttribute('name', 'section_name');
    custom.setAttribute('required', 'required');
    custom.style.display = 'block';
    custom.focus();
  } else {
    sel.setAttribute('name', 'section_name');
    custom.removeAttribute('name');
    custom.removeAttribute('required');
    custom.style.display = 'none';
  }
}

// Populate on load if a grade level is already chosen (e.g. after a validation error).
document.addEventListener('DOMContentLoaded', loadSectionNames);
</script>
@endpush

@endsection
