@extends('layouts.app')
@section('title', 'Enrollment')
@section('breadcrumb', 'Enrollment')

@section('content')
<div style="max-width:960px;">

  <div style="margin-bottom:28px;">
    <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Enrollment</h1>
    <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Manage student enrollment status for the current academic year.</p>
  </div>

  @if($activeAcademicYear)
  <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:999px;padding:.35rem 1rem;margin-bottom:20px;">
    <div style="width:7px;height:7px;border-radius:50%;background:#10b981;"></div>
    <span style="font-size:.8rem;font-weight:700;color:#059669;">Active Year: {{ $activeAcademicYear->year_label }}</span>
  </div>
  @endif

  {{-- ── Prerequisite Checker ─────────────────────────────────────────────── --}}
  <div class="sd-card" style="padding:1.5rem;margin-bottom:1.25rem;">
    <div style="font-size:.95rem;font-weight:800;color:var(--sd-navy);margin-bottom:.2rem;">Prerequisite Checker</div>
    <div style="font-size:.8rem;color:var(--sd-muted);margin-bottom:1rem;">
      Verify whether a student meets all curriculum prerequisites before enrolling them in a grade level.
    </div>

    <form method="GET" action="{{ route('registrar.enrollment') }}"
          style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;">
      <div>
        <label style="display:block;font-size:.76rem;font-weight:700;color:var(--sd-muted);margin-bottom:.3rem;">Student LRN</label>
        <input type="text" name="check_lrn" value="{{ request('check_lrn') }}"
          placeholder="12-digit LRN"
          style="width:100%;padding:.55rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.88rem;background:#fff;">
      </div>
      <div>
        <label style="display:block;font-size:.76rem;font-weight:700;color:var(--sd-muted);margin-bottom:.3rem;">Target Grade Level</label>
        <select name="check_grade_level"
          style="width:100%;padding:.55rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.88rem;background:#fff;">
          <option value="">— Select —</option>
          @foreach($standardGradeLevels as $lvl)
          <option value="{{ $lvl }}" {{ request('check_grade_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit"
        style="padding:.55rem 1.25rem;background:var(--sd-primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.87rem;cursor:pointer;white-space:nowrap;">
        Check
      </button>
    </form>

    @if(request('check_lrn'))
    <div style="margin-top:1.1rem;border-top:1px solid rgba(15,23,42,.07);padding-top:1rem;">

      @if(!$checkStudent)
        <div style="font-size:.87rem;color:#dc2626;font-weight:600;">
          No student found with LRN "{{ request('check_lrn') }}".
        </div>

      @elseif($unmetPrereqs === null)
        <div style="font-size:.87rem;color:#d97706;font-weight:600;">
          Please select a target grade level.
        </div>

      @elseif(empty($unmetPrereqs))
        <div style="display:flex;align-items:center;gap:.6rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;">
          <span style="font-size:1.1rem;color:#16a34a;">✓</span>
          <div>
            <div style="font-weight:700;color:#166534;font-size:.9rem;">All prerequisites met</div>
            <div style="font-size:.8rem;color:#15803d;">
              {{ $checkStudent->full_name }} ({{ $checkStudent->lrn }}) is eligible for {{ $checkGrade }}.
            </div>
          </div>
        </div>

      @else
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:.85rem 1rem;">
          <div style="font-weight:700;color:#991b1b;font-size:.9rem;margin-bottom:.5rem;">
            ⚠ {{ count($unmetPrereqs) }} unmet prerequisite{{ count($unmetPrereqs) > 1 ? 's' : '' }}
            for {{ $checkStudent->full_name }}
          </div>
          <table style="width:100%;border-collapse:collapse;font-size:.83rem;">
            <thead>
              <tr>
                <th style="text-align:left;padding:5px 8px;color:#7f1d1d;font-weight:700;border-bottom:1px solid #fca5a5;">Subject</th>
                <th style="text-align:left;padding:5px 8px;color:#7f1d1d;font-weight:700;border-bottom:1px solid #fca5a5;">Requires Passing</th>
                <th style="text-align:center;padding:5px 8px;color:#7f1d1d;font-weight:700;border-bottom:1px solid #fca5a5;">Min Grade</th>
                <th style="text-align:center;padding:5px 8px;color:#7f1d1d;font-weight:700;border-bottom:1px solid #fca5a5;">Student's Grade</th>
              </tr>
            </thead>
            <tbody>
              @foreach($unmetPrereqs as $p)
              <tr>
                <td style="padding:5px 8px;color:#991b1b;font-weight:600;">{{ $p['subject'] }}</td>
                <td style="padding:5px 8px;color:#7f1d1d;">{{ $p['requires'] }}</td>
                <td style="padding:5px 8px;text-align:center;color:#7f1d1d;">{{ $p['min_grade'] }}</td>
                <td style="padding:5px 8px;text-align:center;font-weight:700;
                    color:{{ $p['student_grade'] !== null ? '#dc2626' : '#6b7280' }};">
                  {{ $p['student_grade'] !== null ? number_format($p['student_grade'], 0) : 'No record' }}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div style="font-size:.77rem;color:#991b1b;margin-top:.6rem;">
            Registrar may override and proceed, but these holds will appear on the student's Academic Holds page.
          </div>
        </div>
      @endif

    </div>
    @endif
  </div>

  {{-- ── Coming Soon placeholder ──────────────────────────────────────────── --}}
  <div class="sd-card" style="overflow:hidden;margin-bottom:16px;">
    <div style="background:linear-gradient(135deg,#1e3a8a,#312e81,#1e1b4b);padding:40px 32px;text-align:center;">
      <div style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:8px;">
        Enrollment Management — Coming Soon
      </div>
      <div style="font-size:.875rem;color:rgba(255,255,255,.75);max-width:440px;margin:0 auto;line-height:1.6;">
        Full intake processing, section assignment, and certification printing will be available here.
      </div>
    </div>
  </div>

</div>
@endsection
