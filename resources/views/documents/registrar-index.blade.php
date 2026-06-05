@extends('layouts.app')
@section('title', 'Document Requests — Registrar')
@section('breadcrumb', 'Document Requests')

@push('head')
<style>
.doc-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width:640px){ .doc-stats { grid-template-columns: 1fr; } }
.doc-stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; }
.doc-stat-label { font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
.doc-stat-num { font-size: 2rem; font-weight: 800; color: #1e293b; line-height: 1; }

.doc-filters { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.doc-filters select, .doc-filters input { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: .85rem; background: #f8fafc; }
.doc-filters button { background: #3b82f6; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-weight: 700; font-size: .85rem; cursor: pointer; }

.doc-table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.doc-table { width: 100%; border-collapse: collapse; }
.doc-table th { padding: 10px 14px; background: #f8fafc; text-align: left; font-size: .73rem; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
.doc-table td { padding: 12px 14px; font-size: .84rem; color: #334155; border-bottom: 1px solid #f8fafc; vertical-align: top; }
.doc-table tr:last-child td { border-bottom: none; }
.doc-table tr:hover td { background: #f8fafc; }

.doc-status { display: inline-flex; padding: 3px 10px; border-radius: 99px; font-size: .72rem; font-weight: 700; }
.doc-status--pending    { background: #fef3c7; color: #92400e; }
.doc-status--processing { background: #dbeafe; color: #1e40af; }
.doc-status--ready      { background: #d1fae5; color: #065f46; }
.doc-status--released   { background: #f0fdf4; color: #166534; }
.doc-status--rejected   { background: #fee2e2; color: #991b1b; }

.doc-update-form select { padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .8rem; }
.doc-update-form input { padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .8rem; width: 160px; }
.doc-update-form button { background: #0f172a; color: #fff; border: none; border-radius: 7px; padding: 5px 12px; font-size: .78rem; font-weight: 700; cursor: pointer; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
  <div>
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0;">Document Requests</h1>
    <p style="color:#64748b;font-size:.85rem;margin:4px 0 0;">Manage and process student document requests</p>
  </div>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

{{-- Stats --}}
<div class="doc-stats">
  <div class="doc-stat-card" style="border-left:4px solid #f59e0b;">
    <div class="doc-stat-label">⏳ Pending</div>
    <div class="doc-stat-num" style="color:#d97706;">{{ $counts['pending'] }}</div>
  </div>
  <div class="doc-stat-card" style="border-left:4px solid #3b82f6;">
    <div class="doc-stat-label">⚙️ Processing</div>
    <div class="doc-stat-num" style="color:#2563eb;">{{ $counts['processing'] }}</div>
  </div>
  <div class="doc-stat-card" style="border-left:4px solid #10b981;">
    <div class="doc-stat-label">✅ Ready</div>
    <div class="doc-stat-num" style="color:#059669;">{{ $counts['ready'] }}</div>
  </div>
</div>

{{-- Filters --}}
<form method="GET" class="doc-filters">
  <select name="status">
    <option value="">All Statuses</option>
    @foreach(['pending','processing','ready','released','rejected'] as $s)
      <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or LRN…">
  <button type="submit">Filter</button>
  @if($status || $search)
    <a href="{{ route('documents.registrar.index') }}" style="font-size:.83rem;color:#64748b;text-decoration:none;padding:8px 0;">Clear</a>
  @endif
</form>

{{-- Table --}}
<div class="doc-table-card">
  @if($requests->isEmpty())
    <div style="text-align:center;padding:50px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📋</div>
      <p style="font-weight:600;margin:0 0 4px;">No requests found</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="doc-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Document</th>
            <th>Copies</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Update Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
          <tr>
            <td style="color:#94a3b8;font-size:.78rem;">{{ $req->id }}</td>
            <td>
              <div style="font-weight:700;color:#1e293b;">{{ $req->student?->first_name }} {{ $req->student?->last_name }}</div>
              <div style="font-size:.75rem;color:#64748b;">LRN: {{ $req->student?->lrn ?? 'N/A' }}</div>
            </td>
            <td style="font-weight:600;">{{ $req->document_label }}</td>
            <td>{{ $req->copies }}</td>
            <td style="max-width:180px;font-size:.8rem;color:#64748b;" title="{{ $req->purpose }}">{{ Str::limit($req->purpose, 60) }}</td>
            <td><span class="doc-status doc-status--{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
            <td style="color:#94a3b8;font-size:.78rem;">{{ $req->created_at->format('M d, Y') }}</td>
            <td>
              @if(!in_array($req->status, ['released']))
              <form method="POST" action="{{ route('documents.update-status', $req) }}" class="doc-update-form" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                @csrf @method('PATCH')
                <select name="status" required>
                  @foreach(['processing','ready','released','rejected'] as $s)
                    <option value="{{ $s }}" {{ $req->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                  @endforeach
                </select>
                <input type="text" name="remarks" placeholder="Remarks…" value="{{ $req->remarks }}">
                <button type="submit">Update</button>
              </form>
              @else
                <span style="font-size:.78rem;color:#10b981;font-weight:700;">Released</span>
                @if($req->released_at)<div style="font-size:.72rem;color:#94a3b8;">{{ $req->released_at->format('M d, Y') }}</div>@endif
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div style="padding:16px 20px;">{{ $requests->links() }}</div>
  @endif
</div>
@endsection
