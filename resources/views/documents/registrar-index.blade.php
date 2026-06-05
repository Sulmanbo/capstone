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
.doc-table tr.selected td { background: #eff6ff; }

.doc-status { display: inline-flex; padding: 3px 10px; border-radius: 99px; font-size: .72rem; font-weight: 700; }
.doc-status--pending    { background: #fef3c7; color: #92400e; }
.doc-status--processing { background: #dbeafe; color: #1e40af; }
.doc-status--ready      { background: #d1fae5; color: #065f46; }
.doc-status--released   { background: #f0fdf4; color: #166534; }
.doc-status--rejected   { background: #fee2e2; color: #991b1b; }

.doc-update-form select { padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .8rem; }
.doc-update-form input { padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .8rem; width: 160px; }
.doc-update-form button { background: #0f172a; color: #fff; border: none; border-radius: 7px; padding: 5px 12px; font-size: .78rem; font-weight: 700; cursor: pointer; }

.bulk-bar { background: #1e40af; color: #fff; border-radius: 12px; padding: 12px 18px; margin-bottom: 16px; display: none; align-items: center; gap: 12px; flex-wrap: wrap; }
.bulk-bar.active { display: flex; }
.bulk-bar select,.bulk-bar input { padding: 6px 10px; border-radius: 8px; border: 1.5px solid rgba(255,255,255,.4); background: rgba(255,255,255,.15); color: #fff; font-size: .84rem; font-weight: 600; }
.bulk-bar input::placeholder { color: rgba(255,255,255,.6); }
.bulk-bar .bulk-apply-btn { background: #fff; color: #1e40af; border: none; border-radius: 8px; padding: 6px 18px; font-weight: 800; cursor: pointer; font-size: .84rem; }
.bulk-bar .bulk-clear-btn { background: rgba(255,255,255,.15); color: #fff; border: none; border-radius: 8px; padding: 6px 12px; cursor: pointer; font-size: .82rem; margin-left: auto; }
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

{{-- Bulk Action Bar --}}
<div class="bulk-bar" id="bulk-bar">
  <span id="bulk-count" style="font-weight:700;font-size:.88rem;">0 selected</span>
  <form method="POST" action="{{ route('documents.bulk-update') }}" id="bulk-form" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:0;">
    @csrf
    <input type="hidden" name="ids" id="bulk-ids">
    <select name="status">
      <option value="processing">→ Processing</option>
      <option value="ready">→ Ready for Pickup</option>
      <option value="released">→ Released</option>
      <option value="rejected">→ Rejected</option>
    </select>
    <input type="text" name="remarks" placeholder="Bulk remarks (optional)…" style="width:200px;">
    <button type="submit" class="bulk-apply-btn">Apply to Selected</button>
  </form>
  <button type="button" onclick="clearSelection()" class="bulk-clear-btn">✕ Clear</button>
</div>

{{-- Table --}}
<div class="doc-table-card">
  @if($requests->isEmpty())
    <div style="text-align:center;padding:50px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📋</div>
      <p style="font-weight:600;margin:0 0 4px;">No requests found</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="doc-table" id="doc-table">
        <thead>
          <tr>
            <th style="width:36px;padding-left:16px;">
              <input type="checkbox" id="select-all" title="Select all" style="cursor:pointer;width:15px;height:15px;">
            </th>
            <th>#</th>
            <th>Student</th>
            <th>Document</th>
            <th>Copies</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Update</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
          <tr id="row-{{ $req->id }}">
            <td style="padding-left:16px;">
              @if(!in_array($req->status, ['released']))
                <input type="checkbox" class="row-check" value="{{ $req->id }}" style="cursor:pointer;width:15px;height:15px;">
              @endif
            </td>
            <td style="color:#94a3b8;font-size:.75rem;">{{ $req->id }}</td>
            <td>
              <div style="font-weight:700;color:#1e293b;">{{ $req->student?->first_name }} {{ $req->student?->last_name }}</div>
              <div style="font-size:.75rem;color:#64748b;">LRN: {{ $req->student?->lrn ?? 'N/A' }}</div>
            </td>
            <td style="font-weight:600;">{{ $req->document_label }}</td>
            <td>{{ $req->copies }}</td>
            <td style="max-width:160px;font-size:.8rem;color:#64748b;" title="{{ $req->purpose }}">{{ Str::limit($req->purpose, 55) }}</td>
            <td><span class="doc-status doc-status--{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
            <td style="color:#94a3b8;font-size:.78rem;white-space:nowrap;">{{ $req->created_at->format('M d, Y') }}</td>
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
                @if($req->released_at)<div style="font-size:.72rem;color:#94a3b8;white-space:nowrap;">{{ $req->released_at->format('M d, Y') }}</div>@endif
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
      <span style="font-size:.82rem;color:#64748b;">Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }} of {{ $requests->total() }}</span>
      {{ $requests->links() }}
    </div>
  @endif
</div>

@push('scripts')
<script>
const bulkBar   = document.getElementById('bulk-bar');
const bulkCount = document.getElementById('bulk-count');
const bulkIds   = document.getElementById('bulk-ids');
const selectAll = document.getElementById('select-all');

function updateBulkBar() {
  const checked = [...document.querySelectorAll('.row-check:checked')];
  checked.length > 0 ? bulkBar.classList.add('active') : bulkBar.classList.remove('active');
  bulkCount.textContent = checked.length + ' selected';
  bulkIds.value = checked.map(c => c.value).join(',');

  // Highlight selected rows
  document.querySelectorAll('.row-check').forEach(c => {
    c.closest('tr').classList.toggle('selected', c.checked);
  });
}

function clearSelection() {
  document.querySelectorAll('.row-check').forEach(c => c.checked = false);
  if (selectAll) selectAll.checked = false;
  updateBulkBar();
}

document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', function() {
  updateBulkBar();
  // Sync select-all state
  const all  = document.querySelectorAll('.row-check').length;
  const chkd = document.querySelectorAll('.row-check:checked').length;
  if (selectAll) selectAll.indeterminate = chkd > 0 && chkd < all;
  if (selectAll) selectAll.checked = chkd === all && all > 0;
}));

if (selectAll) {
  selectAll.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
    updateBulkBar();
  });
}

document.getElementById('bulk-form')?.addEventListener('submit', async function (e) {
  e.preventDefault();
  const checked = [...document.querySelectorAll('.row-check:checked')];
  if (!checked.length) { await encAlert('Please select at least one request.', { title: 'No Selection' }); return; }
  const ok = await encConfirm('Update status for ' + checked.length + ' selected request(s)?', {
    title: 'Bulk Status Update', confirmText: 'Apply', type: 'info',
  });
  if (ok) this.submit();
});
</script>
@endpush
@endsection
