@extends('layouts.app')
@section('title', 'Announcements')
@section('breadcrumb', 'Announcements')

@section('content')
<div style="max-width:760px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:var(--navy);margin:0 0 4px;">Announcements</h1>
      <p style="font-size:.82rem;color:var(--gray-400);margin:0;">School-wide notices and registrar advisories.</p>
    </div>
    <button onclick="document.getElementById('compose-form').classList.toggle('hidden')"
            class="enc-btn enc-btn--primary enc-btn--sm">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      Post Announcement
    </button>
  </div>

  {{-- Compose Form --}}
  <div id="compose-form" class="{{ $errors->any() ? '' : 'hidden' }}" style="margin-bottom:24px;">
    <div class="enc-card">
      <div class="enc-card__body" style="padding:24px;">
        <div style="font-size:.92rem;font-weight:700;color:var(--navy);margin-bottom:16px;">New Announcement</div>

        @if(session('success'))
        <div style="padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-left:4px solid #22c55e;border-radius:8px;color:#166534;font-size:.85rem;margin-bottom:16px;">
          {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="padding:10px 14px;background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:8px;color:#991b1b;font-size:.85rem;margin-bottom:16px;">
          @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('registrar.announcements.store') }}">
          @csrf
          <div style="margin-bottom:14px;">
            <label class="enc-label" style="display:block;margin-bottom:4px;">Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                   placeholder="Announcement title"
                   class="enc-input" style="width:100%;">
          </div>
          <div style="margin-bottom:14px;">
            <label class="enc-label" style="display:block;margin-bottom:4px;">Message *</label>
            <textarea name="message" required maxlength="2000" rows="4"
                      placeholder="Write your announcement here..."
                      class="enc-input" style="width:100%;resize:vertical;">{{ old('message') }}</textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
            <div>
              <label class="enc-label" style="display:block;margin-bottom:4px;">Priority *</label>
              <select name="priority" required class="enc-input" style="width:100%;">
                <option value="low"    {{ old('priority','low') === 'low'    ? 'selected' : '' }}>Notice</option>
                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
              </select>
            </div>
            <div>
              <label class="enc-label" style="display:block;margin-bottom:4px;">Audience *</label>
              <select name="target_audience" required class="enc-input" style="width:100%;">
                <option value="all"       {{ old('target_audience','all') === 'all'       ? 'selected' : '' }}>Everyone</option>
                <option value="student"   {{ old('target_audience') === 'student'   ? 'selected' : '' }}>Students Only</option>
                <option value="faculty"   {{ old('target_audience') === 'faculty'   ? 'selected' : '' }}>Faculty Only</option>
                <option value="registrar" {{ old('target_audience') === 'registrar' ? 'selected' : '' }}>Registrar Only</option>
              </select>
            </div>
          </div>
          <div style="display:flex;gap:10px;">
            <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Post</button>
            <button type="button" onclick="document.getElementById('compose-form').classList.add('hidden')"
                    class="enc-btn enc-btn--secondary enc-btn--sm">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @if(session('success') && !$errors->any())
  <div style="padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-left:4px solid #22c55e;border-radius:8px;color:#166534;font-size:.85rem;margin-bottom:20px;">
    {{ session('success') }}
  </div>
  @endif

  @if($announcements->isEmpty())
  <div class="enc-card">
    <div class="enc-card__body" style="text-align:center;padding:56px 24px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
      <div style="font-weight:600;font-size:.9rem;color:var(--navy);">No announcements yet</div>
      <div style="font-size:.8rem;color:var(--gray-400);margin-top:4px;">Click "Post Announcement" to create one.</div>
    </div>
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($announcements as $ann)
    @php
      $p      = $ann->priority ?? 'normal';
      $pClass = match($p) { 'urgent','high' => 'high', 'medium' => 'medium', default => 'low' };
      $pLabel = match($p) { 'urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', default => 'Notice' };
    @endphp
    <div class="sd-announce-item sd-announce-item--{{ $pClass }}" style="border-radius:14px;padding:18px 22px;">
      <div class="sd-announce-icon">
        @if($pClass === 'high')
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        @elseif($pClass === 'medium')
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        @else
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
        @endif
      </div>
      <div class="sd-announce-body">
        <div class="sd-announce-title">{{ $ann->title }}</div>
        <div class="sd-announce-msg" style="margin-top:6px;line-height:1.65;">{{ $ann->message }}</div>
        <div class="sd-announce-date">
          Posted {{ $ann->created_at->diffForHumans() }}
          @if($ann->author)· by {{ $ann->author->first_name }} {{ $ann->author->last_name }}@endif
        </div>
      </div>
      <span class="sd-priority-badge badge--{{ $pClass }}">{{ $pLabel }}</span>
    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection
