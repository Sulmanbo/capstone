@extends('layouts.app')
@section('title', 'Bulk Student Import')
@section('breadcrumb', 'Import Students')

@section('content')
<div style="max-width:860px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Bulk Student Import</h1>
    <p style="font-size:.875rem;color:#94a3b8;margin:0;">Upload a CSV file to enroll multiple students at once.</p>
  </div>

  {{-- ── Results panel ──────────────────────────────────────────────────── --}}
  @if(isset($imported))
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:20px 24px;margin-bottom:24px;">
    <div style="font-size:.95rem;font-weight:700;color:#166534;margin-bottom:6px;">
      Import complete — {{ $imported }} student(s) created@if($skipped > 0), {{ $skipped }} skipped@endif.
    </div>
    @if(!empty($errors))
    <div style="margin-top:12px;">
      <div style="font-size:.8rem;font-weight:700;color:#374151;margin-bottom:8px;">Skipped rows:</div>
      <ul style="margin:0;padding-left:18px;font-size:.8rem;color:#64748b;line-height:1.8;">
        @foreach($errors as $err)
        <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
    @endif
  </div>
  @endif

  @error('csv_file')
  <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#991b1b;">
    {{ $message }}
  </div>
  @enderror

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

    {{-- ── Upload form ─────────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
      <div style="font-size:.88rem;font-weight:700;color:#374151;margin-bottom:16px;">Upload CSV</div>

      <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data">
        @csrf

        <label style="display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:6px;">
          CSV File <span style="color:#dc2626;">*</span>
        </label>
        <input type="file" name="csv_file" accept=".csv,.txt" required
               style="display:block;width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px;font-size:.84rem;margin-bottom:16px;box-sizing:border-box;">

        <button type="submit"
                style="width:100%;padding:.6rem;background:#6366f1;color:#fff;border:none;border-radius:9px;font-size:.88rem;font-weight:700;cursor:pointer;">
          Import Students
        </button>
      </form>

      <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;">
        <a href="{{ route('admin.students.import.template') }}"
           style="font-size:.78rem;color:#6366f1;font-weight:600;text-decoration:none;">
          ↓ Download CSV Template
        </a>
      </div>
    </div>

    {{-- ── Format guide ─────────────────────────────────────────────────── --}}
    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
      <div style="font-size:.88rem;font-weight:700;color:#374151;margin-bottom:12px;">CSV Format</div>

      <div style="font-size:.76rem;color:#374151;line-height:1.7;margin-bottom:14px;">
        <strong>Required columns</strong> (must be in header row):
      </div>
      <table style="width:100%;font-size:.75rem;border-collapse:collapse;margin-bottom:14px;">
        <thead>
          <tr style="background:#e2e8f0;">
            <th style="padding:5px 8px;text-align:left;font-weight:700;">Column</th>
            <th style="padding:5px 8px;text-align:left;font-weight:700;">Rules</th>
          </tr>
        </thead>
        <tbody>
          @foreach([
            ['first_name',   'Text, required'],
            ['last_name',    'Text, required'],
            ['email',        'Valid email, unique'],
            ['lrn',          '12 digits, unique'],
            ['grade_level',  'Optional · 7–12'],
            ['section_name', 'Optional · must match existing section'],
            ['gender',       'Optional · male or female'],
            ['phone',        'Optional'],
            ['address',      'Optional'],
          ] as [$col, $rule])
          <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:5px 8px;font-family:monospace;color:#6366f1;">{{ $col }}</td>
            <td style="padding:5px 8px;color:#64748b;">{{ $rule }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div style="font-size:.76rem;color:#64748b;line-height:1.6;">
        <strong>Notes:</strong><br>
        · Temporary password = student's LRN<br>
        · Students must reset password on first login<br>
        · Duplicate LRN or email rows are skipped<br>
        · Max file size: 2 MB (~5,000 students)
      </div>
    </div>

  </div>

</div>
@endsection
