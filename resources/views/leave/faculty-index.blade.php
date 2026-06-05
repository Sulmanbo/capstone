@extends('layouts.app')
@section('title', 'Leave Requests')
@section('breadcrumb', 'Leave Requests')

@push('head')
<style>
.lv-hero { background: linear-gradient(135deg,#7c3aed,#a78bfa); border-radius: 16px; padding: 28px; color: #fff; margin-bottom: 24px; }
.lv-hero h2 { margin: 0 0 6px; font-size: 1.25rem; font-weight: 800; }
.lv-hero p { margin: 0; font-size: .88rem; opacity: .85; }

.lv-form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; }
.lv-form-card h3 { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 18px; }
.lv-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:640px){ .lv-form-grid { grid-template-columns: 1fr; } }
.lv-field label { display: block; font-size: .78rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
.lv-field select,
.lv-field input,
.lv-field textarea { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: .86rem; background: #f8fafc; box-sizing: border-box; transition: border-color .15s; }
.lv-field select:focus, .lv-field input:focus, .lv-field textarea:focus { outline: none; border-color: #7c3aed; background: #fff; }
.lv-submit { background: #7c3aed; color: #fff; border: none; border-radius: 9px; padding: 10px 28px; font-weight: 700; font-size: .9rem; cursor: pointer; margin-top: 8px; transition: background .15s; }
.lv-submit:hover { background: #6d28d9; }

.lv-list-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.lv-table { width: 100%; border-collapse: collapse; }
.lv-table th { padding: 10px 16px; background: #f8fafc; text-align: left; font-size: .73rem; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
.lv-table td { padding: 12px 16px; font-size: .85rem; color: #334155; border-bottom: 1px solid #f8fafc; }
.lv-table tr:last-child td { border-bottom: none; }
.lv-badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: .71rem; font-weight: 700; }
.lv-badge--pending  { background: #fef3c7; color: #92400e; }
.lv-badge--approved { background: #d1fae5; color: #065f46; }
.lv-badge--rejected { background: #fee2e2; color: #991b1b; }
</style>
@endpush

@section('content')

<div class="lv-hero">
  <h2>🏖️ Faculty Leave Management</h2>
  <p>Submit leave requests for approval. Allow 1–2 business days for processing.</p>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

{{-- Form --}}
<div class="lv-form-card">
  <h3>New Leave Request</h3>
  <form method="POST" action="{{ route('leave.faculty.store') }}">
    @csrf
    <div class="lv-form-grid">
      <div class="lv-field">
        <label>Leave Type *</label>
        <select name="leave_type" required>
          <option value="">— Select type —</option>
          @foreach(\App\Models\LeaveRequest::$types as $key => $label)
            <option value="{{ $key }}" {{ old('leave_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        @error('leave_type')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
      </div>
      <div></div>
      <div class="lv-field">
        <label>Start Date *</label>
        <input type="date" name="start_date" required value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}">
        @error('start_date')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
      </div>
      <div class="lv-field">
        <label>End Date *</label>
        <input type="date" name="end_date" required value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}">
        @error('end_date')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
      </div>
    </div>
    <div class="lv-field" style="margin-top:14px;">
      <label>Reason *</label>
      <textarea name="reason" rows="3" required maxlength="1000" placeholder="Briefly explain the reason for your leave...">{{ old('reason') }}</textarea>
      @error('reason')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="lv-submit">Submit Request</button>
  </form>
</div>

{{-- History --}}
<div class="lv-list-card">
  <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
    <h3 style="margin:0;font-size:1rem;font-weight:700;color:#1e293b;">My Leave History</h3>
    <span style="font-size:.82rem;color:#64748b;">{{ $requests->count() }} total</span>
  </div>
  @if($requests->isEmpty())
    <div style="text-align:center;padding:40px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📋</div>
      <p style="font-weight:600;margin:0 0 4px;">No requests yet</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="lv-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Dates</th>
            <th>Days</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Submitted</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
          <tr>
            <td style="font-weight:600;">{{ $req->type_label }}</td>
            <td>
              {{ $req->start_date->format('M d') }} – {{ $req->end_date->format('M d, Y') }}
            </td>
            <td>{{ $req->days_count }}</td>
            <td style="max-width:200px;font-size:.8rem;color:#64748b;" title="{{ $req->reason }}">{{ Str::limit($req->reason, 60) }}</td>
            <td><span class="lv-badge lv-badge--{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
            <td style="font-size:.8rem;color:#64748b;">{{ $req->admin_remarks ?: '—' }}</td>
            <td style="font-size:.78rem;color:#94a3b8;">{{ $req->created_at->format('M d, Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
