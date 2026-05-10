@extends('layouts.app')

@section('title', 'Application — ' . $applicant->reference_number)
@section('breadcrumb', 'Applicant Detail')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $applicant->full_name }}</h1>
      <p class="enc-page__subtitle">Ref: {{ $applicant->reference_number }} · Submitted {{ $applicant->created_at->format('M d, Y') }}</p>
    </div>
    <a href="{{ route('admin.applicants.index') }}" class="app-btn app-btn--ghost">← Back to List</a>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">
  {{ session('success') }}
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.25rem;align-items:start;">

  {{-- ── Left: applicant details ──────────────────────────────────── --}}
  <div style="display:grid;gap:1.1rem;">

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Personal Information</div></div>
      <div class="enc-card__body">
        @include('admin.applicants._info-grid', ['rows' => [
          ['Date of Birth', $applicant->date_of_birth->format('F d, Y')],
          ['Sex', $applicant->sex],
          ['LRN', $applicant->lrn ?? '—'],
          ['Nationality', $applicant->nationality ?? '—'],
        ]])
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Address</div></div>
      <div class="enc-card__body" style="font-size:.88rem;color:var(--navy);line-height:1.7;">
        {{ $applicant->address }}<br>
        @if($applicant->barangay) Brgy. {{ $applicant->barangay }}, @endif
        @if($applicant->municipality) {{ $applicant->municipality }}, @endif
        @if($applicant->province) {{ $applicant->province }} @endif
        @if($applicant->zip_code) {{ $applicant->zip_code }} @endif
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Previous School</div></div>
      <div class="enc-card__body">
        @include('admin.applicants._info-grid', ['rows' => [
          ['School', $applicant->previous_school ?? '—'],
          ['Grade Completed', $applicant->previous_grade_level ?? '—'],
          ['School Year', $applicant->school_year_completed ?? '—'],
        ]])
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Parent / Guardian</div></div>
      <div class="enc-card__body">
        @include('admin.applicants._info-grid', ['rows' => [
          ['Name', $applicant->parent_guardian_name],
          ['Relationship', $applicant->relationship],
          ['Contact', $applicant->parent_contact],
          ['Email', $applicant->parent_email ?? '—'],
        ]])
      </div>
    </div>

    {{-- Entrance Test Result --}}
    @php $etr = $applicant->entranceTestResult; @endphp
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header">
        <div class="enc-card__title">Entrance Test</div>
        <a href="{{ route('admin.entrance-tests.create', $applicant->id) }}"
           style="font-size:.78rem;font-weight:700;color:var(--primary);text-decoration:none;">
          {{ $etr ? 'Edit Result' : 'Record Test' }} →
        </a>
      </div>
      <div class="enc-card__body">
        @if($etr)
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
            <span style="display:inline-block;padding:.3rem .9rem;border-radius:999px;font-size:.78rem;font-weight:800;
              background:{{ $etr->passed ? '#dcfce7' : '#fee2e2' }};
              color:{{ $etr->passed ? '#166534' : '#991b1b' }};">
              {{ $etr->passed ? 'PASSED' : 'FAILED' }}
            </span>
            <span style="font-size:.85rem;color:var(--gray-500);">{{ $etr->test_date->format('M d, Y') }}</span>
          </div>
          @include('admin.applicants._info-grid', ['rows' => [
            ['Score',   number_format($etr->total_score, 0) . ' / ' . number_format($etr->max_score, 0) . ' (' . $etr->percentage . '%)'],
            ['Passing', number_format($etr->passing_score, 0)],
          ]])
          @if($etr->notes)
          <div style="margin-top:.6rem;font-size:.82rem;color:var(--gray-500);font-style:italic;">{{ $etr->notes }}</div>
          @endif
        @else
          <div style="color:var(--gray-400);font-size:.88rem;">No entrance test recorded yet.</div>
        @endif
      </div>
    </div>

  </div>

  {{-- ── Right: status panel ───────────────────────────────────────── --}}
  <div style="display:grid;gap:1rem;">

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Application Status</div></div>
      <div class="enc-card__body" style="display:grid;gap:.75rem;">

        <div style="display:flex;align-items:center;gap:.6rem;">
          <span class="status-chip status-{{ $applicant->status }}" style="font-size:.85rem;padding:.3rem .9rem;">
            {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
          </span>
        </div>

        @include('admin.applicants._info-grid', ['rows' => [
          ['Applying For', $applicant->applying_for_grade . ($applicant->applying_for_year ? ' · ' . $applicant->applying_for_year : '')],
          ['Submitted', $applicant->created_at->format('M d, Y h:i A')],
          ['Reviewed By', $applicant->reviewedBy?->full_name ?? '—'],
          ['Reviewed At', $applicant->reviewed_at?->format('M d, Y') ?? '—'],
        ]])

        @if($applicant->remarks)
        <div style="background:#fef9c3;border-radius:8px;padding:.65rem .9rem;font-size:.82rem;color:#854d0e;">
          <strong>Remarks:</strong> {{ $applicant->remarks }}
        </div>
        @endif

      </div>
    </div>

    {{-- Update status form --}}
    @if(in_array(auth()->user()->role_id, ['03','04']))
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Update Status</div></div>
      <div class="enc-card__body">
        <form method="POST" action="{{ route('admin.applicants.update-status', $applicant->id) }}"
              style="display:grid;gap:.75rem;">
          @csrf
          @method('PATCH')

          <div>
            <label class="app-label">New Status</label>
            <select name="status" class="app-input" required>
              @foreach(['pending','under_review','accepted','rejected','enrolled'] as $s)
              <option value="{{ $s }}" {{ $applicant->status === $s ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_',' ',$s)) }}
              </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="app-label">Remarks (optional)</label>
            <textarea name="remarks" class="app-input" rows="3"
              placeholder="Additional notes for this decision…">{{ old('remarks', $applicant->remarks) }}</textarea>
          </div>

          <button type="submit"
            style="padding:.6rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:.88rem;">
            Save Decision
          </button>
        </form>
      </div>
    </div>
    @endif

  </div>
</div>
@endsection

@push('head')
<style>
.status-chip { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.73rem; font-weight:700; }
.status-pending      { background:#fef9c3; color:#854d0e; }
.status-under_review { background:#dbeafe; color:#1e40af; }
.status-accepted     { background:#dcfce7; color:#166534; }
.status-rejected     { background:#fee2e2; color:#991b1b; }
.status-enrolled     { background:#e0f2fe; color:#0369a1; }
.app-label { display:block; font-size:.76rem; font-weight:700; color:var(--gray-500); margin-bottom:.3rem; }
.app-input { width:100%; padding:.55rem .85rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.88rem; background:#fff; color:var(--navy); font-family:inherit; }
.app-input:focus { outline:none; border-color:var(--primary); }
textarea.app-input { resize:vertical; }
.app-btn { display:inline-flex; align-items:center; justify-content:center; padding:.55rem 1.1rem; border-radius:999px; font-weight:700; text-decoration:none; font-size:.87rem; }
.app-btn--ghost { background:rgba(15,23,42,.07); color:var(--navy); }
</style>
@endpush
