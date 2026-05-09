@extends('layouts.app')

@section('title', 'Account Balance Summary')
@section('breadcrumb', 'Account Balance Summary')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Account Balance Summary</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — Financial Overview</p>
    </div>
  </div>
</div>

<div class="enc-stats">
  <div class="enc-stat-card student-glass-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">₱{{ number_format($balance['total_fees'], 2) }}</div>
      <div class="enc-stat-label">Total Fees</div>
    </div>
  </div>

  <div class="enc-stat-card student-glass-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">₱{{ number_format($balance['total_paid'], 2) }}</div>
      <div class="enc-stat-label">Total Paid</div>
    </div>
  </div>

  <div class="enc-stat-card student-glass-card">
    <div class="enc-stat-icon enc-stat-icon--orange">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">₱{{ number_format($balance['balance_due'], 2) }}</div>
      <div class="enc-stat-label">Balance Due</div>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Transaction History</div>
    <span class="enc-card__meta">All payments and charges on your account</span>
  </div>
  <div class="enc-card__body">
    @if(empty($balance['transactions']))
      <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.45);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.25)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
        </svg>
        <p style="font-size:.95rem;font-weight:600;">No Transactions Found</p>
        <p style="font-size:.82rem;margin-top:.25rem;">No payment records are available at this time.</p>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:1px solid rgba(255,255,255,.1);">
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Date</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Description</th>
              <th style="text-align:right;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Amount</th>
              <th style="text-align:left;padding:.6rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.5);">Type</th>
            </tr>
          </thead>
          <tbody>
            @foreach($balance['transactions'] as $tx)
            <tr style="border-bottom:1px solid rgba(255,255,255,.06);">
              <td style="padding:.65rem .75rem;font-size:.85rem;color:rgba(255,255,255,.5);">{{ $tx['date'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.88rem;">{{ $tx['description'] }}</td>
              <td style="padding:.65rem .75rem;font-size:.88rem;text-align:right;">₱{{ number_format($tx['amount'], 2) }}</td>
              <td style="padding:.65rem .75rem;">
                <span style="padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;background:{{ $tx['type']==='payment' ? 'rgba(34,197,94,.15)' : 'rgba(239,68,68,.15)' }};color:{{ $tx['type']==='payment' ? '#4ade80' : '#f87171' }};">
                  {{ ucfirst($tx['type']) }}
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
