@extends('layouts.app')
@section('title', 'School Calendar')
@section('breadcrumb', 'School Calendar')

@push('head')
<style>
.cal-wrap { display: grid; grid-template-columns: 1fr 320px; gap: 24px; }
@media(max-width:900px){ .cal-wrap { grid-template-columns: 1fr; } }

.cal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.cal-nav-btn { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 14px; cursor: pointer; font-weight: 600; color: #334155; transition: background .15s; }
.cal-nav-btn:hover { background: #f1f5f9; }
.cal-month-label { font-size: 1.25rem; font-weight: 700; color: #1e293b; }

.cal-grid { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.cal-dow-row { display: grid; grid-template-columns: repeat(7,1fr); background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.cal-dow { text-align: center; padding: 10px 4px; font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
.cal-days { display: grid; grid-template-columns: repeat(7,1fr); }
.cal-day { min-height: 88px; padding: 6px; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; position: relative; }
.cal-day:nth-child(7n){ border-right: none; }
.cal-day-num { font-size: .82rem; font-weight: 600; color: #334155; margin-bottom: 4px; }
.cal-day--empty { background: #fafbfc; }
.cal-day--today .cal-day-num { background: #3b82f6; color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .78rem; }
.cal-event-dot { font-size: .7rem; padding: 1px 5px; border-radius: 4px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff; font-weight: 600; cursor: default; }

.cal-sidebar-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 20px; }
.cal-sidebar-card h3 { font-size: .9rem; font-weight: 700; color: #1e293b; margin: 0 0 14px; }
.cal-upcoming-item { padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
.cal-upcoming-item:last-child { border-bottom: none; padding-bottom: 0; }
.cal-upcoming-date { font-size: .72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
.cal-upcoming-title { font-size: .86rem; font-weight: 600; color: #1e293b; margin: 3px 0 2px; }
.cal-upcoming-type { display: inline-block; font-size: .68rem; padding: 2px 8px; border-radius: 99px; font-weight: 700; color: #fff; }

.badge-holiday    { background: #ef4444; }
.badge-exam       { background: #f59e0b; }
.badge-no_classes { background: #8b5cf6; }
.badge-event      { background: #3b82f6; }

.cal-add-form label { display: block; font-size: .78rem; font-weight: 600; color: #475569; margin-bottom: 4px; margin-top: 10px; }
.cal-add-form input,.cal-add-form select,.cal-add-form textarea { width: 100%; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .83rem; box-sizing: border-box; background: #f8fafc; }
.cal-add-form input:focus,.cal-add-form select:focus,.cal-add-form textarea:focus { outline: none; border-color: #3b82f6; background: #fff; }
.btn-add-event { width: 100%; margin-top: 12px; background: #3b82f6; color: #fff; border: none; border-radius: 8px; padding: 9px; font-weight: 700; font-size: .86rem; cursor: pointer; transition: background .15s; }
.btn-add-event:hover { background: #2563eb; }
</style>
@endpush

@section('content')
<div class="cal-header">
  <div>
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0;">School Calendar</h1>
    <p style="color:#64748b;font-size:.85rem;margin:4px 0 0;">{{ \Carbon\Carbon::create($year,$month,1)->format('F Y') }}</p>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="?month={{ $prevMonth['month'] }}&year={{ $prevMonth['year'] }}" class="cal-nav-btn">← Prev</a>
    <a href="?month={{ now()->month }}&year={{ now()->year }}" class="cal-nav-btn">Today</a>
    <a href="?month={{ $nextMonth['month'] }}&year={{ $nextMonth['year'] }}" class="cal-nav-btn">Next →</a>
  </div>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

<div class="cal-wrap">
  {{-- Calendar Grid --}}
  <div>
    <div class="cal-grid">
      <div class="cal-dow-row">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
          <div class="cal-dow">{{ $dow }}</div>
        @endforeach
      </div>
      <div class="cal-days">
        @for($i = 0; $i < $startDow; $i++)
          <div class="cal-day cal-day--empty"></div>
        @endfor
        @for($d = 1; $d <= $daysInMonth; $d++)
          @php
            $isToday = ($d == now()->day && $month == now()->month && $year == now()->year);
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayEvents = $events->filter(fn($e) =>
              \Carbon\Carbon::parse($e->start_date)->format('Y-m-d') <= $dateStr &&
              \Carbon\Carbon::parse($e->end_date ?? $e->start_date)->format('Y-m-d') >= $dateStr
            );
          @endphp
          <div class="cal-day {{ $isToday ? 'cal-day--today' : '' }}">
            <div class="cal-day-num">{{ $d }}</div>
            @foreach($dayEvents->take(3) as $event)
              <div class="cal-event-dot" style="background:{{ $event->color ?? '#3b82f6' }};" title="{{ $event->title }}">
                {{ Str::limit($event->title, 15) }}
              </div>
            @endforeach
            @if($dayEvents->count() > 3)
              <div style="font-size:.67rem;color:#64748b;margin-top:2px;">+{{ $dayEvents->count()-3 }} more</div>
            @endif
          </div>
        @endfor
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div>
    {{-- Upcoming Events --}}
    <div class="cal-sidebar-card">
      <h3>📅 Upcoming Events (30 days)</h3>
      @forelse($upcoming as $event)
        <div class="cal-upcoming-item">
          <div class="cal-upcoming-date">{{ \Carbon\Carbon::parse($event->start_date)->format('D, M d') }}</div>
          <div class="cal-upcoming-title">{{ $event->title }}</div>
          <span class="cal-upcoming-type badge-{{ $event->type }}">{{ str_replace('_',' ',ucfirst($event->type)) }}</span>
        </div>
      @empty
        <p style="color:#94a3b8;font-size:.83rem;text-align:center;margin:0;">No upcoming events</p>
      @endforelse
    </div>

    {{-- Add Event (Admin/Registrar only) --}}
    @if(in_array(auth()->user()->role_id, ['03','04']))
    <div class="cal-sidebar-card">
      <h3>➕ Add Event</h3>
      <form method="POST" action="{{ route('calendar.store') }}" class="cal-add-form">
        @csrf
        <label>Title *</label>
        <input type="text" name="title" placeholder="Event title" required maxlength="255">

        <label>Type *</label>
        <select name="type" required>
          <option value="event">School Event</option>
          <option value="holiday">Holiday</option>
          <option value="exam">Exam / Quiz</option>
          <option value="no_classes">No Classes</option>
        </select>

        <label>Audience *</label>
        <select name="audience" required>
          <option value="all">Everyone</option>
          <option value="student">Students only</option>
          <option value="faculty">Faculty only</option>
        </select>

        <label>Start Date *</label>
        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}">

        <label>End Date</label>
        <input type="date" name="end_date">

        <label>Description</label>
        <textarea name="description" rows="2" placeholder="Optional details..." maxlength="1000"></textarea>

        <button type="submit" class="btn-add-event">Add to Calendar</button>
      </form>
    </div>
    @endif

    {{-- Legend --}}
    <div class="cal-sidebar-card">
      <h3>Legend</h3>
      <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach(['holiday'=>'Holiday','exam'=>'Exam / Quiz','no_classes'=>'No Classes','event'=>'School Event'] as $type => $label)
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:14px;height:14px;border-radius:3px;background:{{ match($type){ 'holiday'=>'#ef4444','exam'=>'#f59e0b','no_classes'=>'#8b5cf6',default=>'#3b82f6' } }};display:inline-block;"></span>
            <span style="font-size:.82rem;color:#475569;font-weight:500;">{{ $label }}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- This month's events list --}}
    @if($events->isNotEmpty())
    <div class="cal-sidebar-card">
      <h3>This Month</h3>
      @foreach($events as $event)
        <div class="cal-upcoming-item">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
              <div class="cal-upcoming-date">{{ \Carbon\Carbon::parse($event->start_date)->format('M d') }}{{ $event->end_date && $event->end_date != $event->start_date ? ' – '.\Carbon\Carbon::parse($event->end_date)->format('M d') : '' }}</div>
              <div class="cal-upcoming-title">{{ $event->title }}</div>
              <span class="cal-upcoming-type badge-{{ $event->type }}">{{ str_replace('_',' ',ucfirst($event->type)) }}</span>
            </div>
            @if(in_array(auth()->user()->role_id, ['03','04']))
              <form method="POST" action="{{ route('calendar.destroy', $event) }}" onsubmit="return confirm('Remove this event?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:2px 4px;font-size:.8rem;">✕</button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>
    @endif
  </div>
</div>
@endsection
