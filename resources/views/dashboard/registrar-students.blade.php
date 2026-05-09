@extends('layouts.app')
@section('title', 'Student Records')
@section('breadcrumb', 'Student Records')

@push('head')
<style>
.reg-search-bar { display:flex; gap:10px; margin-bottom:20px; }
.reg-search-input { flex:1; padding:.55rem 1rem; border:1px solid var(--sd-border); border-radius:999px; font-size:.875rem; outline:none; transition:border-color .15s; background:#fff; }
.reg-search-input:focus { border-color:var(--sd-primary); box-shadow:0 0 0 3px rgba(79,70,229,.08); }
.reg-table { width:100%; border-collapse:collapse; }
.reg-table th { padding:10px 14px; font-size:.72rem; font-weight:700; color:var(--sd-muted); text-transform:uppercase; letter-spacing:.07em; border-bottom:2px solid var(--sd-border); text-align:left; }
.reg-table td { padding:12px 14px; font-size:.875rem; border-bottom:1px solid #f1f5f9; color:var(--sd-navy); }
.reg-table tr:last-child td { border-bottom:none; }
.reg-table tr:hover td { background:#f8fafc; }
.reg-avatar { width:32px; height:32px; border-radius:8px; background:#4f46e5; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:700; flex-shrink:0; }
.reg-status { display:inline-block; padding:.18rem .6rem; border-radius:999px; font-size:.7rem; font-weight:700; }
.status--active   { background:rgba(16,185,129,.1); color:#059669; }
.status--inactive { background:#f1f5f9; color:#64748b; }
.status--locked   { background:rgba(239,68,68,.1); color:#dc2626; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <div>
    <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 3px;">Student Records</h1>
    <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">All enrolled and registered students</p>
  </div>
  <span style="font-size:.8rem;font-weight:600;color:var(--sd-muted);background:#f1f5f9;padding:.35rem .9rem;border-radius:999px;">
    {{ $students->total() }} {{ Str::plural('student', $students->total()) }}
  </span>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('registrar.students') }}" class="reg-search-bar">
  <input type="text" name="search" value="{{ $search }}" placeholder="Search by name…" class="reg-search-input">
  <button type="submit" class="enc-button enc-button--primary enc-button--sm">Search</button>
  @if($search)
    <a href="{{ route('registrar.students') }}" class="enc-button enc-button--secondary enc-button--sm">Clear</a>
  @endif
</form>

<div class="sd-card">
  <div class="sd-card__body" style="padding:0;">
    @if($students->isEmpty())
      <div style="text-align:center;padding:56px 24px;color:var(--sd-muted);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        <div style="font-weight:600;font-size:.9rem;">No students found</div>
        @if($search)<div style="font-size:.8rem;margin-top:4px;">Try a different search term.</div>@endif
      </div>
    @else
      <div style="overflow-x:auto;">
        <table class="reg-table">
          <thead>
            <tr>
              <th>Student</th>
              <th>LRN</th>
              <th>Email</th>
              <th>Status</th>
              <th>Enrolled</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $s)
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <div class="reg-avatar">{{ strtoupper(substr($s->first_name,0,1)) }}{{ strtoupper(substr($s->last_name,0,1)) }}</div>
                  <div>
                    <div style="font-weight:600;">{{ $s->last_name }}, {{ $s->first_name }}</div>
                    @if($s->middle_name)<div style="font-size:.75rem;color:var(--sd-muted);">{{ $s->middle_name }}</div>@endif
                  </div>
                </div>
              </td>
              <td style="font-family:monospace;font-size:.82rem;">{{ $s->lrn ?? '—' }}</td>
              <td style="font-size:.82rem;color:var(--sd-muted);">{{ $s->email ?? '—' }}</td>
              <td>
                <span class="reg-status status--{{ $s->status ?? 'inactive' }}">
                  {{ ucfirst($s->status ?? 'inactive') }}
                </span>
              </td>
              <td style="font-size:.8rem;color:var(--sd-muted);">{{ $s->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($students->hasPages())
      <div style="padding:14px 20px;border-top:1px solid var(--sd-border);">
        {{ $students->links() }}
      </div>
      @endif
    @endif
  </div>
</div>

@endsection
