@extends('layouts.app')
@section('title', 'Announcements')
@section('breadcrumb', 'Announcements')

@section('content')
<div style="max-width:760px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.35rem;font-weight:800;color:var(--navy);margin:0 0 4px;">Announcements</h1>
      <p style="font-size:.875rem;color:var(--gray-400);margin:0;">Faculty announcements and notices from the administration.</p>
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

        @if($errors->any())
        <div style="padding:10px 14px;background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:8px;color:#991b1b;font-size:.85rem;margin-bottom:16px;">
          @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('faculty.announcements.store') }}">
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

  @if(session('success'))
  <div style="padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-left:4px solid #22c55e;border-radius:8px;color:#166534;font-size:.85rem;margin-bottom:20px;">
    {{ session('success') }}
  </div>
  @endif

  @if($announcements->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:60px 24px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:justify-content:center;margin:0 auto 16px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:#94a3b8;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
    </div>
    <div style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:6px;">No announcements yet</div>
    <div style="font-size:.82rem;color:#94a3b8;">Click "Post Announcement" to create one.</div>
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($announcements as $ann)
    @php
      $priorityConfig = match($ann->priority ?? 'normal') {
        'urgent'  => ['bg'=>'#fef2f2','border'=>'#fecaca','dot'=>'#ef4444','tag'=>'#fef2f2','tagText'=>'#991b1b','label'=>'Urgent'],
        'high'    => ['bg'=>'#fff7ed','border'=>'#fed7aa','dot'=>'#f97316','tag'=>'#fff7ed','tagText'=>'#9a3412','label'=>'High Priority'],
        default   => ['bg'=>'#fff',   'border'=>'#e5e7eb','dot'=>'#6366f1','tag'=>'#f8fafc', 'tagText'=>'#475569','label'=>'Notice'],
      };
    @endphp
    <div style="background:{{ $priorityConfig['bg'] }};border:1px solid {{ $priorityConfig['border'] }};border-radius:14px;padding:20px 24px;">
      <div style="display:flex;align-items:flex-start;gap:14px;">
        <div style="width:8px;height:8px;border-radius:50%;background:{{ $priorityConfig['dot'] }};flex-shrink:0;margin-top:6px;"></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
            <div style="font-size:.9rem;font-weight:700;color:#0f172a;">{{ $ann->title }}</div>
            <span style="font-size:.65rem;font-weight:700;padding:.15rem .55rem;border-radius:20px;background:{{ $priorityConfig['tag'] }};color:{{ $priorityConfig['tagText'] }};border:1px solid {{ $priorityConfig['border'] }};">
              {{ $priorityConfig['label'] }}
            </span>
          </div>
          <div style="font-size:.875rem;color:#374151;line-height:1.65;margin-bottom:10px;">{{ $ann->message }}</div>
          <div style="font-size:.75rem;color:#94a3b8;">
            Posted {{ $ann->created_at->diffForHumans() }}
            @if($ann->author)· by {{ $ann->author->first_name }} {{ $ann->author->last_name }}@endif
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection
