@extends('layouts.app')
@section('title', $message->subject)
@section('content')

@push('head')
<style>
.thread-wrap  { max-width:720px; }
.bubble       { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 22px; margin-bottom:14px; }
.bubble.mine  { background:#eff6ff; border-color:#bfdbfe; }
.bubble__meta { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.bubble__avatar { width:34px; height:34px; border-radius:50%; background:#6366f1; color:#fff; font-size:.75rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.bubble__name  { font-size:.84rem; font-weight:700; color:#0f172a; }
.bubble__time  { font-size:.72rem; color:#94a3b8; margin-left:auto; }
.bubble__body  { font-size:.875rem; color:#334155; line-height:1.7; white-space:pre-wrap; }
.reply-box     { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-top:8px; }
</style>
@endpush

<div class="thread-wrap">

  <div style="margin-bottom:20px;">
    <a href="{{ route('faculty.inbox') }}" style="font-size:.82rem;color:#1d4ed8;text-decoration:none;">← Back to Inbox</a>
  </div>

  <h1 style="font-size:1.05rem;font-weight:800;color:#0f172a;margin:0 0 20px;">{{ $message->subject }}</h1>

  @if(session('success'))
  <div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.85rem;">{{ session('success') }}</div>
  @endif

  {{-- Thread --}}
  @foreach($thread as $msg)
    @php $mine = $msg->sender_id === auth()->id(); @endphp
    <div class="bubble {{ $mine ? 'mine' : '' }}">
      <div class="bubble__meta">
        <div class="bubble__avatar" style="background:{{ $mine ? '#64748b' : '#6366f1' }};">
          {{ strtoupper(substr($msg->sender->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($msg->sender->last_name ?? '', 0, 1)) }}
        </div>
        <div>
          <div class="bubble__name">{{ $msg->sender->first_name }} {{ $msg->sender->last_name }} {{ $mine ? '(You)' : '' }}</div>
          <div style="font-size:.72rem;color:#64748b;">{{ $msg->sender->role_id === '01' ? 'Student' : 'Teacher' }}</div>
        </div>
        <div class="bubble__time">{{ $msg->created_at->format('M j, Y g:i A') }}</div>
      </div>
      <div class="bubble__body">{{ $msg->body }}</div>
    </div>
  @endforeach

  {{-- Reply form --}}
  <div class="reply-box">
    <div style="font-size:.82rem;font-weight:700;color:#374151;margin-bottom:12px;">Reply</div>
    <form method="POST" action="{{ route('faculty.inbox.reply', $message) }}" style="display:flex;flex-direction:column;gap:12px;">
      @csrf
      <textarea name="body" required rows="4" maxlength="3000"
                placeholder="Write your reply…"
                style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.87rem;resize:vertical;box-sizing:border-box;">{{ old('body') }}</textarea>
      <button type="submit"
              style="padding:.55rem 1.2rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;align-self:flex-end;">
        Send Reply
      </button>
    </form>
  </div>

</div>

@endsection
