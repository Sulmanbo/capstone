@extends('layouts.app')
@section('title', 'Faculty Schedules')
@section('breadcrumb', 'Faculty Schedules')

@push('head')
<style>
.sch-grid { display:grid; grid-template-columns:400px 1fr; gap:24px; align-items:start; }
@media(max-width:960px){ .sch-grid { grid-template-columns:1fr; } }

.sch-form-card {
  background:#fff; border:1px solid #e2e8f0; border-radius:16px;
  padding:24px; box-shadow:0 2px 12px rgba(15,23,42,.05); position:sticky; top:24px;
}
.sch-form-card h3 { font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 20px; }

.form-group { margin-bottom:15px; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:#374151; margin-bottom:5px; }
.form-control {
  width:100%; padding:.58rem .85rem; border:1px solid #d1d5db;
  border-radius:10px; font-size:.86rem; color:#0f172a;
  transition:border-color .15s, box-shadow .15s; box-sizing:border-box;
}
.form-control:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }

/* Day checkboxes */
.day-group { display:flex; flex-wrap:wrap; gap:8px; }
.day-input { display:none; }
.day-label {
  width:42px; text-align:center; padding:.35rem .5rem;
  border-radius:8px; font-size:.76rem; font-weight:700;
  border:1.5px solid #e2e8f0; color:#64748b; cursor:pointer;
  transition:all .15s;
}
.day-input:checked + .day-label { border-color:#4f46e5; background:#eef2ff; color:#4338ca; }

.btn-assign {
  width:100%; padding:.7rem 1rem; border:none; border-radius:10px;
  background:#0f766e; color:#fff; font-weight:700; font-size:.9rem;
  cursor:pointer; transition:background .15s, transform .1s;
  margin-top:4px;
}
.btn-assign:hover { background:#0d5f58; transform:translateY(-1px); }

/* Table */
.sch-table-wrap { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; box-shadow:0 2px 12px rgba(15,23,42,.04); }
.sch-table { width:100%; border-collapse:collapse; }
.sch-table th {
  text-align:left; padding:11px 14px;
  font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
  color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0;
}
.sch-table td {
  padding:12px 14px; font-size:.86rem; color:#0f172a;
  border-bottom:1px solid #f1f5f9; vertical-align:middle;
}
.sch-table tr:last-child td { border-bottom:none; }
.sch-table tr:hover td { background:#f8fafc; }

.faculty-avatar {
  width:30px; height:30px; border-radius:8px;
  background:linear-gradient(135deg,#4f46e5,#06b6d4);
  display:inline-flex; align-items:center; justify-content:center;
  font-size:.68rem; font-weight:800; color:#fff; flex-shrink:0; margin-right:8px;
  vertical-align:middle;
}
.day-chip {
  display:inline-block; padding:.15rem .45rem; border-radius:5px;
  background:#f1f5f9; color:#475569; font-size:.72rem; font-weight:600; margin:1px;
}
.btn-del-sm {
  padding:.3rem .65rem; border-radius:7px; border:1px solid #fecaca;
  background:#fff5f5; color:#dc2626; font-size:.74rem; font-weight:600;
  cursor:pointer; transition:background .15s;
}
.btn-del-sm:hover { background:#fee2e2; }

.empty-state { text-align:center; padding:48px 24px; color:#94a3b8; }
.empty-state svg { width:48px; height:48px; margin:0 auto 12px; display:block; opacity:.4; }
.empty-state p { font-size:.9rem; font-weight:500; }
</style>
@endpush

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.88rem;color:#166534;font-weight:500;">
  {{ session('success') }}
</div>
@endif

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Faculty Schedules</h1>
      <p class="enc-page__subtitle">Assign subjects, sections, classrooms, and time slots to faculty members.</p>
    </div>
  </div>
</div>

<div class="sch-grid">

  {{-- ── Assign Schedule Form ─────────────────────── --}}
  <div class="sch-form-card">
    <h3>Assign Schedule</h3>
    <form method="POST" action="{{ route('admin.schedules.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label">Faculty Member *</label>
        <select name="faculty_id" class="form-control" required>
          <option value="">— Select Faculty —</option>
          @foreach($faculty as $f)
            <option value="{{ $f->id }}" {{ old('faculty_id') == $f->id ? 'selected' : '' }}>
              {{ $f->last_name }}, {{ $f->first_name }}
            </option>
          @endforeach
        </select>
        @error('faculty_id')<div style="color:#dc2626;font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Subject *</label>
        <input type="text" name="subject_name" class="form-control" placeholder="e.g. Mathematics 7" value="{{ old('subject_name') }}" required>
        @error('subject_name')<div style="color:#dc2626;font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Section</label>
          <input type="text" name="section" class="form-control" placeholder="e.g. 7-Sampaguita" value="{{ old('section') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Room / Classroom</label>
          <input type="text" name="room" class="form-control" placeholder="e.g. Room 101" value="{{ old('room') }}">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Days *</label>
        <div class="day-group">
          @foreach(['monday' => 'Mon','tuesday' => 'Tue','wednesday' => 'Wed','thursday' => 'Thu','friday' => 'Fri','saturday' => 'Sat'] as $val => $label)
            <input type="checkbox" name="days[]" id="d_{{ $val }}" value="{{ $val }}" class="day-input"
              {{ is_array(old('days')) && in_array($val, old('days')) ? 'checked' : '' }}>
            <label for="d_{{ $val }}" class="day-label">{{ $label }}</label>
          @endforeach
        </div>
        @error('days')<div style="color:#dc2626;font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Start Time *</label>
          <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
        </div>
        <div class="form-group">
          <label class="form-label">End Time *</label>
          <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Academic Year <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
        <select name="academic_year_id" class="form-control">
          <option value="">— Select Academic Year —</option>
          @foreach($academicYears as $ay)
            <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>
              {{ $ay->year_label }}
            </option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn-assign">Assign Schedule</button>
    </form>
  </div>

  {{-- ── Schedule Table ───────────────────────────── --}}
  <div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
      <div style="font-size:.9rem;font-weight:700;color:#0f172a;">All Assigned Schedules
        <span style="margin-left:6px;font-size:.78rem;font-weight:500;color:#64748b;">({{ $schedules->total() }})</span>
      </div>
    </div>

    @if($schedules->isEmpty())
      <div class="sch-table-wrap">
        <div class="empty-state">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
          </svg>
          <p>No schedules assigned yet.</p>
        </div>
      </div>
    @else
      <div class="sch-table-wrap">
        <table class="sch-table">
          <thead>
            <tr>
              <th>Faculty</th>
              <th>Subject</th>
              <th>Section</th>
              <th>Room</th>
              <th>Days</th>
              <th>Time</th>
              <th>A.Y.</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($schedules as $sch)
            <tr>
              <td>
                <span class="faculty-avatar">
                  {{ strtoupper(substr($sch->faculty->first_name ?? 'F', 0, 1)) }}{{ strtoupper(substr($sch->faculty->last_name ?? '', 0, 1)) }}
                </span>
                {{ $sch->faculty->last_name ?? '—' }}, {{ $sch->faculty->first_name ?? '' }}
              </td>
              <td style="font-weight:600;">{{ $sch->subject_name }}</td>
              <td>{{ $sch->section ?: '—' }}</td>
              <td>{{ $sch->room ?: '—' }}</td>
              <td>
                @foreach($sch->days as $day)
                  <span class="day-chip">{{ ucfirst(substr($day,0,3)) }}</span>
                @endforeach
              </td>
              <td style="white-space:nowrap;font-size:.82rem;color:#475569;">{{ $sch->time_range }}</td>
              <td style="font-size:.8rem;color:#94a3b8;">{{ $sch->academicYear->year_label ?? '—' }}</td>
              <td>
                <form method="POST" action="{{ route('admin.schedules.destroy', $sch) }}" style="margin:0;"
                      onsubmit="return confirm('Remove this schedule?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-del-sm">Remove</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="margin-top:16px;">{{ $schedules->links() }}</div>
    @endif
  </div>

</div>
@endsection
