@extends('layouts.app')
@section('title', 'Classrooms')
@section('breadcrumb', 'Classrooms')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Classrooms</h1>
      <p class="enc-page__subtitle">Manage classrooms used in scheduling. Rooms are scoped per academic year.</p>
    </div>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">{{ session('success') }}</div>
@endif

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

{{-- Year selector and add form --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

  <div class="enc-card">
    <div class="enc-card__header"><div class="enc-card__title">Filter by Academic Year</div></div>
    <div class="enc-card__body" style="padding:20px;">
      <form method="GET" style="display:flex;flex-direction:column;gap:6px;">
        <label style="font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()"
                style="padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— Select Year —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} ({{ ucfirst($yr->status) }})
            </option>
          @endforeach
        </select>
      </form>
    </div>
  </div>

  <div class="enc-card">
    <div class="enc-card__header"><div class="enc-card__title">+ Add Classroom</div></div>
    <div class="enc-card__body" style="padding:20px;">
      <form method="POST" action="{{ route('admin.classrooms.store') }}" style="display:flex;flex-direction:column;gap:10px;">
        @csrf
        <input type="hidden" name="academic_year_id" value="{{ $yearId }}">
        <input type="text" name="room_name" placeholder="Room name (e.g. Room 101)" required maxlength="50" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
        <input type="text" name="building" placeholder="Building (optional)" maxlength="50" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
        <input type="number" name="capacity" placeholder="Capacity" required min="1" max="200" value="40" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
        <select name="status" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <button type="submit" {{ !$yearId ? 'disabled' : '' }} style="padding:.5rem 1rem;border:none;border-radius:8px;background:{{ $yearId ? '#1d4ed8' : '#94a3b8' }};color:#fff;font-size:.875rem;font-weight:700;cursor:{{ $yearId ? 'pointer' : 'not-allowed' }};">Add Classroom</button>
        @if(!$yearId)<p style="font-size:.75rem;color:#92400e;margin:0;">Pick an academic year first.</p>@endif
      </form>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="enc-card">
  <div class="enc-card__header"><div class="enc-card__title">Classrooms List</div></div>
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Room</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Building</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Capacity</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Year</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($classrooms as $cr)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;font-weight:600;color:#0f172a;">{{ $cr->room_name }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $cr->building ?? '—' }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $cr->capacity }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $cr->academicYear?->year_label ?? '—' }}</td>
            <td style="padding:12px 14px;">
              @php $c = $cr->status === 'active' ? ['#166534','#86efac','#f0fdf4'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $cr->status }}</span>
            </td>
            <td style="padding:12px 14px;text-align:right;">
              <form action="{{ route('admin.classrooms.destroy', $cr) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete classroom \'{{ $cr->room_name }}\'?');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">No classrooms in this academic year. Add one above.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="margin-top:16px;">{{ $classrooms->links() }}</div>

@endsection
