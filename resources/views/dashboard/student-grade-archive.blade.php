@extends('layouts.app')

@section('title', 'Grade Archive')
@section('breadcrumb', 'Grade Archive')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Grade Archive</h1>
      <p class="enc-page__subtitle">Your finalized grades from previous quarters and academic years.</p>
    </div>
  </div>
</div>

{{-- Year filter --}}
@if($years->isNotEmpty())
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:220px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Filter by Academic Year</label>
        <select name="year" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— All Years —</option>
          @foreach($years as $yr)
            <option value="{{ $yr->id }}" {{ (string)$selectedYearId === (string)$yr->id ? 'selected' : '' }}>{{ $yr->year_label }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>
@endif

{{-- Archive content --}}
@if($archive->isEmpty())
<div class="enc-card">
  <div class="enc-card__body" style="padding:48px 32px;text-align:center;">
    <div style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:8px;">No finalized grades yet</div>
    <p style="font-size:.875rem;color:#64748b;margin:0 auto;max-width:420px;">
      Once your teachers submit and the registrar finalizes your grades, they will appear here for your records.
    </p>
  </div>
</div>
@else
  @foreach($archive as $yearLabel => $quarters)
  <div style="margin-bottom:28px;">
    <h2 style="font-size:1.05rem;font-weight:800;color:#0f172a;margin:0 0 14px;display:flex;align-items:center;gap:10px;">
      <span style="display:inline-block;width:6px;height:22px;background:#1d4ed8;border-radius:3px;"></span>
      Academic Year {{ $yearLabel }}
    </h2>

    @foreach($quarters as $quarterName => $data)
    <div class="enc-card" style="margin-bottom:16px;">
      <div class="enc-card__header" style="display:flex;justify-content:space-between;align-items:center;">
        <div class="enc-card__title">{{ $quarterName }}</div>
        <div style="font-size:.8rem;color:#64748b;">
          Average: <strong style="color:#0f172a;">{{ $data['average'] }}</strong>
        </div>
      </div>
      <div class="enc-card__body" style="padding:0;">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
              <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Subject</th>
                <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Faculty</th>
                <th style="padding:11px 14px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Grade</th>
                <th style="padding:11px 14px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Remark</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data['subjects'] as $subj)
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:11px 14px;color:#0f172a;font-weight:500;">{{ $subj['name'] }}</td>
                <td style="padding:11px 14px;color:#64748b;">{{ $subj['faculty'] }}</td>
                <td style="padding:11px 14px;text-align:center;font-weight:700;color:#0f172a;">{{ $subj['grade'] }}</td>
                <td style="padding:11px 14px;text-align:center;">
                  @php $c = $subj['passing'] ? ['#166534','#86efac','#f0fdf4'] : ['#991b1b','#fca5a5','#fef2f2']; @endphp
                  <span style="display:inline-block;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;font-weight:700;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $subj['remark'] }}</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endforeach
@endif

@endsection
