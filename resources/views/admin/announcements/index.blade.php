@extends('layouts.app')
@section('title', 'Announcements')
@section('breadcrumb', 'Announcements')

@push('head')
<style>
.ann-grid { display:grid; grid-template-columns:380px 1fr; gap:24px; align-items:start; }
@media(max-width:900px){ .ann-grid { grid-template-columns:1fr; } }

.ann-form-card {
  background:#fff; border:1px solid #e2e8f0; border-radius:16px;
  padding:24px; box-shadow:0 2px 12px rgba(15,23,42,.05); position:sticky; top:24px;
}
.ann-form-card h3 { font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 20px; }

.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:#374151; margin-bottom:6px; }
.form-control {
  width:100%; padding:.6rem .85rem; border:1px solid #d1d5db;
  border-radius:10px; font-size:.88rem; color:#0f172a;
  transition:border-color .15s, box-shadow .15s; box-sizing:border-box;
}
.form-control:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.1); }
textarea.form-control { resize:vertical; min-height:90px; }

.chip-group { display:flex; flex-wrap:wrap; gap:8px; }
.chip-input { display:none; }
.chip-label {
  padding:.35rem .85rem; border-radius:999px; font-size:.78rem; font-weight:600;
  border:1.5px solid #e2e8f0; color:#64748b; cursor:pointer;
  transition:all .15s;
}
.chip-input:checked + .chip-label { border-color:#4f46e5; background:#eef2ff; color:#4338ca; }

.btn-post {
  width:100%; padding:.7rem 1rem; border:none; border-radius:10px;
  background:#4f46e5; color:#fff; font-weight:700; font-size:.9rem;
  cursor:pointer; transition:background .15s, transform .1s;
}
.btn-post:hover { background:#4338ca; transform:translateY(-1px); }

/* List styles */
.ann-list { display:flex; flex-direction:column; gap:12px; }
.ann-item {
  background:#fff; border:1px solid #e2e8f0; border-left:4px solid transparent;
  border-radius:12px; padding:16px 18px;
  display:flex; align-items:flex-start; gap:14px;
  box-shadow:0 1px 6px rgba(15,23,42,.04);
}
.ann-item--high   { border-left-color:#ef4444; }
.ann-item--medium { border-left-color:#f59e0b; }
.ann-item--low    { border-left-color:#4f46e5; }
.ann-item--inactive { opacity:.5; }
.ann-body { flex:1; min-width:0; }
.ann-title { font-weight:700; color:#0f172a; font-size:.92rem; }
.ann-msg   { font-size:.83rem; color:#64748b; margin-top:3px; line-height:1.5; }
.ann-meta  { font-size:.74rem; color:#94a3b8; margin-top:6px; display:flex; gap:12px; flex-wrap:wrap; }
.ann-badge {
  display:inline-block; font-size:.7rem; font-weight:700;
  padding:.15rem .55rem; border-radius:999px;
}
.b-high   { background:rgba(239,68,68,.1);  color:#dc2626; }
.b-medium { background:rgba(245,158,11,.1); color:#d97706; }
.b-low    { background:rgba(79,70,229,.1);  color:#4338ca; }
.b-all      { background:rgba(16,185,129,.1); color:#059669; }
.b-student  { background:rgba(99,102,241,.1); color:#4338ca; }
.b-faculty  { background:rgba(245,158,11,.1); color:#d97706; }
.b-registrar{ background:rgba(14,165,233,.1); color:#0284c7; }

.ann-actions { display:flex; gap:8px; flex-shrink:0; }
.ann-btn {
  padding:.35rem .7rem; border-radius:8px; font-size:.76rem; font-weight:600;
  border:1px solid; cursor:pointer; transition:all .15s; text-decoration:none;
  display:inline-flex; align-items:center; gap:4px;
}
.ann-btn--toggle { border-color:#d1d5db; color:#374151; background:#f9fafb; }
.ann-btn--toggle:hover { background:#f1f5f9; }
.ann-btn--del { border-color:#fecaca; color:#dc2626; background:#fff5f5; }
.ann-btn--del:hover { background:#fee2e2; }

.empty-state {
  text-align:center; padding:48px 24px; color:#94a3b8;
}
.empty-state svg { width:48px; height:48px; margin:0 auto 12px; display:block; opacity:.4; }
.empty-state p { font-size:.9rem; font-weight:500; }
</style>
@endpush

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.88rem;color:#166534;font-weight:500;">
  {{ session('success') }}
</div>
@endif

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Announcements</h1>
      <p class="enc-page__subtitle">Post and manage announcements for students, faculty, and registrars.</p>
    </div>
  </div>
</div>

<div class="ann-grid">

  {{-- ── Post Announcement Form ───────────────────── --}}
  <div class="ann-form-card">
    <h3>Post New Announcement</h3>
    <form method="POST" action="{{ route('admin.announcements.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label">Title *</label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Enrollment Reminder" value="{{ old('title') }}" required>
        @error('title')<div style="color:#dc2626;font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Message *</label>
        <textarea name="message" class="form-control" placeholder="Write the announcement details here…" required>{{ old('message') }}</textarea>
        @error('message')<div style="color:#dc2626;font-size:.76rem;margin-top:4px;">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Priority</label>
        <div class="chip-group">
          @foreach(['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $val => $label)
            <input type="radio" name="priority" id="p_{{ $val }}" value="{{ $val }}" class="chip-input"
              {{ old('priority', 'medium') === $val ? 'checked' : '' }}>
            <label for="p_{{ $val }}" class="chip-label">{{ $label }}</label>
          @endforeach
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Send To</label>
        <div class="chip-group">
          @foreach(['all' => 'Everyone', 'student' => 'Students', 'faculty' => 'Faculty', 'registrar' => 'Registrars'] as $val => $label)
            <input type="radio" name="target_audience" id="a_{{ $val }}" value="{{ $val }}" class="chip-input"
              {{ old('target_audience', 'all') === $val ? 'checked' : '' }}>
            <label for="a_{{ $val }}" class="chip-label">{{ $label }}</label>
          @endforeach
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Expires On <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
      </div>

      <button type="submit" class="btn-post">Post Announcement</button>
    </form>
  </div>

  {{-- ── Announcement List ────────────────────────── --}}
  <div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
      <div style="font-size:.9rem;font-weight:700;color:#0f172a;">All Announcements
        <span style="margin-left:6px;font-size:.78rem;font-weight:500;color:#64748b;">({{ $announcements->total() }})</span>
      </div>
    </div>

    @if($announcements->isEmpty())
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        <p>No announcements yet. Post your first one!</p>
      </div>
    @else
      <div class="ann-list">
        @foreach($announcements as $ann)
        <div class="ann-item ann-item--{{ $ann->priority }} {{ !$ann->is_active ? 'ann-item--inactive' : '' }}">
          <div class="ann-body">
            <div class="ann-title">{{ $ann->title }}</div>
            <div class="ann-msg">{{ Str::limit($ann->message, 140) }}</div>
            <div class="ann-meta">
              <span><span class="ann-badge b-{{ $ann->priority }}">{{ $ann->priority_label }}</span></span>
              <span><span class="ann-badge b-{{ $ann->target_audience }}">{{ $ann->audience_label }}</span></span>
              @if(!$ann->is_active)
                <span style="color:#ef4444;font-weight:600;">Deactivated</span>
              @endif
              @if($ann->expires_at)
                <span>Expires {{ $ann->expires_at->format('M d, Y g:i A') }}</span>
              @endif
              <span>By {{ $ann->author->first_name ?? 'Admin' }} · {{ $ann->created_at->diffForHumans() }}</span>
            </div>
          </div>
          <div class="ann-actions">
            <form method="POST" action="{{ route('admin.announcements.toggle', $ann) }}" style="margin:0;">
              @csrf @method('PATCH')
              <button type="submit" class="ann-btn ann-btn--toggle">
                {{ $ann->is_active ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
            <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" style="margin:0;"
                  onsubmit="return confirm('Delete this announcement?')">
              @csrf @method('DELETE')
              <button type="submit" class="ann-btn ann-btn--del">Delete</button>
            </form>
          </div>
        </div>
        @endforeach
      </div>

      <div style="margin-top:20px;">{{ $announcements->links() }}</div>
    @endif
  </div>

</div>
@endsection
