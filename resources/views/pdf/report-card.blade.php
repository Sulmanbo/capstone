<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #111; }

  /* OFFICIAL watermark — fixed behind all content */
  .watermark {
    position: fixed;
    top: 38%;
    left: 5%;
    width: 90%;
    text-align: center;
    font-size: 80pt;
    font-weight: 900;
    color: rgba(30, 58, 95, 0.07);
    transform: rotate(-35deg);
    letter-spacing: 12px;
    z-index: 0;
    pointer-events: none;
  }

  .page { position: relative; z-index: 1; padding: 18mm 16mm; }

  /* Header */
  .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px; margin-bottom: 12px; }
  .header .school { font-size: 13pt; font-weight: 700; color: #1e3a5f; }
  .header .address { font-size: 8pt; color: #555; margin-top: 2px; }
  .header .doc-title { font-size: 11pt; font-weight: 700; margin-top: 6px; letter-spacing: 1px; text-transform: uppercase; }
  .header .doc-sub { font-size: 8.5pt; color: #444; margin-top: 2px; }

  /* Student info row */
  .info-grid { display: table; width: 100%; margin-bottom: 12px; }
  .info-col { display: table-cell; width: 50%; vertical-align: top; font-size: 9pt; }
  .info-row { margin-bottom: 4px; }
  .info-label { font-weight: 700; color: #555; width: 90px; display: inline-block; }

  /* Grades table */
  table.grades { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 12px; }
  table.grades th {
    background: #1e3a5f; color: #fff; padding: 5px 7px; text-align: center;
    border: 1px solid #1e3a5f; font-size: 8.5pt;
  }
  table.grades th.subject-col { text-align: left; }
  table.grades td { padding: 4px 7px; border: 1px solid #ccc; text-align: center; }
  table.grades td.subject-cell { text-align: left; font-weight: 600; }
  table.grades tr:nth-child(even) td { background: #f5f7fa; }
  table.grades tr.average-row td { font-weight: 700; background: #e8edf3; }
  table.grades tr.overall-row td { font-weight: 900; background: #1e3a5f; color: #fff; }
  .pass { color: #166534; } .fail { color: #991b1b; }

  /* Summary & signature */
  .summary-row { display: table; width: 100%; margin-bottom: 14px; }
  .summary-col { display: table-cell; width: 50%; vertical-align: top; }
  .summary-box { border: 1px solid #ccc; border-radius: 4px; padding: 8px 10px; font-size: 9pt; }
  .summary-box .label { font-size: 7.5pt; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 3px; }
  .summary-box .value { font-size: 11pt; font-weight: 900; color: #1e3a5f; }
  .summary-box .desc { font-size: 8pt; color: #666; margin-top: 2px; }

  .sig-section { display: table; width: 100%; margin-top: 10px; }
  .sig-col { display: table-cell; width: 50%; padding: 0 8px; }
  .sig-line { border-top: 1px solid #333; margin-top: 24px; padding-top: 3px; font-size: 8pt; text-align: center; color: #444; }

  /* QR + footer */
  .footer { display: table; width: 100%; margin-top: 10px; border-top: 1px solid #ccc; padding-top: 8px; }
  .footer-qr { display: table-cell; width: 80px; vertical-align: middle; }
  .footer-qr img { width: 72px; height: 72px; }
  .footer-text { display: table-cell; vertical-align: middle; padding-left: 10px; font-size: 7.5pt; color: #555; }
  .footer-text .verify-url { font-size: 7pt; color: #1e3a5f; word-break: break-all; }
  .footer-meta { display: table-cell; text-align: right; vertical-align: bottom; font-size: 7pt; color: #888; }
</style>
</head>
<body>

<div class="watermark">OFFICIAL</div>

<div class="page">

  {{-- ── School Header ─────────────────────────────────────────────────── --}}
  <div class="header">
    <div class="school">EncryptEd Academy</div>
    <div class="address">Department of Education · Republic of the Philippines</div>
    <div class="doc-title">Learner's Progress Report Card</div>
    <div class="doc-sub">
      {{ $year->year_label }} &nbsp;·&nbsp;
      School Year Report
    </div>
  </div>

  {{-- ── Student Information ───────────────────────────────────────────── --}}
  <div class="info-grid">
    <div class="info-col">
      <div class="info-row"><span class="info-label">Name:</span> {{ $student->full_name }}</div>
      <div class="info-row"><span class="info-label">LRN:</span> {{ $student->lrn ?? '—' }}</div>
      <div class="info-row"><span class="info-label">Grade Level:</span> {{ $enrollment?->section?->grade_level ?? $student->grade_level ?? '—' }}</div>
    </div>
    <div class="info-col">
      <div class="info-row"><span class="info-label">Section:</span> {{ $enrollment?->section?->section_name ?? '—' }}</div>
      <div class="info-row"><span class="info-label">School Year:</span> {{ $year->year_label }}</div>
      <div class="info-row"><span class="info-label">Generated:</span> {{ $generatedAt->format('M d, Y h:i A') }}</div>
    </div>
  </div>

  {{-- ── Grades Table ──────────────────────────────────────────────────── --}}
  @if($rows)
  <table class="grades">
    <thead>
      <tr>
        <th class="subject-col" style="width:32%;">Learning Area</th>
        @foreach($quarters as $q)
        <th>Q{{ $q->quarter_number }}</th>
        @endforeach
        <th>Final<br>Average</th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $subject => $info)
      <tr>
        <td class="subject-cell">{{ $subject }}</td>
        @foreach($quarters as $q)
        @php $g = $info['quarters'][$q->quarter_number] ?? null; @endphp
        <td>{{ $g ? number_format($g['final_grade'], 0) : '—' }}</td>
        @endforeach
        <td><strong>{{ $info['average'] ? number_format($info['average'], 2) : '—' }}</strong></td>
        <td class="{{ ($info['average'] ?? 0) >= 75 ? 'pass' : 'fail' }}">
          {{ ($info['average'] ?? 0) >= 75 ? 'Passed' : 'Failed' }}
        </td>
      </tr>
      @endforeach
      {{-- General Average row --}}
      <tr class="overall-row">
        <td class="subject-cell" colspan="{{ $quarters->count() + 1 }}">GENERAL AVERAGE</td>
        <td>{{ $overall ? number_format($overall, 2) : '—' }}</td>
        <td>{{ ($overall ?? 0) >= 75 ? 'PROMOTED' : 'RETAINED' }}</td>
      </tr>
    </tbody>
  </table>
  @else
  <p style="text-align:center;color:#888;padding:20px 0;">No finalized grades available for this report card.</p>
  @endif

  {{-- ── Summary boxes ────────────────────────────────────────────────── --}}
  <div class="summary-row">
    <div class="summary-col" style="padding-right:8px;">
      <div class="summary-box">
        <div class="label">General Average</div>
        <div class="value">{{ $overall ? number_format($overall, 2) : 'N/A' }}</div>
        <div class="desc">
          @if($overall)
            @php
              $d = [
                [90,100,'Outstanding'],[85,89,'Very Satisfactory'],[80,84,'Satisfactory'],
                [75,79,'Fairly Satisfactory'],[0,74,'Did Not Meet Expectations']
              ];
              $label = 'N/A';
              $r = (int) round($overall);
              foreach ($d as [$min,$max,$lbl]) { if ($r >= $min && $r <= $max) { $label = $lbl; break; } }
            @endphp
            {{ $label }}
          @endif
        </div>
      </div>
    </div>
    <div class="summary-col" style="padding-left:8px;">
      <div class="summary-box">
        <div class="label">Promotion Status</div>
        <div class="value" style="color:{{ ($overall ?? 0) >= 75 ? '#166534' : '#991b1b' }};">
          {{ ($overall ?? 0) >= 75 ? 'PROMOTED' : 'RETAINED' }}
        </div>
        <div class="desc">Based on DepEd Order No. 8 s. 2015</div>
      </div>
    </div>
  </div>

  {{-- ── Signatures ────────────────────────────────────────────────────── --}}
  <div class="sig-section">
    <div class="sig-col">
      <div class="sig-line">Class Adviser</div>
    </div>
    <div class="sig-col">
      <div class="sig-line">School Principal / Head Teacher</div>
    </div>
  </div>

  {{-- ── QR Code + Verification Footer ───────────────────────────────── --}}
  <div class="footer">
    <div class="footer-qr">
      <img src="{{ $qrDataUri }}" alt="Verify QR">
    </div>
    <div class="footer-text">
      <strong style="font-size:8pt;color:#1e3a5f;">Scan to Verify Authenticity</strong><br>
      This document can be verified at:<br>
      <span class="verify-url">{{ $verifyUrl }}</span><br><br>
      Any alteration to this document invalidates its authenticity.
    </div>
    <div class="footer-meta">
      Token: {{ substr($token->token, 0, 16) }}…<br>
      Generated: {{ $generatedAt->format('Y-m-d H:i') }}<br>
      SHA-256 verified
    </div>
  </div>

</div>
</body>
</html>
