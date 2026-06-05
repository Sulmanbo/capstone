@extends('layouts.app')
@section('title', 'Leave Requests — Management')
@section('breadcrumb', 'Leave Management')

@push('head')
<style>
.lv-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width:640px){ .lv-stats { grid-template-columns: 1fr; } }
.lv-stat { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; }
.lv-stat-label { font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
.lv-stat-num { font-size: 2rem; font-weight: 800; line-height: 1; }

.lv-filters { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.lv-filters select, .lv-filters input { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: .85rem; }
.lv-filters button { background: #7c3aed; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-weight: 700; cursor: pointer; }

.lv-table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.lv-table { width: 100%; border-collapse: collapse; }
.lv-table th { padding: 10px 14px; background: #f8fafc; font-size: .73rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: left; border-bottom: 1px solid #e2e8f0; }
.lv-table td { padding: 12px 14px; font-size: .84rem; color: #334155; border-bottom: 1px solid #f8fafc; vertical-align: top; }
.lv-table tr:last-child td { border-bottom: none; }
.lv-table tr:hover td { background: #fafafa; }
.lv-badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: .71rem; font-weight: 700; }
.lv-badge--pending  { background: #fef3c7; color: #92400e; }
.lv-badge--approved { background: #d1fae5; color: #065f46; }
.lv-badge--rejected { background: #fee2e2; color: #991b1b; }
.lv-review-form select { padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .8rem; }
.lv-review-form input { padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .8rem; width: 150px; }
.lv-review-form button { background: #7c3aed; color: #fff; border: none; border-radius: 7px; padding: 5px 12px; font-size: .78rem; font-weight: 700; cursor: pointer; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
  <div>
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0;">Faculty Leave Management</h1>
    <p style="color:#64748b;font-size:.85rem;margin:4px 0 0;">Review and approve faculty leave requests</p>
  </div>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

<div class="lv-stats">
  <div class="lv-stat" style="border-left:4px solid #f59e0b;">
    <div class="lv-stat-label">⏳ Pending</div>
    <div class="lv-stat-num" style="color:#d97706;">{{ $counts['pending'] }}</div>
  </div>
  <div class="lv-stat" style="border-left:4px solid #10b981;">
    <div class="lv-stat-label">✅ Approved</div>
    <div class="lv-stat-num" style="color:#059669;">{{ $counts['approved'] }}</div>
  </div>
  <div class="lv-stat" style="border-left:4px solid #ef4444;">
    <div class="lv-stat-label">❌ Rejected</div>
    <div class="lv-stat-num" style="color:#dc2626;">{{ $counts['rejected'] }}</div>
  </div>
</div>

<form method="GET" class="lv-filters">
  <select name="status">
    <option value="">All Statuses</option>
    @foreach(['pending','approved','rejected'] as $s)
      <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or employee #…">
  <button type="submit">Filter</button>
  @if($status || $search)
    <a href="{{ route('leave.admin.index') }}" style="font-size:.83rem;color:#64748b;text-decoration:none;padding:8px 0;">Clear</a>
  @endif
</form>

{{-- Bulk Action Bar (pending only) --}}
<div id="lv-bulk-bar" style="display:none;background:#7c3aed;color:#fff;border-radius:12px;padding:12px 18px;margin-bottom:16px;align-items:center;gap:12px;flex-wrap:wrap;">
  <span id="lv-bulk-count" style="font-weight:700;font-size:.88rem;">0 selected</span>
  <form method="POST" action="{{ route('leave.bulk-review') }}" id="lv-bulk-form" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:0;">
    @csrf
    <input type="hidden" name="ids" id="lv-bulk-ids">
    <select name="status" style="padding:6px 10px;border-radius:8px;border:1.5px solid rgba(255,255,255,.4);background:rgba(255,255,255,.15);color:#fff;font-size:.84rem;font-weight:600;">
      <option value="approved">✓ Approve All</option>
      <option value="rejected">✕ Reject All</option>
    </select>
    <input type="text" name="admin_remarks" placeholder="Remarks (optional)…"
      style="padding:6px 10px;border-radius:8px;border:1.5px solid rgba(255,255,255,.4);background:rgba(255,255,255,.15);color:#fff;font-size:.84rem;width:200px;">
    <button type="submit" style="background:#fff;color:#7c3aed;border:none;border-radius:8px;padding:6px 18px;font-weight:800;cursor:pointer;font-size:.84rem;">Apply</button>
  </form>
  <button type="button" onclick="lvClearSel()" style="background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;font-size:.82rem;margin-left:auto;">✕ Clear</button>
</div>

<div class="lv-table-card">
  @if($requests->isEmpty())
    <div style="text-align:center;padding:50px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📋</div>
      <p style="font-weight:600;margin:0 0 4px;">No requests found</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="lv-table">
        <thead>
          <tr>
            <th style="width:36px;padding-left:14px;">
              <input type="checkbox" id="lv-select-all" title="Select pending" style="cursor:pointer;width:15px;height:15px;">
            </th>
            <th>#</th>
            <th>Faculty</th>
            <th>Type</th>
            <th>Period</th>
            <th>Days</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
          <tr>
            <td style="padding-left:14px;">
              @if($req->status === 'pending')
                <input type="checkbox" class="lv-check" value="{{ $req->id }}" style="cursor:pointer;width:15px;height:15px;">
              @endif
            </td>
            <td style="color:#94a3b8;font-size:.75rem;">{{ $req->id }}</td>
            <td>
              <div style="font-weight:700;color:#1e293b;">{{ $req->faculty?->first_name }} {{ $req->faculty?->last_name }}</div>
              <div style="font-size:.74rem;color:#64748b;">{{ $req->faculty?->employee_number ?? 'N/A' }}</div>
            </td>
            <td style="font-weight:600;font-size:.83rem;">{{ $req->type_label }}</td>
            <td style="font-size:.83rem;">
              {{ $req->start_date->format('M d') }} – {{ $req->end_date->format('M d, Y') }}
            </td>
            <td>{{ $req->days_count }}</td>
            <td style="max-width:180px;font-size:.8rem;color:#64748b;" title="{{ $req->reason }}">{{ Str::limit($req->reason, 55) }}</td>
            <td><span class="lv-badge lv-badge--{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
            <td style="font-size:.75rem;color:#94a3b8;">{{ $req->created_at->format('M d, Y') }}</td>
            <td>
              @if($req->status === 'pending')
              <form method="POST" action="{{ route('leave.review', $req) }}" class="lv-review-form" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                @csrf @method('PATCH')
                <select name="status" required>
                  <option value="approved">Approve</option>
                  <option value="rejected">Reject</option>
                </select>
                <input type="text" name="admin_remarks" placeholder="Remarks…">
                <button type="submit">Submit</button>
              </form>
              @else
                <span style="font-size:.78rem;color:#64748b;">
                  {{ ucfirst($req->status) }}
                  @if($req->reviewed_at)<br><span style="color:#94a3b8;font-size:.72rem;">{{ $req->reviewed_at->format('M d, Y') }}</span>@endif
                </span>
                @if($req->admin_remarks)
                  <div style="font-size:.75rem;color:#475569;margin-top:3px;font-style:italic;">"{{ $req->admin_remarks }}"</div>
                @endif
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

@push('scripts')
<script>
const lvBar   = document.getElementById('lv-bulk-bar');
const lvCount = document.getElementById('lv-bulk-count');
const lvIds   = document.getElementById('lv-bulk-ids');
const lvAll   = document.getElementById('lv-select-all');

function lvUpdate() {
  const checked = [...document.querySelectorAll('.lv-check:checked')];
  lvBar.style.display = checked.length > 0 ? 'flex' : 'none';
  lvCount.textContent = checked.length + ' selected';
  lvIds.value = checked.map(c => c.value).join(',');
}
function lvClearSel() {
  document.querySelectorAll('.lv-check').forEach(c => c.checked = false);
  if (lvAll) lvAll.checked = false;
  lvUpdate();
}
document.querySelectorAll('.lv-check').forEach(c => c.addEventListener('change', lvUpdate));
if (lvAll) lvAll.addEventListener('change', function() {
  document.querySelectorAll('.lv-check').forEach(c => c.checked = this.checked);
  lvUpdate();
});
document.getElementById('lv-bulk-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const checked = [...document.querySelectorAll('.lv-check:checked')];
  if (!checked.length) { await encAlert('Select at least one request.', { title: 'No Selection' }); return; }
  const action = this.querySelector('select[name=status]').value;
  const ok = await encConfirm('Set ' + checked.length + ' request(s) to ' + action + '?', {
    title: 'Bulk ' + (action === 'approved' ? 'Approve' : 'Reject'),
    confirmText: action === 'approved' ? 'Approve All' : 'Reject All',
    type: action === 'approved' ? 'success' : 'danger',
  });
  if (ok) this.submit();
});
</script>
@endpush
@endsection
