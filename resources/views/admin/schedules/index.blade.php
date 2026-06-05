@extends('layouts.app')
@section('title', 'Schedules')
@section('breadcrumb', 'Schedules')

@push('head')
<style>
/* ── Filter bar ─────────────────────────────────────── */
.sch-filters {
  display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
  padding: 18px 20px; background: #fff;
  border: 1px solid #e2e8f0; border-radius: 12px;
  margin-bottom: 20px;
}
.sch-filter-group { display: flex; flex-direction: column; gap: 5px; min-width: 170px; flex: 1; }
.sch-filter-label {
  font-size: .7rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .06em; color: #64748b;
}
.sch-filter-select {
  padding: .5rem .8rem; border: 1px solid #e2e8f0; border-radius: 8px;
  background: #fff; font-size: .85rem; color: #0f172a;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; background-size: 14px;
  padding-right: 34px; cursor: pointer;
  transition: border-color .15s;
}
.sch-filter-select:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }

/* ── Table ──────────────────────────────────────────── */
.sch-table { width: 100%; border-collapse: collapse; font-size: .87rem; }
.sch-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.sch-table th {
  padding: 11px 14px; text-align: left;
  font-size: .7rem; font-weight: 700; color: #64748b;
  text-transform: uppercase; letter-spacing: .06em;
  white-space: nowrap;
}
.sch-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.sch-table tbody tr:hover td { background: #f8fafc; }
.sch-table tbody tr:last-child td { border-bottom: none; }

/* ── Subject / Section cell ─────────────────────────── */
.sch-subject { font-weight: 700; color: #0f172a; margin-bottom: 3px; }
.sch-section-tag {
  display: inline-flex; align-items: center; gap: 5px;
  background: #eff6ff; color: #1d4ed8;
  font-size: .72rem; font-weight: 700; border-radius: 5px;
  padding: 2px 7px;
}

/* ── Faculty cell ───────────────────────────────────── */
.sch-faculty { font-weight: 600; color: #0f172a; }
.sch-tba {
  display: inline-flex; align-items: center; gap: 5px;
  background: #fef3c7; color: #92400e;
  font-size: .75rem; font-weight: 700; border-radius: 6px;
  padding: 3px 10px; font-style: normal;
}

/* ── Days / Time cell ───────────────────────────────── */
.sch-days { font-size: .78rem; font-weight: 700; color: #475569; margin-bottom: 2px; display: flex; gap: 3px; flex-wrap: wrap; }
.sch-day-pill {
  display: inline-block; padding: 1px 6px; border-radius: 4px;
  background: #e0e7ff; color: #3730a3; font-size: .68rem; font-weight: 700;
}
.sch-time { font-size: .78rem; color: #64748b; font-variant-numeric: tabular-nums; }

/* ── Status badges ──────────────────────────────────── */
.sch-badge {
  display: inline-block; padding: .25rem .65rem; border-radius: 6px;
  font-size: .7rem; font-weight: 700; text-transform: uppercase; white-space: nowrap;
}
.sch-badge.tba       { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.sch-badge.assigned  { background: #f0fdf4; color: #166534; border: 1px solid #86efac; }
.sch-badge.cancelled { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }

/* ── Actions ────────────────────────────────────────── */
.sch-action-edit {
  display: inline-block; padding: .3rem .75rem; border-radius: 6px;
  background: #eff6ff; color: #1d4ed8; font-size: .78rem; font-weight: 700;
  text-decoration: none; margin-right: 6px; transition: background .1s;
}
.sch-action-edit:hover { background: #dbeafe; }
.sch-action-del {
  display: inline-block; padding: .3rem .75rem; border-radius: 6px;
  background: #fef2f2; color: #dc2626; font-size: .78rem; font-weight: 700;
  border: none; cursor: pointer; transition: background .1s;
}
.sch-action-del:hover { background: #fee2e2; }

/* ── Empty state ─────────────────────────────────────── */
.sch-empty { padding: 48px; text-align: center; color: #94a3b8; }
.sch-empty svg { display: block; margin: 0 auto 12px; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Class Schedules</h1>
      <p class="enc-page__subtitle">Manage section schedules. Unassigned faculty will appear as <strong>TBA</strong>.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.schedules.create', request()->only('academic_year_id')) }}"
         style="background:#1d4ed8;color:#fff;padding:.55rem 1.2rem;border-radius:8px;font-size:.85rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        New Schedule
      </a>
    </div>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.87rem;font-weight:500;">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:16px;padding:12px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.87rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

{{-- ── Filter Bar ───────────────────────────────────────── --}}
<form method="GET" class="sch-filters">

  <div class="sch-filter-group" style="min-width:220px;flex:2;">
    <label class="sch-filter-label">Academic Year</label>
    <select name="academic_year_id" class="sch-filter-select" onchange="this.form.submit()">
      <option value="">— All Years —</option>
      @foreach($academicYears as $yr)
        <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
          {{ $yr->year_label }} · {{ ucfirst($yr->status) }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="sch-filter-group">
    <label class="sch-filter-label">Section</label>
    <select name="section_id" class="sch-filter-select" onchange="this.form.submit()">
      <option value="">— All Sections —</option>
      @php
        $grouped = $sections->groupBy('grade_level');
        $gradeOrder = ['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'];
        $sorted = collect($gradeOrder)->filter(fn($g) => $grouped->has($g))->mapWithKeys(fn($g) => [$g => $grouped[$g]]);
        foreach ($grouped as $g => $list) if (!in_array($g, $gradeOrder)) $sorted[$g] = $list;
      @endphp
      @foreach($sorted as $grade => $list)
        <optgroup label="{{ $grade }}">
          @foreach($list as $s)
            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>
              {{ $s->section_name }}
            </option>
          @endforeach
        </optgroup>
      @endforeach
    </select>
  </div>

  <div class="sch-filter-group">
    <label class="sch-filter-label">Faculty</label>
    <select name="faculty_id" class="sch-filter-select" onchange="this.form.submit()">
      <option value="">— All Faculty —</option>
      @foreach($faculty as $f)
        <option value="{{ $f->id }}" {{ $facultyId == $f->id ? 'selected' : '' }}>
          {{ $f->first_name }} {{ $f->last_name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="sch-filter-group" style="min-width:130px;flex:0;">
    <label class="sch-filter-label">Status</label>
    <select name="status" class="sch-filter-select" onchange="this.form.submit()">
      <option value="">— Any —</option>
      <option value="tba"       {{ $statusFilter === 'tba'       ? 'selected' : '' }}>TBA</option>
      <option value="assigned"  {{ $statusFilter === 'assigned'  ? 'selected' : '' }}>Assigned</option>
      <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
  </div>

</form>

{{-- ── Schedule Table ───────────────────────────────────── --}}
<div class="enc-card">
  <div style="overflow-x:auto;">
    <table class="sch-table">
      <thead>
        <tr>
          <th>Subject</th>
          <th>Section</th>
          <th>Faculty</th>
          <th>Room</th>
          <th>Days / Time</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($schedules as $sch)
        <tr>
          {{-- Subject --}}
          <td>
            <div class="sch-subject">{{ $sch->subject?->subject_name ?? '—' }}</div>
          </td>

          {{-- Section --}}
          <td>
            @if($sch->section)
            <span class="sch-section-tag">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
              {{ $sch->section->grade_level }} — {{ $sch->section->section_name }}
            </span>
            @else
            <span style="color:#94a3b8;">—</span>
            @endif
          </td>

          {{-- Faculty --}}
          <td>
            @if($sch->faculty)
              <div class="sch-faculty">{{ $sch->faculty->first_name }} {{ $sch->faculty->last_name }}</div>
            @else
              <span class="sch-tba">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                TBA
              </span>
            @endif
          </td>

          {{-- Room --}}
          <td style="color:#475569;font-size:.85rem;">
            {{ $sch->classroom?->room_name ?? $sch->room ?? '—' }}
          </td>

          {{-- Days / Time --}}
          <td>
            <div class="sch-days">
              @foreach($sch->schedule_days ?? [] as $day)
              <span class="sch-day-pill">{{ strtoupper(substr($day, 0, 3)) }}</span>
              @endforeach
            </div>
            <div class="sch-time">
              {{ \Carbon\Carbon::parse($sch->start_time)->format('g:i A') }}
              –
              {{ \Carbon\Carbon::parse($sch->end_time)->format('g:i A') }}
            </div>
          </td>

          {{-- Status --}}
          <td>
            <span class="sch-badge {{ $sch->status }}">{{ $sch->status }}</span>
          </td>

          {{-- Actions --}}
          <td style="text-align:right;white-space:nowrap;">
            <a href="{{ route('admin.schedules.edit', $sch) }}" class="sch-action-edit">Edit</a>
            <form action="{{ route('admin.schedules.destroy', $sch) }}" method="POST" style="display:inline;"
                  data-confirm="Delete this schedule?" data-confirm-type="danger" data-confirm-title="Delete Schedule" data-confirm-ok="Delete">
              @csrf @method('DELETE')
              <button type="submit" class="sch-action-del">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="sch-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;color:#cbd5e1;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
            </svg>
            No schedules found. Click <strong>New Schedule</strong> to create one.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top:16px;">{{ $schedules->links() }}</div>

@endsection
