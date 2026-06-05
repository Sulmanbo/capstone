@extends('layouts.app')
@section('title', 'Payments')
@section('breadcrumb', 'Payments')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Enrollment Payment</h1>
      <p class="enc-page__subtitle">Pay your enrollment fee to be enlisted into a section. Choose an account, transfer the amount, and upload your receipt.</p>
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

@if(!$activeYear)
<div class="enc-card"><div class="enc-card__body" style="padding:40px;text-align:center;color:#64748b;">There is no active academic year. Please check back later.</div></div>
@elseif(!$fee)
<div class="enc-card"><div class="enc-card__body" style="padding:40px;text-align:center;color:#64748b;">
  No enrollment fee has been set for your grade level ({{ $gradeLevel ?? 'unknown' }}) yet. Please contact the registrar.
</div></div>
@else

{{-- Fee summary --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
    <div>
      <div style="font-size:.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Enrollment Fee — {{ $gradeLevel }} · {{ $activeYear->year_label }}</div>
      <div style="font-size:2rem;font-weight:800;color:#0f172a;margin-top:4px;">₱{{ number_format($fee->amount, 2) }}</div>
    </div>
    <div>
      @if($hasPaid)
        <span style="display:inline-block;padding:.5rem 1.2rem;border-radius:999px;font-size:.9rem;font-weight:800;color:#166534;background:#f0fdf4;border:1px solid #86efac;">✓ PAID</span>
      @else
        <span style="display:inline-block;padding:.5rem 1.2rem;border-radius:999px;font-size:.9rem;font-weight:800;color:#92400e;background:#fffbeb;border:1px solid #fcd34d;">PAYMENT REQUIRED</span>
      @endif
    </div>
  </div>
</div>

@if($hasPaid)
<div class="enc-card">
  <div class="enc-card__body" style="padding:40px;text-align:center;">
    <div style="font-size:1.1rem;font-weight:700;color:#166534;margin-bottom:8px;">You're all paid up!</div>
    <p style="color:#64748b;font-size:.9rem;margin:0;">The registrar will enlist you into your section. Check your dashboard for updates.</p>
  </div>
</div>

@elseif(empty($accounts))
<div class="enc-card"><div class="enc-card__body" style="padding:40px;text-align:center;color:#64748b;">
  No payment accounts are configured. Contact the administrator.
</div></div>

@else

{{-- Instructions --}}
@if($instructions)
<div style="margin-bottom:20px;padding:14px 18px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;color:#1e40af;font-size:.85rem;line-height:1.55;">
  {{ $instructions }}
</div>
@endif

{{-- Single combined form: tabs on top pick the account, body shows QR + submit --}}
<form method="POST" action="{{ route('student.payments.submit') }}" enctype="multipart/form-data">
  @csrf

  {{-- Account tabs --}}
  <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;" role="tablist">
    @foreach($accounts as $i => $acct)
    <label style="cursor:pointer;">
      <input type="radio" name="account_id" value="{{ $acct['id'] }}" {{ $i === 0 ? 'checked' : '' }}
             style="display:none;" class="acct-radio" onchange="showAccount('{{ $acct['id'] }}')">
      <span class="acct-tab acct-tab-{{ $acct['id'] }}"
            style="display:inline-block;padding:.55rem 1.2rem;border-radius:8px;font-size:.875rem;font-weight:700;
                   color:{{ $i === 0 ? '#fff' : '#475569' }};
                   background:{{ $i === 0 ? '#1d4ed8' : '#f8fafc' }};
                   border:1px solid {{ $i === 0 ? '#1d4ed8' : '#e2e8f0' }};">
        {{ $acct['label'] }}
      </span>
    </label>
    @endforeach
  </div>

  {{-- Per-account panels --}}
  @foreach($accounts as $i => $acct)
  <div id="acct-panel-{{ $acct['id'] }}" class="acct-panel" style="display:{{ $i === 0 ? 'grid' : 'none' }};grid-template-columns:1fr 1fr;gap:24px;">

    {{-- Account details + QR --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">{{ $acct['label'] }} — Recipient Details</div></div>
      <div class="enc-card__body" style="padding:24px;display:flex;flex-direction:column;align-items:center;gap:18px;">

        @if(!empty($acct['qr_path']))
        <div style="padding:12px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;">
          <img src="{{ asset($acct['qr_path']) }}" alt="QR code for {{ $acct['label'] }}"
               style="width:200px;height:200px;display:block;">
        </div>
        <p style="font-size:.78rem;color:#94a3b8;margin:0;text-align:center;">
          Scan this QR with your banking or e-wallet app.<br>
          <em>(Placeholder QR — your school will swap this for the live one.)</em>
        </p>
        @endif

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;font-size:.85rem;color:#334155;line-height:1.7;width:100%;">
          <div><strong>Account Name:</strong> {{ $acct['account_name'] }}</div>
          <div style="display:flex;align-items:center;gap:8px;">
            <strong>Account Number:</strong>
            <code style="background:#fff;padding:2px 8px;border-radius:4px;font-family:monospace;border:1px solid #e2e8f0;" id="acct-num-{{ $acct['id'] }}">{{ $acct['account_number'] }}</code>
            <button type="button" onclick="copyAcctNum('{{ $acct['id'] }}')"
                    style="background:none;border:1px solid #cbd5e1;border-radius:6px;padding:2px 8px;font-size:.72rem;font-weight:600;color:#475569;cursor:pointer;">Copy</button>
          </div>
          @if(!empty($acct['branch']))
          <div><strong>Branch:</strong> {{ $acct['branch'] }}</div>
          @endif
        </div>
      </div>
    </div>

    {{-- Proof submission form --}}
    <div class="enc-card">
      <div class="enc-card__header"><div class="enc-card__title">Submit Proof of Payment</div></div>
      <div class="enc-card__body" style="padding:24px;display:flex;flex-direction:column;gap:14px;">
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Transfer Reference No.</label>
          <input type="text" name="reference_number" required maxlength="100" placeholder="e.g. 1234567890"
                 value="{{ old('reference_number') }}"
                 style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.9rem;">
        </div>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Proof of Payment (screenshot)</label>
          <input type="file" name="proof" accept="image/*" required style="width:100%;font-size:.85rem;">
          <p style="font-size:.72rem;color:#94a3b8;margin:6px 0 0;">Clear photo or screenshot. JPG/PNG up to 4 MB.</p>
        </div>
        <button type="submit" style="margin-top:6px;padding:.7rem 1.2rem;border:none;border-radius:8px;background:#065f46;color:#fff;font-size:.95rem;font-weight:700;cursor:pointer;">
          Submit Proof of Payment
        </button>
        <p style="font-size:.72rem;color:#64748b;margin:0;">The registrar will verify your proof and mark you as paid. You'll see the status in your Payment History below.</p>
      </div>
    </div>
  </div>
  @endforeach
</form>

<script>
function showAccount(id) {
  // Panels
  document.querySelectorAll('.acct-panel').forEach(p => p.style.display = 'none');
  const target = document.getElementById('acct-panel-' + id);
  if (target) target.style.display = 'grid';

  // Tab styling
  document.querySelectorAll('.acct-tab').forEach(t => {
    t.style.color = '#475569';
    t.style.background = '#f8fafc';
    t.style.borderColor = '#e2e8f0';
  });
  const tab = document.querySelector('.acct-tab-' + id);
  if (tab) {
    tab.style.color = '#fff';
    tab.style.background = '#1d4ed8';
    tab.style.borderColor = '#1d4ed8';
  }
}

function copyAcctNum(id) {
  const el = document.getElementById('acct-num-' + id);
  if (!el) return;
  navigator.clipboard.writeText(el.textContent.trim())
    .then(() => { el.style.background = '#f0fdf4'; setTimeout(() => el.style.background = '#fff', 800); })
    .catch(() => { /* clipboard may fail on http; ignore */ });
}
</script>
@endif

{{-- Payment history --}}
@if($payments->isNotEmpty())
<div class="enc-card" style="margin-top:24px;">
  <div class="enc-card__header"><div class="enc-card__title">Payment History</div></div>
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Date</th>
            <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Account</th>
            <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Reference</th>
            <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Amount</th>
            <th style="padding:11px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $p)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:11px 14px;color:#64748b;">{{ $p->created_at->format('M d, Y g:i A') }}</td>
            <td style="padding:11px 14px;color:#0f172a;">{{ $p->account_label }}</td>
            <td style="padding:11px 14px;color:#64748b;font-family:monospace;font-size:.8rem;">{{ $p->reference_number }}</td>
            <td style="padding:11px 14px;color:#0f172a;font-weight:600;">₱{{ number_format($p->amount, 2) }}</td>
            <td style="padding:11px 14px;">
              @php $c = ['paid'=>['#166534','#86efac','#f0fdf4'],'pending'=>['#92400e','#fcd34d','#fffbeb'],'failed'=>['#991b1b','#fca5a5','#fef2f2'],'refunded'=>['#475569','#cbd5e1','#f8fafc']][$p->status] ?? ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $p->status }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@endif

@endsection
