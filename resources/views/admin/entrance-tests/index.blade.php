@extends('layouts.app')

@section('title', 'Entrance Test Results')
@section('breadcrumb', 'Entrance Test Results')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Entrance Test Results</h1>
      <p class="enc-page__subtitle">Record and review admission examination scores.</p>
    </div>
  </div>
</div>

{{-- Summary chips --}}
<div style="display:flex;gap:.65rem;flex-wrap:wrap;margin-bottom:1.25rem;">
  <div style="background:#f1f5f9;color:#374151;padding:.3rem .85rem;border-radius:999px;font-size:.78rem;font-weight:700;">
    Total applicants: {{ $counts['total'] }}
  </div>
  <div style="background:#dbeafe;color:#1e40af;padding:.3rem .85rem;border-radius:999px;font-size:.78rem;font-weight:700;">
    Tested: {{ $counts['tested'] }}
  </div>
  <div style="background:#dcfce7;color:#166534;padding:.3rem .85rem;border-radius:999px;font-size:.78rem;font-weight:700;">
    Passed: {{ $counts['passed'] }}
  </div>
  <div style="background:#fee2e2;color:#991b1b;padding:.3rem .85rem;border-radius:999px;font-size:.78rem;font-weight:700;">
    Failed: {{ $counts['failed'] }}
  </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.entrance-tests.index') }}"
      style="display:flex;gap:.65rem;flex-wrap:wrap;margin-bottom:1.1rem;align-items:flex-end;">
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Result</label>
    <select name="result"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All</option>
      <option value="passed"  {{ request('result') === 'passed'  ? 'selected' : '' }}>Passed</option>
      <option value="failed"  {{ request('result') === 'failed'  ? 'selected' : '' }}>Failed</option>
      <option value="pending" {{ request('result') === 'pending' ? 'selected' : '' }}>Not yet tested</option>
    </select>
  </div>
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Grade</label>
    <select name="grade"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All grades</option>
      @foreach($grades as $g)
      <option value="{{ $g }}" {{ request('grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
      @endforeach
    </select>
  </div>
  <button type="submit"
    style="padding:.5rem 1.1rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.87rem;cursor:pointer;">
    Filter
  </button>
  @if(request()->hasAny(['result','grade']))
  <a href="{{ route('admin.entrance-tests.index') }}"
    style="padding:.5rem .9rem;background:rgba(15,23,42,.07);color:var(--navy);border-radius:8px;font-size:.87rem;text-decoration:none;font-weight:600;">
    Clear
  </a>
  @endif
</form>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">
  {{ session('success') }}
</div>
@endif

<div class="enc-card" style="padding:0;overflow:hidden;">
  @if($applicants->isEmpty())
    <div style="padding:3rem;text-align:center;color:var(--gray-400);font-size:.9rem;">
      No applicants found.
    </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="etbl-th" style="text-align:left;">Applicant</th>
          <th class="etbl-th">Grade</th>
          <th class="etbl-th">Test Date</th>
          <th class="etbl-th">Score</th>
          <th class="etbl-th">Result</th>
          <th class="etbl-th"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($applicants as $a)
        @php $r = $a->entranceTestResult; @endphp
        <tr class="etbl-row">
          <td class="etbl-td">
            <div style="font-weight:700;color:var(--navy);">{{ $a->full_name }}</div>
            <div style="font-size:.76rem;color:var(--gray-400);">{{ $a->reference_number }}</div>
          </td>
          <td class="etbl-td" style="text-align:center;font-size:.85rem;">{{ $a->applying_for_grade }}</td>
          <td class="etbl-td" style="text-align:center;font-size:.82rem;color:var(--gray-500);">
            {{ $r ? $r->test_date->format('M d, Y') : '—' }}
          </td>
          <td class="etbl-td" style="text-align:center;">
            @if($r)
              <span style="font-weight:800;font-size:.95rem;color:var(--navy);">
                {{ number_format($r->total_score, 0) }}
              </span>
              <span style="font-size:.75rem;color:var(--gray-400);">/ {{ number_format($r->max_score, 0) }}</span>
              <div style="font-size:.72rem;color:var(--gray-400);">{{ $r->percentage }}%</div>
            @else
              <span style="color:var(--gray-400);font-size:.82rem;">—</span>
            @endif
          </td>
          <td class="etbl-td" style="text-align:center;">
            @if($r)
              <span class="result-chip {{ $r->passed ? 'chip-pass' : 'chip-fail' }}">
                {{ $r->passed ? 'PASSED' : 'FAILED' }}
              </span>
            @else
              <span class="result-chip chip-pending">Not Tested</span>
            @endif
          </td>
          <td class="etbl-td" style="text-align:right;">
            <a href="{{ route('admin.entrance-tests.create', $a->id) }}"
               style="font-size:.82rem;color:var(--primary);font-weight:700;text-decoration:none;">
              {{ $r ? 'Edit' : 'Record' }} →
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($applicants->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $applicants->links() }}
  </div>
  @endif
  @endif
</div>
@endsection

@push('head')
<style>
.etbl-th { padding:10px 14px; font-size:.73rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid rgba(15,23,42,.08); text-align:center; }
.etbl-td { padding:12px 14px; border-bottom:1px solid rgba(15,23,42,.04); vertical-align:middle; }
.etbl-row:hover td { background:rgba(15,23,42,.015); }
.result-chip { display:inline-block; padding:.22rem .7rem; border-radius:999px; font-size:.72rem; font-weight:800; letter-spacing:.04em; }
.chip-pass    { background:#dcfce7; color:#166534; }
.chip-fail    { background:#fee2e2; color:#991b1b; }
.chip-pending { background:#f1f5f9; color:#64748b; }
</style>
@endpush
