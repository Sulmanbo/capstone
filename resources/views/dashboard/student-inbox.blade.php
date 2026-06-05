@extends('layouts.app')
@section('title', 'Inbox')
@section('content')

@push('head')
<style>
.msg-layout   { display:grid; grid-template-columns:320px 1fr; gap:20px; align-items:start; max-width:1000px; }
.msg-sidebar  { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.msg-tab-bar  { display:flex; border-bottom:1px solid #e2e8f0; }
.msg-tab      { flex:1; padding:.65rem; font-size:.78rem; font-weight:700; text-align:center; cursor:pointer; color:#64748b; border-bottom:2px solid transparent; transition:all .15s; }
.msg-tab.active { color:#1d4ed8; border-bottom-color:#1d4ed8; }
.msg-row      { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-bottom:1px solid #f1f5f9; cursor:pointer; text-decoration:none; transition:background .1s; }
.msg-row:hover { background:#f8fafc; }
.msg-row.unread { background:#eff6ff; }
.msg-avatar   { width:36px; height:36px; border-radius:50%; background:#1d4ed8; color:#fff; font-size:.78rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.msg-meta     { flex:1; min-width:0; }
.msg-name     { font-size:.82rem; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.msg-subject  { font-size:.76rem; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.msg-time     { font-size:.68rem; color:#94a3b8; white-space:nowrap; }
.msg-unread-dot { width:8px; height:8px; border-radius:50%; background:#1d4ed8; flex-shrink:0; margin-top:4px; }
.msg-compose  { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.msg-compose__header { padding:16px 20px; border-bottom:1px solid #e2e8f0; font-weight:700; font-size:.92rem; color:#0f172a; }
.msg-compose__body   { padding:20px; display:flex; flex-direction:column; gap:14px; }
</style>
@endpush

<div style="margin-bottom:24px;">
  <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Inbox</h1>
  <p style="font-size:.82rem;color:#64748b;margin:0;">Send messages to your teachers and view replies.</p>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.85rem;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.85rem;">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="msg-layout">

  {{-- ── Left: message list ─────────────────────────────────────── --}}
  <div class="msg-sidebar">
    <div class="msg-tab-bar">
      <div class="msg-tab {{ $tab === 'inbox' ? 'active' : '' }}" onclick="switchTab('inbox', this)">
        Inbox @if($unreadCount > 0)<span style="background:#1d4ed8;color:#fff;border-radius:99px;font-size:.65rem;padding:.1rem .4rem;margin-left:4px;">{{ $unreadCount }}</span>@endif
      </div>
      <div class="msg-tab {{ $tab === 'sent' ? 'active' : '' }}" onclick="switchTab('sent', this)">Sent</div>
    </div>

    {{-- Inbox list --}}
    <div id="pane-inbox" style="{{ $tab !== 'inbox' ? 'display:none' : '' }}">
      @forelse($inbox as $m)
        <a href="{{ route('student.inbox.show', $m) }}" class="msg-row {{ $m->isUnread() ? 'unread' : '' }}">
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

    {{-- Sent list --}}
    <div id="pane-sent" style="{{ $tab !== 'sent' ? 'display:none' : '' }}">
      @forelse($sent as $m)
        <a href="{{ route('student.inbox.show', $m) }}" class="msg-row">
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

  {{-- ── Right: compose form ────────────────────────────────────── --}}
  <div class="msg-compose">
    <div class="msg-compose__header">New Message</div>
    <div class="msg-compose__body">
      @if($recipients->isEmpty())
        <div style="padding:20px;text-align:center;color:#94a3b8;font-size:.84rem;">
          You have no enrolled teachers to message yet.<br>
          <span style="font-size:.76rem;">Contact your admin if this is incorrect.</span>
        </div>
      @else
        <form method="POST" action="{{ route('student.inbox.store') }}" style="display:flex;flex-direction:column;gap:12px;">
          @csrf

          <div>
            <label style="display:block;font-size:.75rem;font-weight:700;color:#475569;margin-bottom:5px;">To (Teacher)</label>
            <select name="recipient_id" required style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.87rem;background:#fff;">
              <option value="">— Select a teacher —</option>
              @foreach($recipients as $r)
                <option value="{{ $r->id }}" {{ old('recipient_id') == $r->id ? 'selected' : '' }}>
                  {{ $r->last_name }}, {{ $r->first_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label style="display:block;font-size:.75rem;font-weight:700;color:#475569;margin-bottom:5px;">Subject</label>
            <input type="text" name="subject" required maxlength="255" value="{{ old('subject') }}"
                   placeholder="e.g. Question about homework"
                   style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.87rem;box-sizing:border-box;">
          </div>

          <div>
            <label style="display:block;font-size:.75rem;font-weight:700;color:#475569;margin-bottom:5px;">Message</label>
            <textarea name="body" required rows="6" maxlength="3000"
                      placeholder="Write your message here…"
                      style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.87rem;resize:vertical;box-sizing:border-box;">{{ old('body') }}</textarea>
          </div>

          <button type="submit"
                  style="padding:.6rem 1.4rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;align-self:flex-end;">
            Send Message
          </button>
        </form>
      @endif
    </div>
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
