@extends('layouts.app')
@section('title', 'Schedules')
@section('breadcrumb', 'Schedules')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Class Schedules</h1>
      <p class="enc-page__subtitle">Create schedules first; assign faculty when ready. Schedules without a teacher remain as <strong>TBA</strong>.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.schedules.create', request()->only('academic_year_id')) }}"
         style="background:#1d4ed8;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">
        + New Schedule
      </a>
    </div>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;font-weight:500;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

{{-- Filters — Academic Year FIRST per adviser feedback --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:200px;flex:1;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— All Years —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} ({{ ucfirst($yr->status) }}, {{ ucfirst($yr->term_type ?? 'quarterly') }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;min-width:180px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Section</label>
        <select name="section_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— All —</option>
          @foreach($sections as $s)
            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->grade_level }} — {{ $s->section_name }}</option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;min-width:180px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Faculty</label>
        <select name="faculty_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— All —</option>
          @foreach($faculty as $f)
            <option value="{{ $f->id }}" {{ $facultyId == $f->id ? 'selected' : '' }}>{{ $f->last_name }}, {{ $f->first_name }}</option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:4px;min-width:140px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Status</label>
        <select name="status" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— Any —</option>
          @foreach(['tba','assigned','cancelled'] as $st)
            <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>

{{-- Table --}}
<div class="enc-card">
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Section / Subject</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Faculty</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Room</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Days / Time</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedules as $sch)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;">
              <div style="font-weight:600;color:#0f172a;">{{ $sch->subject?->subject_name ?? '—' }}</div>
              <div style="font-size:.78rem;color:#64748b;">{{ $sch->section?->grade_level }} — {{ $sch->section?->section_name }}</div>
            </td>
            <td style="padding:12px 14px;">
              @if($sch->faculty)
                {{ $sch->faculty->last_name }}, {{ $sch->faculty->first_name }}
              @else
                <span style="color:#92400e;font-weight:600;font-style:italic;">— TBA —</span>
              @endif
            </td>
            <td style="padding:12px 14px;color:#64748b;">
              {{ $sch->classroom?->room_name ?? $sch->room ?? '—' }}
            </td>
            <td style="padding:12px 14px;font-family:monospace;font-size:.82rem;color:#475569;">
              {{ collect($sch->schedule_days ?? [])->map(fn($d) => substr(ucfirst($d),0,3))->implode(' ') }}<br>
              {{ \Carbon\Carbon::parse($sch->start_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($sch->end_time)->format('g:i A') }}
            </td>
            <td style="padding:12px 14px;">
              @php $colors = ['tba'=>['#92400e','#fcd34d','#fffbeb'], 'assigned'=>['#166534','#86efac','#f0fdf4'], 'cancelled'=>['#991b1b','#fca5a5','#fef2f2']]; $c = $colors[$sch->status] ?? $colors['tba']; @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $sch->status }}</span>
            </td>
            <td style="padding:12px 14px;text-align:right;">
              <a href="{{ route('admin.schedules.edit', $sch) }}" style="color:#1d4ed8;text-decoration:none;font-size:.82rem;font-weight:600;margin-right:10px;">Edit</a>
              <form action="{{ route('admin.schedules.destroy', $sch) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this schedule?');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">No schedules found. Create one to get started.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="margin-top:16px;">{{ $schedules->links() }}</div>

@endsection
