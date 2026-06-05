@extends('layouts.app')
@section('title', 'Document Requests')
@section('breadcrumb', 'Document Requests')

@push('head')
<style>
.doc-hero { background: linear-gradient(135deg,#1e40af,#3b82f6); border-radius: 16px; padding: 28px; color: #fff; margin-bottom: 24px; }
.doc-hero h2 { margin: 0 0 6px; font-size: 1.25rem; font-weight: 800; }
.doc-hero p { margin: 0; font-size: .88rem; opacity: .85; }

.doc-form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 24px; }
.doc-form-card h3 { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 18px; }
.doc-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:640px){ .doc-form-grid { grid-template-columns: 1fr; } }

.doc-form-field label { display: block; font-size: .78rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
.doc-form-field select,
.doc-form-field input,
.doc-form-field textarea { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: .86rem; background: #f8fafc; box-sizing: border-box; transition: border-color .15s; }
.doc-form-field select:focus,
.doc-form-field input:focus,
.doc-form-field textarea:focus { outline: none; border-color: #3b82f6; background: #fff; }
.doc-submit-btn { background: #3b82f6; color: #fff; border: none; border-radius: 9px; padding: 10px 28px; font-weight: 700; font-size: .9rem; cursor: pointer; margin-top: 8px; transition: background .15s; }
.doc-submit-btn:hover { background: #2563eb; }

.doc-list-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.doc-list-header { padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
.doc-list-header h3 { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0; }
.doc-table { width: 100%; border-collapse: collapse; }
.doc-table th { padding: 10px 16px; background: #f8fafc; text-align: left; font-size: .75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e2e8f0; }
.doc-table td { padding: 13px 16px; font-size: .85rem; color: #334155; border-bottom: 1px solid #f8fafc; }
.doc-table tr:last-child td { border-bottom: none; }

.doc-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 99px; font-size: .72rem; font-weight: 700; }
.doc-status--pending    { background: #fef3c7; color: #92400e; }
.doc-status--processing { background: #dbeafe; color: #1e40af; }
.doc-status--ready      { background: #d1fae5; color: #065f46; }
.doc-status--released   { background: #f0fdf4; color: #166534; }
.doc-status--rejected   { background: #fee2e2; color: #991b1b; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="doc-hero">
  <h2>📄 Request Official Documents</h2>
  <p>Submit your document requests below. Processing typically takes 3–5 business days.</p>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

{{-- Request Form --}}
<div class="doc-form-card">
  <h3>New Document Request</h3>
  <form method="POST" action="{{ route('documents.student.store') }}">
    @csrf
    <div class="doc-form-grid">
      <div class="doc-form-field">
        <label>Document Type *</label>
        <select name="document_type" required>
          <option value="">— Select document —</option>
          @foreach(\App\Models\DocumentRequest::$types as $key => $label)
            <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        @error('document_type')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
      </div>
      <div class="doc-form-field">
        <label>Number of Copies *</label>
        <input type="number" name="copies" min="1" max="10" value="{{ old('copies',1) }}" required>
        @error('copies')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
      </div>
    </div>
    <div class="doc-form-field" style="margin-top:14px;">
      <label>Purpose *</label>
      <textarea name="purpose" rows="2" maxlength="500" placeholder="State the purpose of this document request..." required>{{ old('purpose') }}</textarea>
      @error('purpose')<p style="color:#ef4444;font-size:.75rem;margin:4px 0 0;">{{ $message }}</p>@enderror
    </div>
    <div style="margin-top:4px;">
      <button type="submit" class="doc-submit-btn">Submit Request</button>
    </div>
  </form>
</div>

{{-- My Requests --}}
<div class="doc-list-card">
  <div class="doc-list-header">
    <h3>My Requests</h3>
    <span style="font-size:.82rem;color:#64748b;">{{ $requests->count() }} total</span>
  </div>
  @if($requests->isEmpty())
    <div style="text-align:center;padding:40px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📋</div>
      <p style="font-weight:600;margin:0 0 4px;">No requests yet</p>
      <p style="font-size:.82rem;margin:0;">Submit your first document request above.</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="doc-table">
        <thead>
          <tr>
            <th>Document</th>
            <th>Copies</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
          <tr>
            <td style="font-weight:600;">{{ $req->document_label }}</td>
            <td>{{ $req->copies }}</td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $req->purpose }}">{{ $req->purpose }}</td>
            <td><span class="doc-status doc-status--{{ $req->status }}">{{ ucfirst($req->status) }}</span></td>
            <td style="color:#64748b;font-size:.8rem;">{{ $req->remarks ?: '—' }}</td>
            <td style="color:#94a3b8;font-size:.8rem;">{{ $req->created_at->format('M d, Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
