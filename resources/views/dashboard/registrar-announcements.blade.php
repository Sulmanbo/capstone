@extends('layouts.app')
@section('title', 'Announcements')
@section('breadcrumb', 'Announcements')

@section('content')
<div style="max-width:760px;">

  <div style="margin-bottom:24px;">
    <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Announcements</h1>
    <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">School-wide notices and registrar advisories.</p>
  </div>

  @if($announcements->isEmpty())
  <div class="sd-card">
    <div class="sd-card__body" style="text-align:center;padding:56px 24px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
      <div style="font-weight:600;font-size:.9rem;color:var(--sd-navy);">No announcements</div>
      <div style="font-size:.8rem;color:var(--sd-muted);margin-top:4px;">Check back later for school notices.</div>
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
        <div class="sd-announce-date">Posted {{ $ann->created_at->diffForHumans() }}</div>
      </div>
      <span class="sd-priority-badge badge--{{ $pClass }}">{{ $pLabel }}</span>
    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection
