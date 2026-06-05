<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SF10 — {{ $student->last_name }}, {{ $student->first_name }}</title>
<style>
body { font-family: Arial, sans-serif; font-size: 9.5pt; margin: 18px; color: #000; }
h1 { font-size: 13pt; text-align: center; margin: 0; }
h2 { font-size: 11pt; text-align: center; margin: 4px 0 2px; }
.sub { text-align: center; font-size: 9pt; margin-bottom: 4px; }
.divider { border-top: 1px solid #000; margin: 5px 0; }
.info-grid { width: 100%; border-collapse: collapse; margin: 6px 0 14px; }
.info-grid td { padding: 3px 5px; font-size: 9pt; border: 1px solid #ccc; }
.info-grid .label { font-weight: bold; background: #f0f0f0; width: 100px; }
.year-block { margin-bottom: 14px; page-break-inside: avoid; }
.yr-title { background: #7f1d1d; color: #fff; padding: 5px 8px; font-weight: bold; font-size: 9.5pt; }
table.grades { width: 100%; border-collapse: collapse; }
table.grades th { background: #444; color: #fff; padding: 5px 7px; font-size: 8.5pt; text-align: center; }
table.grades th:first-child { text-align: left; }
table.grades td { padding: 4px 7px; font-size: 8.5pt; border: 1px solid #ccc; text-align: center; }
table.grades td:first-child { text-align: left; }
table.grades tr:nth-child(even) td { background: #f5f5f5; }
.pass { color: green; } .fail { color: red; }
</style>
</head>
<body>
<h1>Phil. Academy of Sakya</h1>
<h2>SCHOOL FORM 10 — LEARNER'S PERMANENT ACADEMIC RECORD</h2>
<div class="divider"></div>
<table class="info-grid">
  <tr>
    <td class="label">Learner's Name:</td>
    <td colspan="3"><strong>{{ $student->last_name }}, {{ $student->first_name }}</strong></td>
  </tr>
  <tr>
    <td class="label">LRN:</td>
    <td>{{ $student->lrn ?? 'N/A' }}</td>
    <td class="label">Sex:</td>
    <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
  </tr>
</table>

@foreach($history as $rec)
  @php $enrollment = $rec['enrollment']; $grades = $rec['grades']; @endphp
  @php
    $subjects = $grades->groupBy(fn($g) => $g->sectionSubject?->subject?->subject_name);
    $quarters = $grades->pluck('gradingQuarter')->filter()->unique('quarter_number')->sortBy('quarter_number');
  @endphp
  <div class="year-block">
    <div class="yr-title">
      {{ $enrollment->section?->academicYear?->year_label }} — Grade {{ $enrollment->section?->grade_level }} · {{ $enrollment->section?->section_name }}
    </div>
    @if($grades->isEmpty())
      <p style="padding:6px 8px;font-size:8.5pt;color:#666;">No grades on record for this year.</p>
    @else
    <table class="grades">
      <thead>
        <tr>
          <th>Learning Area</th>
          @foreach($quarters as $q)<th>Q{{ $q->quarter_number }}</th>@endforeach
          <th>Final</th>
        </tr>
      </thead>
      <tbody>
        @foreach($subjects as $subj => $subGrades)
          @php
            $qMap = $subGrades->pluck('final_grade','gradingQuarter.quarter_number');
            $vals = $qMap->filter()->values();
            $final = $vals->isNotEmpty() ? round($vals->avg(),2) : null;
          @endphp
          <tr>
            <td>{{ $subj }}</td>
            @foreach($quarters as $q)
              @php $g = $qMap[$q->quarter_number] ?? null; @endphp
              <td class="{{ $g!==null?($g>=75?'pass':'fail'):'' }}">{{ $g ?? '—' }}</td>
            @endforeach
            <td class="{{ $final!==null?($final>=75?'pass':'fail'):'' }}"><strong>{{ $final ?? '—' }}</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
@endforeach

<table style="width:100%;margin-top:18px;">
  <tr>
    <td style="text-align:center;width:45%;"><div style="margin-top:28px;border-top:1px solid #000;padding-top:4px;font-size:8.5pt;">Registrar</div></td>
    <td style="width:10%;"></td>
    <td style="text-align:center;width:45%;"><div style="margin-top:28px;border-top:1px solid #000;padding-top:4px;font-size:8.5pt;">School Principal</div></td>
  </tr>
</table>
<p style="font-size:7pt;color:#555;text-align:right;margin-top:10px;">Generated: {{ now()->format('F d, Y') }}</p>
</body>
</html>
