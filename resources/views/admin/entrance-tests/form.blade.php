@extends('layouts.app')

@section('title', ($result ? 'Edit' : 'Record') . ' Entrance Test — ' . $applicant->reference_number)
@section('breadcrumb', 'Entrance Test Entry')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $result ? 'Edit' : 'Record' }} Entrance Test</h1>
      <p class="enc-page__subtitle">
        {{ $applicant->full_name }} · {{ $applicant->reference_number }} · {{ $applicant->applying_for_grade }}
      </p>
    </div>
    <a href="{{ route('admin.entrance-tests.index') }}" class="et-btn et-btn--ghost">← Back to List</a>
  </div>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.87rem;color:#991b1b;">
  <strong>Please fix the following:</strong>
  <ul style="margin:.35rem 0 0 1.1rem;">
    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
  </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.entrance-tests.store', $applicant->id) }}">
  @csrf

  <div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem;align-items:start;">

    {{-- ── Left: per-area scores ────────────────────────────────────── --}}
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header">
        <div class="enc-card__title">Per-Area Scores</div>
        <span class="enc-card__meta">Leave blank if not assessed separately</span>
      </div>
      <div class="enc-card__body">

        <table style="width:100%;border-collapse:collapse;font-size:.88rem;">
          <thead>
            <tr>
              <th style="text-align:left;padding:8px 10px;color:var(--gray-500);font-weight:700;border-bottom:1px solid rgba(15,23,42,.08);">Area</th>
              <th style="text-align:center;padding:8px 10px;color:var(--gray-500);font-weight:700;border-bottom:1px solid rgba(15,23,42,.08);width:120px;">Score</th>
              <th style="text-align:center;padding:8px 10px;color:var(--gray-500);font-weight:700;border-bottom:1px solid rgba(15,23,42,.08);width:80px;">/ Max</th>
            </tr>
          </thead>
          <tbody>
            @foreach($testAreas as $key => $label)
            <tr>
              <td style="padding:9px 10px;border-bottom:1px solid rgba(15,23,42,.04);">{{ $label }}</td>
              <td style="padding:9px 10px;border-bottom:1px solid rgba(15,23,42,.04);">
                <input type="number" name="score_{{ $key }}" step="0.01" min="0"
                  value="{{ old("score_{$key}", $result?->scores[$key] ?? '') }}"
                  class="et-input" style="text-align:center;"
                  placeholder="—">
              </td>
              <td style="padding:9px 10px;border-bottom:1px solid rgba(15,23,42,.04);text-align:center;color:var(--gray-400);font-size:.82rem;">
                {{ number_format($result?->getMaxPerArea() ?? 20, 0) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <div style="margin-top:.85rem;padding:.65rem .9rem;background:#f8fafc;border-radius:8px;font-size:.78rem;color:var(--gray-400);line-height:1.6;">
          Per-area scores are optional and used for detailed reporting only.<br>
          The <strong>Total Score</strong> on the right is what determines pass/fail.
        </div>

      </div>
    </div>

    {{-- ── Right: overall result ────────────────────────────────────── --}}
    <div style="display:grid;gap:1rem;">

      <div class="enc-card" style="padding:1.25rem;">
        <div class="enc-card__header"><div class="enc-card__title">Test Details</div></div>
        <div class="enc-card__body" style="display:grid;gap:.85rem;">

          <div>
            <label class="et-label">Test Date <span style="color:#e11d48;">*</span></label>
            <input type="date" name="test_date" class="et-input" required
              value="{{ old('test_date', $result?->test_date?->format('Y-m-d') ?? date('Y-m-d')) }}">
            @error('test_date')<div class="et-err">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="et-label">Total Score <span style="color:#e11d48;">*</span></label>
            <input type="number" name="total_score" class="et-input" required
              step="0.01" min="0"
              value="{{ old('total_score', $result?->total_score ?? '') }}"
              id="totalScore">
            @error('total_score')<div class="et-err">{{ $message }}</div>@enderror
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;">
            <div>
              <label class="et-label">Max Score</label>
              <input type="number" name="max_score" class="et-input"
                step="0.01" min="1"
                value="{{ old('max_score', $result?->max_score ?? 100) }}"
                id="maxScore">
            </div>
            <div>
              <label class="et-label">Passing Score</label>
              <input type="number" name="passing_score" class="et-input"
                step="0.01" min="0"
                value="{{ old('passing_score', $result?->passing_score ?? 75) }}"
                id="passingScore">
            </div>
          </div>

          {{-- Live pass/fail indicator --}}
          <div id="liveResult" style="border-radius:10px;padding:.65rem .9rem;text-align:center;font-weight:800;font-size:.95rem;display:none;"></div>

          <div>
            <label class="et-label">Notes</label>
            <textarea name="notes" class="et-input" rows="3"
              placeholder="Optional remarks…">{{ old('notes', $result?->notes ?? '') }}</textarea>
          </div>

        </div>
      </div>

      {{-- Existing result badge --}}
      @if($result)
      <div style="padding:.85rem 1rem;border-radius:10px;background:{{ $result->passed ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $result->passed ? '#bbf7d0' : '#fca5a5' }};">
        <div style="font-size:.72rem;font-weight:700;color:{{ $result->passed ? '#166534' : '#991b1b' }};text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">Current Result</div>
        <div style="font-size:1.1rem;font-weight:900;color:{{ $result->passed ? '#15803d' : '#dc2626' }};">
          {{ $result->passed ? 'PASSED' : 'FAILED' }}
        </div>
        <div style="font-size:.82rem;color:{{ $result->passed ? '#166534' : '#991b1b' }};margin-top:.15rem;">
          {{ number_format($result->total_score, 0) }} / {{ number_format($result->max_score, 0) }}
          ({{ $result->percentage }}%)
          · Tested {{ $result->test_date->format('M d, Y') }}
        </div>
      </div>
      @endif

      <div style="display:flex;gap:.65rem;">
        <button type="submit" class="et-btn et-btn--primary" style="flex:1;">
          {{ $result ? 'Update Result' : 'Save Result' }}
        </button>
        <a href="{{ route('admin.applicants.show', $applicant->id) }}" class="et-btn et-btn--ghost">
          View Application
        </a>
      </div>

    </div>
  </div>
</form>
@endsection

@push('head')
<style>
.et-label { display:block; font-size:.76rem; font-weight:700; color:var(--gray-500); margin-bottom:.3rem; }
.et-input { width:100%; padding:.55rem .85rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.88rem; background:#fff; color:var(--navy); font-family:inherit; outline:none; }
.et-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.1); }
textarea.et-input { resize:vertical; }
.et-err { font-size:.74rem; color:#dc2626; font-weight:600; margin-top:.2rem; }
.et-btn { display:inline-flex; align-items:center; justify-content:center; padding:.62rem 1.2rem; border-radius:999px; font-weight:700; font-size:.87rem; cursor:pointer; border:none; text-decoration:none; }
.et-btn--primary { background:var(--primary); color:#fff; }
.et-btn--ghost   { background:rgba(15,23,42,.07); color:var(--navy); }
</style>
@endpush

@push('scripts')
<script>
(function () {
  const total   = document.getElementById('totalScore');
  const max     = document.getElementById('maxScore');
  const passing = document.getElementById('passingScore');
  const box     = document.getElementById('liveResult');

  function update() {
    const t = parseFloat(total.value);
    const m = parseFloat(max.value)     || 100;
    const p = parseFloat(passing.value) || 75;
    if (isNaN(t)) { box.style.display = 'none'; return; }
    const passed = t >= p;
    box.style.display = 'block';
    box.style.background  = passed ? '#f0fdf4' : '#fef2f2';
    box.style.border      = '1px solid ' + (passed ? '#bbf7d0' : '#fca5a5');
    box.style.color       = passed ? '#166534' : '#dc2626';
    const pct = m > 0 ? ((t / m) * 100).toFixed(1) : '—';
    box.textContent = (passed ? '✓ PASSED' : '✗ FAILED') + ' — ' + t.toFixed(0) + ' / ' + m.toFixed(0) + ' (' + pct + '%)';
  }

  [total, max, passing].forEach(el => el.addEventListener('input', update));
  update();
})();
</script>
@endpush
