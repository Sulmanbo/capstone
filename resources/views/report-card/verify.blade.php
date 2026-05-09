<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Card Verification — EncryptEd</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,.1); max-width: 680px; width: 100%; overflow: hidden; }
    .banner { padding: 28px 32px; display: flex; align-items: center; gap: 18px; }
    .banner.ok  { background: linear-gradient(135deg, #059669, #10b981); }
    .banner.bad { background: linear-gradient(135deg, #dc2626, #f87171); }
    .banner-icon { width: 52px; height: 52px; background: rgba(255,255,255,.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.6rem; }
    .banner-text .title { font-size: 1.1rem; font-weight: 800; color: #fff; }
    .banner-text .sub   { font-size: .85rem; color: rgba(255,255,255,.85); margin-top: 3px; }
    .body { padding: 28px 32px; }
    .section-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 10px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
    .info-item { background: #f8fafc; border-radius: 10px; padding: 12px 14px; }
    .info-item .label { font-size: .72rem; font-weight: 700; color: #94a3b8; margin-bottom: 4px; }
    .info-item .value { font-size: .92rem; font-weight: 700; color: #0f172a; }
    table.grades { width: 100%; border-collapse: collapse; font-size: .84rem; margin-bottom: 24px; }
    table.grades th { background: #f1f5f9; padding: 8px 12px; text-align: center; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; }
    table.grades th.left { text-align: left; }
    table.grades td { padding: 7px 12px; border-bottom: 1px solid #f1f5f9; text-align: center; }
    table.grades td.left { text-align: left; font-weight: 600; }
    .pass { color: #059669; font-weight: 700; } .fail { color: #dc2626; font-weight: 700; }
    .token-box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 16px; font-size: .78rem; color: #64748b; word-break: break-all; }
    .school-badge { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; font-size: .78rem; color: #94a3b8; }
    .divider { border: none; border-top: 1px solid #f1f5f9; margin: 20px 0; }
  </style>
</head>
<body>
<div class="card">

  {{-- Verification banner --}}
  <div class="banner {{ $intact ? 'ok' : 'bad' }}">
    <div class="banner-icon">{{ $intact ? '✓' : '⚠' }}</div>
    <div class="banner-text">
      @if($intact)
        <div class="title">Document Verified — Authentic</div>
        <div class="sub">The grades in this report card have not been altered since it was issued.</div>
      @else
        <div class="title">Integrity Warning</div>
        <div class="sub">The grade data in our system does not match what was on the PDF when it was issued. This document may have been tampered with.</div>
      @endif
    </div>
  </div>

  <div class="body">

    {{-- Student info --}}
    <div class="section-title">Student Information</div>
    <div class="info-grid">
      <div class="info-item">
        <div class="label">Full Name</div>
        <div class="value">{{ $record->student?->full_name ?? '—' }}</div>
      </div>
      <div class="info-item">
        <div class="label">LRN</div>
        <div class="value">{{ $record->student?->lrn ?? '—' }}</div>
      </div>
      <div class="info-item">
        <div class="label">Academic Year</div>
        <div class="value">{{ $record->academicYear?->year_label ?? '—' }}</div>
      </div>
      <div class="info-item">
        <div class="label">Section</div>
        <div class="value">{{ $data['enrollment']?->section?->section_name ?? '—' }}</div>
      </div>
    </div>

    {{-- Grades table --}}
    @if(!empty($data['rows']))
    <div class="section-title">Grade Records</div>
    <table class="grades">
      <thead>
        <tr>
          <th class="left">Learning Area</th>
          @foreach($data['quarters'] as $q)
          <th>Q{{ $q->quarter_number }}</th>
          @endforeach
          <th>Average</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data['rows'] as $subject => $info)
        <tr>
          <td class="left">{{ $subject }}</td>
          @foreach($data['quarters'] as $q)
          @php $g = $info['quarters'][$q->quarter_number] ?? null; @endphp
          <td>{{ $g ? number_format($g['final_grade'], 0) : '—' }}</td>
          @endforeach
          <td><strong>{{ $info['average'] ? number_format($info['average'], 2) : '—' }}</strong></td>
          <td class="{{ ($info['average'] ?? 0) >= 75 ? 'pass' : 'fail' }}">
            {{ ($info['average'] ?? 0) >= 75 ? 'Passed' : 'Failed' }}
          </td>
        </tr>
        @endforeach
        <tr style="background:#f1f5f9;">
          <td class="left" colspan="{{ $data['quarters']->count() + 1 }}" style="font-weight:800;">GENERAL AVERAGE</td>
          <td style="font-weight:900;font-size:1rem;">{{ $data['overall'] ? number_format($data['overall'], 2) : '—' }}</td>
          <td class="{{ ($data['overall'] ?? 0) >= 75 ? 'pass' : 'fail' }}">
            {{ ($data['overall'] ?? 0) >= 75 ? 'PROMOTED' : 'RETAINED' }}
          </td>
        </tr>
      </tbody>
    </table>
    @else
    <p style="color:#94a3b8;font-size:.85rem;margin-bottom:20px;">No grade records found.</p>
    @endif

    <hr class="divider">

    {{-- Token info --}}
    <div class="section-title">Verification Token</div>
    <div class="token-box">
      <strong>Token:</strong> {{ $record->token }}<br>
      <strong>Issued:</strong> {{ $record->generated_at->format('F d, Y \a\t h:i A') }}<br>
      <strong>Issued by:</strong> {{ $record->generatedBy?->full_name ?? 'System' }}<br>
      <strong>Data hash:</strong> {{ $record->data_hash }}
    </div>

    <div class="school-badge">
      <span>EncryptEd Academic Management System</span>
      <span>·</span>
      <span>Department of Education Compliant</span>
    </div>

  </div>
</div>
</body>
</html>
