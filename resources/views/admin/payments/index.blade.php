@extends('layouts.app')
@section('title', 'Payments')
@section('breadcrumb', 'Payments')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Payments</h1>
      <p class="enc-page__subtitle">Review uploaded proofs and confirm payments. Students must be <strong>paid</strong> before you can enlist them into a section.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.payments.fees') }}" style="background:#0f172a;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">Manage Fees</a>
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

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
  @foreach(['paid'=>['Paid','#166534'],'pending'=>['Pending','#92400e'],'failed'=>['Rejected','#991b1b']] as $key=>$meta)
  <div class="enc-card"><div class="enc-card__body" style="padding:18px 20px;">
    <div style="font-size:.72rem;font-weight:700;color:{{ $meta[1] }};text-transform:uppercase;letter-spacing:.05em;">{{ $meta[0] }}</div>
    <div style="font-size:1.8rem;font-weight:800;color:#0f172a;margin-top:2px;">{{ $stats[$key] }}</div>
  </div></div>
  @endforeach
</div>

{{-- Filters --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:220px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>{{ $yr->year_label }} ({{ ucfirst($yr->status) }})</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Status</label>
        <select name="status" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All</option>
          @foreach(['pending','paid','failed','refunded'] as $st)
            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>
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
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Submitted</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Student</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Grade</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Account</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Reference</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Amount</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Proof</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $p)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;color:#64748b;font-size:.78rem;">{{ $p->created_at->format('M d, Y') }}<br>{{ $p->created_at->format('g:i A') }}</td>
            <td style="padding:12px 14px;color:#0f172a;font-weight:600;">{{ $p->student?->last_name }}, {{ $p->student?->first_name }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $p->grade_level }}</td>
            <td style="padding:12px 14px;color:#0f172a;">
              <div>{{ $p->account_label }}</div>
              <div style="font-family:monospace;font-size:.75rem;color:#94a3b8;">{{ $p->account_number }}</div>
            </td>
            <td style="padding:12px 14px;color:#64748b;font-family:monospace;font-size:.8rem;">{{ $p->reference_number }}</td>
            <td style="padding:12px 14px;color:#0f172a;font-weight:600;">₱{{ number_format($p->amount, 2) }}</td>
            <td style="padding:12px 14px;">
              @if($p->proof_path)
                <a href="{{ asset('storage/'.$p->proof_path) }}" target="_blank" style="color:#1d4ed8;font-size:.8rem;font-weight:600;text-decoration:none;">View ↗</a>
              @else
                <span style="color:#cbd5e1;">—</span>
              @endif
            </td>
            <td style="padding:12px 14px;">
              @php $c = ['paid'=>['#166534','#86efac','#f0fdf4'],'pending'=>['#92400e','#fcd34d','#fffbeb'],'failed'=>['#991b1b','#fca5a5','#fef2f2'],'refunded'=>['#475569','#cbd5e1','#f8fafc']][$p->status] ?? ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $p->status }}</span>
            </td>
            <td style="padding:12px 14px;text-align:right;white-space:nowrap;">
              @if($p->status === 'pending')
                <form action="{{ route('admin.payments.confirm', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Confirm this payment? The student becomes enlistable.');">
                  @csrf
                  <button type="submit" style="background:none;border:none;color:#166534;font-size:.82rem;font-weight:700;cursor:pointer;margin-right:8px;">Confirm</button>
                </form>
                <form action="{{ route('admin.payments.reject', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reject this payment?');">
                  @csrf
                  <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:700;cursor:pointer;">Reject</button>
                </form>
              @elseif($p->status === 'paid')
                <span style="color:#94a3b8;font-size:.78rem;">✓ {{ $p->paid_at?->format('M d, Y') }}</span>
              @else
                <span style="color:#cbd5e1;font-size:.8rem;">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="9" style="padding:40px;text-align:center;color:#94a3b8;">No payments found for this filter.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="margin-top:16px;">{{ $payments->links() }}</div>

@endsection
