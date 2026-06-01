@extends('layouts.app')
@section('title', 'Subjects Registry')
@section('breadcrumb', 'Subjects')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Subjects Registry</h1>
      <p class="enc-page__subtitle">Master database of all subjects offered by the institution. Each subject can be tagged with a year level to drive the schedule cascading dropdowns.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.subjects.create') }}"
         style="background:#1d4ed8;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">
        + Add Subject
      </a>
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

{{-- Filters --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" action="{{ route('admin.subjects.index') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;flex:1;min-width:240px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Search</label>
        <input type="text" name="search" placeholder="Search by code or name..."
               value="{{ request('search') }}"
               style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
      </div>
      <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Status</label>
        <select name="status" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All Statuses</option>
          <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <button type="submit" style="padding:.55rem 1.1rem;border:none;border-radius:8px;background:#0f172a;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;">Filter</button>
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
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Subject Code</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Subject ID</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Name</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Year Level</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Credits</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subjects as $subject)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;color:#0f172a;font-weight:700;">{{ $subject->subject_code }}</td>
            <td style="padding:12px 14px;">
              <code style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:4px;font-size:.75rem;">{{ $subject->subject_id }}</code>
            </td>
            <td style="padding:12px 14px;color:#0f172a;">{{ $subject->subject_name }}</td>
            <td style="padding:12px 14px;">
              @if($subject->year_level)
                <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;color:#1d4ed8;background:#eff6ff;border:1px solid #bfdbfe;">{{ $subject->year_level }}</span>
              @else
                <span style="color:#94a3b8;font-size:.78rem;font-style:italic;">— Any —</span>
              @endif
            </td>
            <td style="padding:12px 14px;color:#64748b;">{{ $subject->credits ?? '—' }}</td>
            <td style="padding:12px 14px;">
              @php $c = $subject->status === 'active' ? ['#166534','#86efac','#f0fdf4'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $subject->status }}</span>
            </td>
            <td style="padding:12px 14px;text-align:right;white-space:nowrap;">
              <a href="{{ route('admin.subjects.show', $subject) }}" style="color:#1d4ed8;text-decoration:none;font-size:.82rem;font-weight:600;margin-right:10px;">View</a>
              <a href="{{ route('admin.subjects.edit', $subject) }}" style="color:#1d4ed8;text-decoration:none;font-size:.82rem;font-weight:600;margin-right:10px;">Edit</a>
              @if(!$subject->isUsedInCurriculum())
              <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" style="display:inline;" onsubmit="return confirm('Delete subject \'{{ $subject->subject_code }}\'?');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;">No subjects found. Click "+ Add Subject" to create one.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($subjects->hasPages())
<div style="margin-top:16px;">
  {{ $subjects->links() }}
</div>
@endif

@endsection
