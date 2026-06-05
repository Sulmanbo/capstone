<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SF9 — {{ $student->last_name }}, {{ $student->first_name }}</title>
<style>
body { font-family: Arial, sans-serif; font-size: 10pt; margin: 20px; color: #000; }
h1 { font-size: 13pt; text-align: center; margin: 0; }
h2 { font-size: 11pt; text-align: center; margin: 4px 0 2px; }
.sub { text-align: center; font-size: 9pt; margin-bottom: 4px; }
.divider { border-top: 1px solid #000; margin: 6px 0; }
.info-grid { width: 100%; border-collapse: collapse; margin: 8px 0; }
.info-grid td { padding: 3px 6px; font-size: 9pt; border: 1px solid #ccc; }
.info-grid .label { font-weight: bold; background: #f0f0f0; width: 120px; }
table.grades { width: 100%; border-collapse: collapse; margin-top: 12px; }
table.grades th { background: #1e293b; color: #fff; padding: 6px 8px; font-size: 9pt; text-align: center; }
table.grades th:first-child { text-align: left; }
table.grades td { padding: 5px 8px; font-size: 9pt; border: 1px solid #ccc; text-align: center; }
table.grades td:first-child { text-align: left; }
table.grades tr:nth-child(even) td { background: #f5f5f5; }
.pass { color: green; font-weight: bold; }
.fail { color: red; font-weight: bold; }
.total-row td { font-weight: bold; background: #e8e8e8; }
</style>
</head>
<body>
<h1>Phil. Academy of Sakya</h1>
<h2>SCHOOL FORM 9 — LEARNER'S PROGRESS REPORT CARD</h2>
<p class="sub">{{ $section->academicYear?->year_label }}</p>
<div class="divider"></div>
<table class="info-grid">
  <tr>
    <td class="label">Learner's Name:</td>
    <td colspan="3">{{ $student->last_name }}, {{ $student->first_name }}</td>
  </tr>
  <tr>
    <td class="label">LRN:</td>
    <td>{{ $student->lrn ?? 'N/A' }}</td>
    <td class="label">Grade Level:</td>
    <td>{{ $section->grade_level }}</td>
  </tr>
  <tr>
    <td class="label">Section:</td>
    <td>{{ $section->section_name }}</td>
    <td class="label">Adviser:</td>
    <td>{{ $section->adviser?->first_name }} {{ $section->adviser?->last_name }}</td>
  </tr>
</table>

<table class="grades">
  <thead>
    <tr>
      <th>Learning Area</th>
      @foreach($quarters as $q)<th>Quarter {{ $q->quarter_number }}</th>@endforeach
      <th>Final Grade</th>
      <th>Remarks</th>
    </tr>
  </thead>
  <tbody>
    @foreach($gradeData as $subject => $qGrades)
      @php
        $vals = array_filter(array_values($qGrades));
        $final = count($vals) ? round(array_sum($vals)/count($vals),2) : null;
      @endphp
      <tr>
        <td>{{ $subject }}</td>
        @foreach($quarters as $q)
          @php $g = $qGrades[$q->quarter_number] ?? null; @endphp
          <td class="{{ $g !== null ? ($g>=75?'pass':'fail') : '' }}">{{ $g ?? '—' }}</td>
        @endforeach
        <td class="{{ $final !== null ? ($final>=75?'pass':'fail') : '' }}">{{ $final ?? '—' }}</td>
        <td class="{{ $final !== null ? ($final>=75?'pass':'fail') : '' }}">
          {{ $final !== null ? ($final>=75 ? 'Passed' : 'Failed') : '' }}
        </td>
      </tr>
    @endforeach
    @php
      $generalAvg = collect($gradeData)->map(function($qg) {
        $vals = array_filter(array_values($qg));
        return count($vals) ? array_sum($vals)/count($vals) : null;
      })->filter()->avg();
    @endphp
    <tr class="total-row">
      <td>General Average</td>
      @foreach($quarters as $q)<td></td>@endforeach
      <td class="{{ $generalAvg !== null ? ($generalAvg>=75?'pass':'fail') : '' }}">{{ $generalAvg ? number_format($generalAvg,2) : '—' }}</td>
      <td class="{{ $generalAvg !== null ? ($generalAvg>=75?'pass':'fail') : '' }}">
        {{ $generalAvg ? ($generalAvg>=75?'PROMOTED':'RETAINED') : '' }}
      </td>
    </tr>
  </tbody>
</table>

<br>
<table style="width:100%;margin-top:20px;">
  <tr>
    <td style="text-align:center;width:45%;">
      <div style="margin-top:30px;border-top:1px solid #000;padding-top:4px;font-size:9pt;">Class Adviser</div>
    </td>
    <td style="width:10%;"></td>
    <td style="text-align:center;width:45%;">
      <div style="margin-top:30px;border-top:1px solid #000;padding-top:4px;font-size:9pt;">School Principal</div>
    </td>
  </tr>
</table>
<p style="font-size:7pt;color:#555;text-align:right;margin-top:12px;">Generated: {{ now()->format('F d, Y') }}</p>
</body>
</html>
