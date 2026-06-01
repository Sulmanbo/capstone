@extends('layouts.app')
@section('title', 'Academic Years')
@section('breadcrumb', 'Academic Years')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Academic Years</h1>
      <p class="enc-page__subtitle">Manage institutional academic cycles. Multiple years can be active at once so you can prepare next term while the current one is still running.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.academic-years.create') }}"
         style="background:#1d4ed8;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">
        + Add Academic Year
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
    <form method="GET" action="{{ route('admin.academic-years.index') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;flex:1;min-width:240px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Search</label>
        <input type="text" name="search" placeholder="Search by year label..."
               value="{{ request('search') }}"
               style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
      </div>
      <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Status</label>
        <select name="status" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All Statuses</option>
          <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
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
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Year Label</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Start Date</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">End Date</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Term Type</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Quarters</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($academicYears as $year)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;color:#0f172a;font-weight:700;">{{ $year->year_label }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $year->start_date->format('M d, Y') }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $year->end_date->format('M d, Y') }}</td>
            <td style="padding:12px 14px;">
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;color:#1d4ed8;background:#eff6ff;border:1px solid #bfdbfe;">{{ ucfirst($year->term_type ?? 'quarterly') }}</span>
            </td>
            <td style="padding:12px 14px;">
              @php
                $colors = [
                  'active'   => ['#166534','#86efac','#f0fdf4'],
                  'inactive' => ['#92400e','#fcd34d','#fffbeb'],
                  'archived' => ['#475569','#cbd5e1','#f8fafc'],
                ];
                $c = $colors[$year->status] ?? $colors['inactive'];
              @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">
                @if($year->status === 'active')● @endif{{ $year->status }}
              </span>
            </td>
            <td style="padding:12px 14px;color:#64748b;">{{ $year->quarters()->count() }} Quarter(s)</td>
            <td style="padding:12px 14px;text-align:right;white-space:nowrap;">
              <a href="{{ route('admin.academic-years.edit', $year) }}" style="color:#1d4ed8;text-decoration:none;font-size:.82rem;font-weight:600;margin-right:10px;">Edit</a>

              <form method="POST" action="{{ route('admin.academic-years.toggle', $year) }}"
                    style="display:inline;"
                    onsubmit="return confirm('{{ $year->status === 'active' ? 'Deactivate' : 'Activate' }} \'{{ $year->year_label }}\'?');">
                @csrf @method('PATCH')
                <button type="submit" style="background:none;border:none;color:{{ $year->status === 'active' ? '#92400e' : '#166534' }};font-size:.82rem;font-weight:600;cursor:pointer;margin-right:10px;">
                  {{ $year->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
              </form>

              @if($year->status !== 'active' && $year->quarters()->count() === 0)
              <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}"
                    style="display:inline;"
                    onsubmit="return confirm('Delete academic year \'{{ $year->year_label }}\'?');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;">No academic years found. Click "+ Add Academic Year" to create one.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($academicYears->hasPages())
<div style="margin-top:16px;">
  {{ $academicYears->links() }}
</div>
@endif

@endsection
