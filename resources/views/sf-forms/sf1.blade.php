@extends('layouts.app')
@section('title', 'SF1 — Class List')
@section('breadcrumb', 'SF Forms / SF1 Class List')

@push('head')
<style>
.sf-header { background: linear-gradient(135deg,#1e3a5f,#2563eb); border-radius:16px; padding:24px 28px; color:#fff; margin-bottom:22px; }
.sf-header h2 { margin:0 0 4px; font-size:1.2rem; font-weight:800; }
.sf-header p { margin:0; font-size:.85rem; opacity:.8; }
.sf-filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 22px; margin-bottom:22px; display:flex; gap:14px; align-items:flex-end; flex-wrap:wrap; }
.sf-filter-card label { display:block; font-size:.78rem; font-weight:600; color:#475569; margin-bottom:5px; }
.sf-filter-card select { padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:.86rem; background:#f8fafc; min-width:280px; }
.sf-btn { background:#1e3a5f; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; }
.sf-btn-dl { background:#059669; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; text-decoration:none; display:inline-block; }
.sf-table-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.sf-table { width:100%; border-collapse:collapse; }
.sf-table th { padding:10px 16px; background:#1e3a5f; color:#fff; font-size:.75rem; font-weight:700; text-align:left; text-transform:uppercase; }
.sf-table td { padding:11px 16px; font-size:.85rem; color:#334155; border-bottom:1px solid #f1f5f9; }
.sf-table tr:last-child td { border-bottom:none; }
.sf-table tr:nth-child(even) td { background:#f9fafb; }
</style>
@endpush

@section('content')

<div class="sf-header">
  <h2>📋 SF1 — School Form 1: Class List</h2>
  <p>Official DepEd class enrollment list per section.</p>
</div>

<div class="sf-filter-card">
  <form method="GET" action="{{ route('sf.sf1') }}" style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap;width:100%;">
    <div>
      <label>Select Section *</label>
      <select name="section_id" required>
        <option value="">— Choose a section —</option>
        @foreach($sections->groupBy('grade_level') as $grade => $secs)
          <optgroup label="Grade {{ $grade }}">
            @foreach($secs as $sec)
              <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>
                {{ $sec->section_name }} ({{ $sec->academicYear?->year_label }})
              </option>
            @endforeach
          </optgroup>
        @endforeach
      </select>
    </div>
    <button type="submit" class="sf-btn">Load List</button>
    @if($section)
      <a href="{{ route('sf.sf1') }}?section_id={{ $sectionId }}&download=1" class="sf-btn-dl">⬇ Download PDF</a>
    @endif
  </form>
</div>

@if($section)
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 22px;margin-bottom:18px;">
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;flex-wrap:wrap;">
    <div><span style="font-size:.76rem;font-weight:700;color:#64748b;display:block;margin-bottom:3px;">Section</span><span style="font-weight:700;color:#1e293b;">{{ $section->section_name }}</span></div>
    <div><span style="font-size:.76rem;font-weight:700;color:#64748b;display:block;margin-bottom:3px;">Grade Level</span><span style="font-weight:700;color:#1e293b;">{{ $section->grade_level }}</span></div>
    <div><span style="font-size:.76rem;font-weight:700;color:#64748b;display:block;margin-bottom:3px;">Adviser</span><span style="font-weight:700;color:#1e293b;">{{ $section->adviser?->first_name }} {{ $section->adviser?->last_name }}</span></div>
    <div><span style="font-size:.76rem;font-weight:700;color:#64748b;display:block;margin-bottom:3px;">Academic Year</span><span style="font-weight:700;color:#1e293b;">{{ $section->academicYear?->year_label }}</span></div>
    <div><span style="font-size:.76rem;font-weight:700;color:#64748b;display:block;margin-bottom:3px;">Total Enrolled</span><span style="font-weight:700;color:#059669;font-size:1.1rem;">{{ $students->count() }}</span></div>
  </div>
</div>

<div class="sf-table-card">
  @if($students->isEmpty())
    <div style="text-align:center;padding:40px;color:#94a3b8;">
      <p style="font-weight:600;margin:0;">No enrolled students in this section.</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="sf-table">
        <thead>
          <tr>
            <th>#</th>
            <th>LRN</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Gender</th>
            <th>Parent / Guardian</th>
            <th>Contact</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $i => $stu)
          <tr>
            <td>{{ $i+1 }}</td>
            <td style="font-family:monospace;font-size:.82rem;">{{ $stu->lrn ?? 'N/A' }}</td>
            <td style="font-weight:700;">{{ $stu->last_name }}</td>
            <td>{{ $stu->first_name }}</td>
            <td>{{ ucfirst($stu->gender ?? '—') }}</td>
            <td style="font-size:.82rem;">
              @try
                {{ $stu->parent_name ? decrypt($stu->parent_name) : '—' }}
              @catch(\Exception $e)
                —
              @endtry
            </td>
            <td style="font-size:.82rem;">
              @try
                {{ $stu->parent_contact ? decrypt($stu->parent_contact) : '—' }}
              @catch(\Exception $e)
                —
              @endtry
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endif
@endsection
