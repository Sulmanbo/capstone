<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SF1 — {{ $section->section_name }}</title>
<style>
body { font-family: Arial, sans-serif; font-size: 10pt; margin: 20px; color: #000; }
h1 { font-size: 13pt; text-align: center; margin: 0; }
h2 { font-size: 11pt; text-align: center; margin: 4px 0 2px; }
.sub { text-align: center; font-size: 9pt; color: #333; margin-bottom: 4px; }
.meta { width: 100%; margin: 10px 0; border-collapse: collapse; }
.meta td { font-size: 9pt; padding: 2px 6px; }
table.list { width: 100%; border-collapse: collapse; margin-top: 12px; }
table.list th { background: #1e3a5f; color: #fff; padding: 6px 8px; font-size: 8.5pt; text-align: left; }
table.list td { padding: 5px 8px; font-size: 8.5pt; border: 1px solid #ccc; }
table.list tr:nth-child(even) td { background: #f5f5f5; }
.divider { border-top: 1px solid #000; margin: 6px 0; }
</style>
</head>
<body>
<h1>Phil. Academy of Sakya</h1>
<h2>SCHOOL FORM 1 — CLASS LIST</h2>
<p class="sub">School Year: {{ $section->academicYear?->year_label }}</p>
<div class="divider"></div>
<table class="meta">
  <tr>
    <td><strong>Grade Level:</strong> {{ $section->grade_level }}</td>
    <td><strong>Section:</strong> {{ $section->section_name }}</td>
    <td><strong>Adviser:</strong> {{ $section->adviser?->first_name }} {{ $section->adviser?->last_name }}</td>
    <td><strong>Total Enrollment:</strong> {{ $students->count() }}</td>
  </tr>
</table>
<div class="divider"></div>
<table class="list">
  <thead>
    <tr>
      <th>#</th>
      <th>LRN</th>
      <th>Last Name</th>
      <th>First Name</th>
      <th>Sex</th>
      <th>Birth Date</th>
      <th>Parent/Guardian</th>
    </tr>
  </thead>
  <tbody>
    @foreach($students as $i => $stu)
    <tr>
      <td>{{ $i+1 }}</td>
      <td>{{ $stu->lrn ?? '' }}</td>
      <td>{{ $stu->last_name }}</td>
      <td>{{ $stu->first_name }}</td>
      <td>{{ strtoupper(substr($stu->gender ?? '', 0, 1)) }}</td>
      <td></td>
      <td>@try{{ $stu->parent_name ? decrypt($stu->parent_name) : '' }}@catch(\Exception $e)@endtry</td>
    </tr>
    @endforeach
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
<p style="font-size:7pt;color:#555;text-align:right;margin-top:16px;">Generated: {{ now()->format('F d, Y') }}</p>
</body>
</html>
