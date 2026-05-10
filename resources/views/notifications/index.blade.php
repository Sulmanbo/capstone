@extends('layouts.app')

@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Notifications</h1>
      <p class="enc-page__subtitle">All alerts and updates for your account.</p>
    </div>
  </div>
</div>

@if($notifications->isEmpty())
  <div class="enc-card" style="padding:3rem;text-align:center;color:var(--gray-400);">
    No notifications yet.
  </div>
@else
<div class="enc-card" style="padding:0;overflow:hidden;">
  @foreach($notifications as $n)
    @php
      $data   = $n->data;
      $unread = is_null($n->read_at);
      $type   = $data['type'] ?? 'info';

      $colors = [
        'grade_finalized'    => ['#dcfce7','#166534','#22c55e'],
        'grade_locked'       => ['#dbeafe','#1e40af','#3b82f6'],
        'complaint_received' => ['#fef3c7','#92400e','#f59e0b'],
        'complaint_responded'=> ['#f0fdf4','#166534','#22c55e'],
        'unlock_requested'   => ['#fef9c3','#854d0e','#eab308'],
        'unlock_decided'     => ['#ede9fe','#4c1d95','#8b5cf6'],
      ];
      [$bg, $text, $dot] = $colors[$type] ?? ['#f1f5f9','#374151','#94a3b8'];
    @endphp
    <div style="display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.25rem;
      border-bottom:1px solid rgba(15,23,42,.05);
      background:{{ $unread ? '#fafbff' : '#fff' }};
      transition:background .15s;">

      {{-- Type dot ─────────────────────────────────── --}}
      <div style="width:10px;height:10px;border-radius:50%;background:{{ $dot }};
        margin-top:.35rem;flex-shrink:0;"></div>

      {{-- Content ──────────────────────────────────── --}}
      <div style="flex:1;min-width:0;">
        <div style="font-weight:{{ $unread ? '700' : '500' }};font-size:.9rem;color:var(--navy);margin-bottom:.15rem;">
          {{ $data['title'] ?? 'Notification' }}
        </div>
        <div style="font-size:.84rem;color:var(--gray-500);line-height:1.5;">
          {{ $data['message'] ?? '' }}
        </div>
        @if(!empty($data['url']))
        <a href="{{ $data['url'] }}"
           style="font-size:.78rem;color:var(--primary);font-weight:700;text-decoration:none;margin-top:.3rem;display:inline-block;">
          View →
        </a>
        @endif
      </div>

      {{-- Time ─────────────────────────────────────── --}}
      <div style="font-size:.74rem;color:var(--gray-400);white-space:nowrap;flex-shrink:0;">
        {{ $n->created_at->diffForHumans() }}
      </div>

    </div>
  @endforeach

  @if($notifications->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $notifications->links() }}
  </div>
  @endif
</div>
@endif
@endsection
