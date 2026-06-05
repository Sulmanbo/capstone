<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SF2 — {{ $section->section_name }} {{ \Carbon\Carbon::create($year,$month)->format('M Y') }}</title>
<style>
body { font-family: Arial, sans-serif; font-size: 8pt; margin: 14px; color: #000; }
h1 { font-size: 12pt; text-align: center; margin: 0; }
h2 { font-size: 10pt; text-align: center; margin: 3px 0; }
.sub { text-align: center; font-size: 8.5pt; margin-bottom: 4px; }
.divider { border-top: 1px solid #000; margin: 5px 0; }
.meta td { font-size: 8pt; padding: 2px 5px; }
table.att { width: 100%; border-collapse: collapse; margin-top: 10px; }
table.att th { background: #065f46; color: #fff; padding: 5px 3px; font-size: 7.5pt; text-align: center; border: 1px solid #aaa; }
table.att th.name { text-align: left; min-width: 130px; }
table.att td { padding: 4px 3px; font-size: 7.5pt; border: 1px solid #ccc; text-align: center; }
table.att td.name { text-align: left; }
table.att tr:nth-child(even) td { background: #f5f5f5; }
.P { color: green; font-weight: bold; }
.A { color: red; font-weight: bold; }
.L { color: orange; font-weight: bold; }
.E { color: purple; font-weight: bold; }
</style>
</head>
<body>
<h1>Phil. Academy of Sakya</h1>
<h2>SCHOOL FORM 2 — DAILY ATTENDANCE REGISTER</h2>
<p class="sub">{{ \Carbon\Carbon::create($year,$month,1)->format('F Y') }}</p>
<div class="divider"></div>
<table class="meta" style="width:100%;border-collapse:collapse;">
  <tr>
    <td><strong>Grade Level:</strong> {{ $section->grade_level }}</td>
    <td><strong>Section:</strong> {{ $section->section_name }}</td>
    <td><strong>Adviser:</strong> {{ $section->adviser?->first_name }} {{ $section->adviser?->last_name }}</td>
    <td><strong>Total:</strong> {{ $students->count() }}</td>
  </tr>
</table>
<table class="att">
  <thead>
    <tr>
      <th class="name">#  Name</th>
      @for($d=1;$d<=$daysInMonth;$d++)<th>{{ $d }}</th>@endfor
      <th>P</th><th>A</th><th>L</th>
    </tr>
  </thead>
  <tbody>
    @foreach($students as $i => $stu)
    @php
      $stuAtt = $attendance->get($stu->id, collect());
      $p=0;$a=0;$l=0;
    @endphp
    <tr>
      <td class="name">{{ $i+1 }}. {{ $stu->last_name }}, {{ $stu->first_name }}</td>
      @for($d=1;$d<=$daysInMonth;$d++)
        @php
          $ds=sprintf('%04d-%02d-%02d',$year,$month,$d);
          $rec=$stuAtt->first(fn($r)=>\Carbon\Carbon::parse($r->date)->format('Y-m-d')===$ds);
          $st=$rec?->status??'';
          if($st==='present')$p++;elseif($st==='absent')$a++;elseif($st==='late')$l++;
        @endphp
        <td class="{{ strtoupper(substr($st,0,1)) }}">{{ $st?strtoupper(substr($st,0,1)):'' }}</td>
      @endfor
      <td class="P">{{ $p }}</td>
      <td class="A">{{ $a }}</td>
      <td class="L">{{ $l }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<p style="font-size:6.5pt;color:#555;text-align:right;margin-top:10px;">Generated: {{ now()->format('F d, Y') }}</p>
</body>
</html>
