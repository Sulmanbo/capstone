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
    @if($unreadCount > 0)
      <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="display:inline;">
        @csrf
        <button type="submit" style="padding:.5rem 1.2rem;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#475569;font-size:.82rem;font-weight:600;cursor:pointer;">
          Mark all as read
        </button>
      </form>
    @endif
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.85rem;">{{ session('success') }}</div>
@endif

@if($notifications->isEmpty())
  <div class="enc-card" style="padding:40px;text-align:center;color:#94a3b8;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
    </svg>
    No notifications yet. You're all caught up!
  </div>
@else
<div class="enc-card" style="padding:0;overflow:hidden;">
  @foreach($notifications as $n)
    @php
      $unread = is_null($n->read_at);
      $type = $n->type; // 'message', 'grade_submitted', 'announcement', etc.

      $colors = [
        'message'           => '#3b82f6',
        'grade_submitted'   => '#f59e0b',
        'grade_verified'    => '#22c55e',
        'announcement'      => '#8b5cf6',
        'enrollment'        => '#06b6d4',
      ];
      $dotColor = $colors[$type] ?? '#94a3b8';
    @endphp
    <div style="display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.25rem;
      border-bottom:1px solid #f1f5f9;
      background:{{ $unread ? '#eff6ff' : '#fff' }};
      transition:background .15s;">

      {{-- Type dot ─────────────────────────────────── --}}
      <div style="width:8px;height:8px;border-radius:50%;background:{{ $dotColor }};
        margin-top:.4rem;flex-shrink:0;"></div>

      {{-- Content ──────────────────────────────────── --}}
      <div style="flex:1;min-width:0;">
        <div style="font-weight:{{ $unread ? '700' : '600' }};font-size:.9rem;color:#0f172a;margin-bottom:.15rem;">
          {{ $n->title }}
        </div>
        <div style="font-size:.84rem;color:#64748b;line-height:1.5;">
          {{ $n->body }}
        </div>
      </div>

      {{-- Actions ──────────────────────────────────── --}}
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
        <div style="font-size:.72rem;color:#94a3b8;white-space:nowrap;">
          {{ $n->created_at->diffForHumans() }}
        </div>
        @if($unread)
          <form method="POST" action="{{ route('notifications.mark-read', $n) }}" style="display:inline;">
            @csrf
            <button type="submit" style="font-size:.72rem;color:#1d4ed8;background:none;border:none;cursor:pointer;font-weight:600;">Mark read</button>
          </form>
        @endif
      </div>

    </div>
  @endforeach

  @if($notifications->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid #e2e8f0;">
    {{ $notifications->links() }}
  </div>
  @endif
</div>
@endif
@endsection
