<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Application — EncryptEd Academy</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; min-height: 100vh; color: #0f172a; }

  .top-bar { background: #1e3a5f; color: #fff; padding: .75rem 1.5rem; display: flex; align-items: center; gap: .75rem; }
  .top-bar .school { font-size: .95rem; font-weight: 800; letter-spacing: .01em; }
  .top-bar .sub    { font-size: .75rem; opacity: .7; }

  .hero { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); padding: 2.5rem 1.5rem; text-align: center; color: #fff; }
  .hero h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: .4rem; }
  .hero p  { font-size: .87rem; opacity: .85; max-width: 520px; margin: 0 auto; }

  .container { max-width: 780px; margin: 0 auto; padding: 2rem 1rem 4rem; }

  .card { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(15,23,42,.07); margin-bottom: 1.25rem; overflow: hidden; }
  .card-header { padding: .85rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e5e7eb; font-size: .82rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .06em; }
  .card-body { padding: 1.25rem; display: grid; gap: 1rem; }

  .grid-2 { grid-template-columns: 1fr 1fr; }
  .grid-3 { grid-template-columns: 1fr 1fr 1fr; }
  @media(max-width:600px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }

  .field label { display: block; font-size: .76rem; font-weight: 700; color: #64748b; margin-bottom: .3rem; }
  .field label .req { color: #e11d48; }
  .field input, .field select, .field textarea {
    width: 100%; padding: .6rem .85rem; border: 1px solid #e2e8f0; border-radius: 8px;
    font-size: .9rem; color: #0f172a; background: #fff; font-family: inherit;
    transition: border-color .15s, box-shadow .15s;
  }
  .field input:focus, .field select:focus, .field textarea:focus {
    outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12);
  }
  .field textarea { resize: vertical; min-height: 70px; }
  .field .hint { font-size: .72rem; color: #94a3b8; margin-top: .25rem; }
  .field .error { font-size: .75rem; color: #dc2626; margin-top: .25rem; font-weight: 600; }

  .error-banner { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; font-size: .87rem; color: #991b1b; }
  .error-banner ul { margin: .4rem 0 0 1.1rem; }

  .submit-row { display: flex; justify-content: flex-end; gap: .75rem; margin-top: .5rem; }
  .btn-submit { padding: .75rem 2rem; background: #2563eb; color: #fff; border: none; border-radius: 999px; font-weight: 700; font-size: .95rem; cursor: pointer; transition: background .15s; }
  .btn-submit:hover { background: #1d4ed8; }

  .disclaimer { font-size: .75rem; color: #94a3b8; text-align: center; margin-top: 1.5rem; line-height: 1.6; }
</style>
</head>
<body>

<div class="top-bar">
  <div>
    <div class="school">EncryptEd Academy</div>
    <div class="sub">Department of Education · Republic of the Philippines</div>
  </div>
</div>

<div class="hero">
  <h1>Online Admission Application</h1>
  <p>Complete the form below to apply for enrollment. All fields marked with <span style="color:#fca5a5;">*</span> are required.</p>
</div>

<div class="container">

  @if($errors->any())
  <div class="error-banner">
    <strong>Please correct the following errors:</strong>
    <ul>
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
  @endif

  <form method="POST" action="{{ route('apply.store') }}" novalidate>
    @csrf

    {{-- ── Personal Information ────────────────────────────────────── --}}
    <div class="card">
      <div class="card-header">Personal Information</div>
      <div class="card-body grid-2">

        <div class="field">
          <label>First Name <span class="req">*</span></label>
          <input type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="100">
          @error('first_name')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" name="last_name" value="{{ old('last_name') }}" required maxlength="100">
          @error('last_name')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Middle Name</label>
          <input type="text" name="middle_name" value="{{ old('middle_name') }}" maxlength="100">
        </div>

        <div class="field">
          <label>Suffix <span style="font-weight:400;color:#94a3b8;">(Jr., Sr., III…)</span></label>
          <input type="text" name="suffix" value="{{ old('suffix') }}" maxlength="20">
        </div>

        <div class="field">
          <label>Date of Birth <span class="req">*</span></label>
          <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
          @error('date_of_birth')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Sex <span class="req">*</span></label>
          <select name="sex" required>
            <option value="">— Select —</option>
            <option value="Male"   {{ old('sex') === 'Male'   ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
          </select>
          @error('sex')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Learner Reference Number (LRN)</label>
          <input type="text" name="lrn" value="{{ old('lrn') }}" maxlength="12" pattern="\d{12}"
            placeholder="12 digits (if already assigned)">
          <div class="hint">Leave blank if not yet assigned.</div>
          @error('lrn')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Nationality</label>
          <input type="text" name="nationality" value="{{ old('nationality', 'Filipino') }}" maxlength="80">
        </div>

      </div>
    </div>

    {{-- ── Address ──────────────────────────────────────────────────── --}}
    <div class="card">
      <div class="card-header">Home Address</div>
      <div class="card-body">

        <div class="field">
          <label>Street / House No. / Purok <span class="req">*</span></label>
          <input type="text" name="address" value="{{ old('address') }}" required maxlength="300">
          @error('address')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="card-body grid-3" style="padding:0;gap:1rem;">
          <div class="field">
            <label>Barangay</label>
            <input type="text" name="barangay" value="{{ old('barangay') }}" maxlength="100">
          </div>
          <div class="field">
            <label>Municipality / City</label>
            <input type="text" name="municipality" value="{{ old('municipality') }}" maxlength="100">
          </div>
          <div class="field">
            <label>Province</label>
            <input type="text" name="province" value="{{ old('province') }}" maxlength="100">
          </div>
        </div>

      </div>
    </div>

    {{-- ── Previous School ──────────────────────────────────────────── --}}
    <div class="card">
      <div class="card-header">Previous School (if any)</div>
      <div class="card-body grid-3">

        <div class="field" style="grid-column:span 2;">
          <label>School Name</label>
          <input type="text" name="previous_school" value="{{ old('previous_school') }}" maxlength="200">
        </div>

        <div class="field">
          <label>Grade Level Completed</label>
          <select name="previous_grade_level">
            <option value="">— Select —</option>
            @foreach($gradeLevels as $lvl)
            <option value="{{ $lvl }}" {{ old('previous_grade_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label>School Year Completed</label>
          <input type="text" name="school_year_completed" value="{{ old('school_year_completed') }}"
            placeholder="e.g. 2024-2025" maxlength="20">
        </div>

      </div>
    </div>

    {{-- ── Applying For ─────────────────────────────────────────────── --}}
    <div class="card">
      <div class="card-header">Applying For</div>
      <div class="card-body grid-2">

        <div class="field">
          <label>Grade Level <span class="req">*</span></label>
          <select name="applying_for_grade" required>
            <option value="">— Select Grade —</option>
            @foreach($gradeLevels as $lvl)
            <option value="{{ $lvl }}" {{ old('applying_for_grade') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
            @endforeach
          </select>
          @error('applying_for_grade')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>School Year</label>
          <input type="text" name="applying_for_year" value="{{ old('applying_for_year') }}"
            placeholder="e.g. 2025-2026" maxlength="20">
        </div>

      </div>
    </div>

    {{-- ── Parent / Guardian ────────────────────────────────────────── --}}
    <div class="card">
      <div class="card-header">Parent / Guardian Information</div>
      <div class="card-body grid-2">

        <div class="field" style="grid-column:span 2;">
          <label>Full Name <span class="req">*</span></label>
          <input type="text" name="parent_guardian_name" value="{{ old('parent_guardian_name') }}" required maxlength="200">
          @error('parent_guardian_name')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Relationship <span class="req">*</span></label>
          <select name="relationship" required>
            <option value="">— Select —</option>
            @foreach(['Mother','Father','Guardian','Grandparent','Sibling','Other'] as $rel)
            <option value="{{ $rel }}" {{ old('relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
            @endforeach
          </select>
          @error('relationship')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Contact Number <span class="req">*</span></label>
          <input type="text" name="parent_contact" value="{{ old('parent_contact') }}" required maxlength="20">
          @error('parent_contact')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Email Address</label>
          <input type="email" name="parent_email" value="{{ old('parent_email') }}" maxlength="180">
          <div class="hint">We'll send updates here if provided.</div>
          @error('parent_email')<div class="error">{{ $message }}</div>@enderror
        </div>

      </div>
    </div>

    <div class="submit-row">
      <button type="submit" class="btn-submit">Submit Application →</button>
    </div>

    <p class="disclaimer">
      By submitting this form you certify that all information provided is true and correct.<br>
      Submission of false information is grounds for disqualification.
    </p>

  </form>
</div>

</body>
</html>
