@extends('layouts.app')
@section('title', 'Add Academic Year')
@section('breadcrumb', 'Add Academic Year')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Add Academic Year</h1>
      <p class="enc-page__subtitle">Create a new institutional academic cycle. You can later toggle it active/inactive from the list page.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.academic-years.index') }}" style="background:#0f172a;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">← Back</a>
    </div>
  </div>
</div>

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

<div class="enc-card" style="max-width:720px;">
  <form method="POST" action="{{ route('admin.academic-years.store') }}">
    @csrf

    <div class="enc-card__header">
      <div class="enc-card__title">Academic Year Details</div>
    </div>

    <div class="enc-card__body" style="padding:24px;display:flex;flex-direction:column;gap:18px;">

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
          Year Label <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" name="year_label" required maxlength="50"
               placeholder="e.g. 2025-2026"
               value="{{ old('year_label') }}"
               style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('year_label') ? '#fca5a5' : '#cbd5e1' }};border-radius:8px;background:#fff;font-size:.9rem;">
        @error('year_label')
          <p style="font-size:.78rem;color:#dc2626;margin:6px 0 0;">{{ $message }}</p>
        @enderror
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
            Start Date <span style="color:#dc2626;">*</span>
          </label>
          <input type="date" name="start_date" required
                 value="{{ old('start_date') }}"
                 style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('start_date') ? '#fca5a5' : '#cbd5e1' }};border-radius:8px;background:#fff;font-size:.9rem;">
          @error('start_date')
            <p style="font-size:.78rem;color:#dc2626;margin:6px 0 0;">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
            End Date <span style="color:#dc2626;">*</span>
          </label>
          <input type="date" name="end_date" required
                 value="{{ old('end_date') }}"
                 style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('end_date') ? '#fca5a5' : '#cbd5e1' }};border-radius:8px;background:#fff;font-size:.9rem;">
          @error('end_date')
            <p style="font-size:.78rem;color:#dc2626;margin:6px 0 0;">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
            Term Type <span style="color:#dc2626;">*</span>
          </label>
          <select name="term_type" id="term_type" required onchange="updateTermPreview()"
                  style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('term_type') ? '#fca5a5' : '#cbd5e1' }};border-radius:8px;background:#fff;font-size:.9rem;">
            <option value="quarterly" {{ old('term_type', 'quarterly') === 'quarterly' ? 'selected' : '' }}>Quarterly (4 grading periods)</option>
            <option value="semestral" {{ old('term_type') === 'semestral' ? 'selected' : '' }}>Semestral (2 grading periods)</option>
          </select>
          @error('term_type')
            <p style="font-size:.78rem;color:#dc2626;margin:6px 0 0;">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
            Status <span style="color:#dc2626;">*</span>
          </label>
          <select name="status" required
                  style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('status') ? '#fca5a5' : '#cbd5e1' }};border-radius:8px;background:#fff;font-size:.9rem;">
            <option value="">— Select Status —</option>
            <option value="active"   {{ old('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
          @error('status')
            <p style="font-size:.78rem;color:#dc2626;margin:6px 0 0;">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Grading-period preview --}}
      <div id="term-preview" style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;font-size:.82rem;color:#166534;">
        <strong>Grading periods that will be auto-created:</strong>
        <div id="term-preview-list" style="margin-top:6px;display:flex;flex-wrap:wrap;gap:8px;"></div>
        <p style="margin:8px 0 0;font-size:.74rem;color:#475569;">You'll set the start/end date of each period under Grading Quarters after saving.</p>
      </div>

      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;font-size:.82rem;color:#1e40af;">
        <strong>Note:</strong> An academic year cannot last more than 1 year. Multiple academic years may be active at the same time so you can prepare next year's schedules while the current year is still in progress.
      </div>
    </div>

    <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
      <a href="{{ route('admin.academic-years.index') }}" style="padding:.6rem 1.4rem;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#475569;text-decoration:none;font-size:.875rem;font-weight:600;">Cancel</a>
      <button type="submit" style="padding:.6rem 1.4rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;">
        Create Academic Year
      </button>
    </div>
  </form>
</div>

<script>
function updateTermPreview() {
  const sel = document.getElementById('term_type');
  const list = document.getElementById('term-preview-list');
  if (!sel || !list) return;

  const periods = sel.value === 'semestral'
    ? ['1st Semester', '2nd Semester']
    : ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];

  list.innerHTML = '';
  periods.forEach(p => {
    const pill = document.createElement('span');
    pill.textContent = p;
    pill.style.cssText = 'display:inline-block;padding:.25rem .7rem;border-radius:6px;font-size:.78rem;font-weight:700;color:#fff;background:#1d4ed8;';
    list.appendChild(pill);
  });
}

// Initialise on page load
updateTermPreview();

// Client-side hint: cap end_date 1 year after start_date
(function() {
  const start = document.querySelector('input[name="start_date"]');
  const end   = document.querySelector('input[name="end_date"]');
  if (!start || !end) return;
  function applyCap() {
    if (!start.value) { end.removeAttribute('max'); return; }
    const d = new Date(start.value);
    d.setFullYear(d.getFullYear() + 1);
    end.max = d.toISOString().slice(0, 10);
  }
  start.addEventListener('change', applyCap);
  applyCap();
})();
</script>

@endsection
