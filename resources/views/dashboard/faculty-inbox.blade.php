@extends('layouts.app')
@section('title', 'Inbox')
@section('content')

@push('head')
<style>
.msg-layout   { display:grid; grid-template-columns:340px 1fr; gap:20px; align-items:start; max-width:1000px; }
.msg-sidebar  { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.msg-tab-bar  { display:flex; border-bottom:1px solid #e2e8f0; }
.msg-tab      { flex:1; padding:.65rem; font-size:.78rem; font-weight:700; text-align:center; cursor:pointer; color:#64748b; border-bottom:2px solid transparent; transition:all .15s; }
.msg-tab.active { color:#1d4ed8; border-bottom-color:#1d4ed8; }
.msg-row      { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-bottom:1px solid #f1f5f9; cursor:pointer; text-decoration:none; transition:background .1s; }
.msg-row:hover { background:#f8fafc; }
.msg-row.unread { background:#eff6ff; }
.msg-avatar   { width:36px; height:36px; border-radius:50%; background:#6366f1; color:#fff; font-size:.78rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.msg-meta     { flex:1; min-width:0; }
.msg-name     { font-size:.82rem; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.msg-subject  { font-size:.76rem; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.msg-time     { font-size:.68rem; color:#94a3b8; white-space:nowrap; }
.msg-unread-dot { width:8px; height:8px; border-radius:50%; background:#1d4ed8; flex-shrink:0; margin-top:4px; }
</style>
@endpush

<div style="margin-bottom:24px;">
  <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Student Inbox</h1>
  <p style="font-size:.82rem;color:#64748b;margin:0;">Messages from your students. Click a message to read and reply.</p>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.85rem;">{{ session('success') }}</div>
@endif

<div class="msg-layout">

  {{-- ── Message list ────────────────────────────────────────────── --}}
  <div class="msg-sidebar">
    <div class="msg-tab-bar">
      <div class="msg-tab {{ $tab === 'inbox' ? 'active' : '' }}" onclick="switchTab('inbox', this)">
        Inbox @if($unreadCount > 0)<span style="background:#1d4ed8;color:#fff;border-radius:99px;font-size:.65rem;padding:.1rem .4rem;margin-left:4px;">{{ $unreadCount }}</span>@endif
      </div>
      <div class="msg-tab {{ $tab === 'sent' ? 'active' : '' }}" onclick="switchTab('sent', this)">Sent</div>
    </div>

    <div id="pane-inbox" style="{{ $tab !== 'inbox' ? 'display:none' : '' }}">
      @forelse($inbox as $m)
        <a href="{{ route('faculty.inbox.show', $m) }}" class="msg-row {{ $m->isUnread() ? 'unread' : '' }}">
          <div class="msg-avatar">{{ strtoupper(substr($m->sender->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($m->sender->last_name ?? '', 0, 1)) }}</div>
          <div class="msg-meta">
            <div class="msg-name">{{ $m->sender->last_name }}, {{ $m->sender->first_name }}</div>
            <div class="msg-subject">{{ $m->subject }}</div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
            <div class="msg-time">{{ $m->created_at->diffForHumans() }}</div>
            @if($m->isUnread())<div class="msg-unread-dot"></div>@endif
          </div>
        </a>
      @empty
        <div style="padding:36px 20px;text-align:center;color:#94a3b8;font-size:.82rem;">No messages yet.</div>
      @endforelse
    </div>

    <div id="pane-sent" style="{{ $tab !== 'sent' ? 'display:none' : '' }}">
      @forelse($sent as $m)
        <a href="{{ route('faculty.inbox.show', $m) }}" class="msg-row">
          <div class="msg-avatar" style="background:#64748b;">{{ strtoupper(substr($m->recipient->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($m->recipient->last_name ?? '', 0, 1)) }}</div>
          <div class="msg-meta">
            <div class="msg-name">To: {{ $m->recipient->last_name }}, {{ $m->recipient->first_name }}</div>
            <div class="msg-subject">{{ $m->subject }}</div>
          </div>
          <div class="msg-time">{{ $m->created_at->diffForHumans() }}</div>
        </a>
      @empty
        <div style="padding:36px 20px;text-align:center;color:#94a3b8;font-size:.82rem;">No sent messages.</div>
      @endforelse
    </div>
  </div>

  {{-- ── Right panel prompt ──────────────────────────────────────── --}}
  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:40px;text-align:center;color:#94a3b8;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
    </svg>
    <p style="font-size:.84rem;">Select a message on the left to read and reply.</p>
  </div>

</div>

@push('scripts')
<script>
function switchTab(name, el) {
  document.querySelectorAll('.msg-tab').forEach(function(t){ t.classList.remove('active'); });
  el.classList.add('active');
  document.getElementById('pane-inbox').style.display = name === 'inbox' ? '' : 'none';
  document.getElementById('pane-sent').style.display  = name === 'sent'  ? '' : 'none';
}
</script>
@endpush

@endsection
