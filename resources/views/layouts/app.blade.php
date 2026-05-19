{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — EncryptEd · Phil. Academy of Sakya</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  {{-- Platform CSS --}}
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">

  @if(auth()->check() && auth()->user()->role_id === '01')
  <style>
  /* ═══════════════════════════════════════════════════════════
     STUDENT SIDEBAR — Navy + yellow academic portal
     Designed for the actual rhythm of a student day: dashboard
     overview at the top, daily learning (schedule, subjects,
     grades) in the middle, paperwork & finance below.
  ═══════════════════════════════════════════════════════════ */

  .enc-sidebar {
    background:
      linear-gradient(180deg, #0a1f44 0%, #07142c 100%);
    border-right: 1px solid rgba(255, 255, 255, .04);
    box-shadow: 4px 0 20px rgba(10, 31, 68, .25);
  }

  .enc-sidebar::before {
    /* Yellow top accent stripe */
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #d97706 0%, #facc15 50%, #fbbf24 100%);
    z-index: 1;
  }

  /* ── Brand ────────────────────────────────────────────── */
  .stu-brand {
    padding: 22px 20px 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, .06);
    position: relative;
  }
  .stu-brand__glow { display: none; }
  .stu-brand__logo {
    width: 160px;
    height: auto;
    display: block;
    mix-blend-mode: lighten;
    filter: brightness(1.12) contrast(1.05);
  }

  /* ── School identity ──────────────────────────────────── */
  .stu-school {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, .06);
    background: rgba(0, 0, 0, .18);
  }
  .stu-school__seal {
    width: 38px; height: 38px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    mix-blend-mode: lighten;
    border: 2px solid #fbbf24;
    background: rgba(0, 0, 0, .3);
    padding: 1px;
    filter: brightness(1.05);
  }
  .stu-school__name {
    font-size: .76rem;
    font-weight: 700;
    color: rgba(255, 255, 255, .92);
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -.005em;
  }
  .stu-school__sub {
    font-size: .6rem;
    color: #fbbf24;
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-top: 2px;
    font-weight: 600;
    font-family: 'IBM Plex Mono', ui-monospace, monospace;
  }

  /* ── Current term badge ──────────────────────────────── */
  .stu-term {
    margin: 14px 14px 6px;
    padding: 12px 14px;
    background: linear-gradient(135deg, rgba(251, 191, 36, .12) 0%, rgba(251, 191, 36, .04) 100%);
    border: 1px solid rgba(251, 191, 36, .22);
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 11px;
    position: relative;
    overflow: hidden;
  }
  .stu-term::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(251, 191, 36, .15), transparent 70%);
    pointer-events: none;
  }
  .stu-term__icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    background: rgba(251, 191, 36, .18);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .stu-term__icon svg { width: 16px; height: 16px; color: #fbbf24; }
  .stu-term__body { flex: 1; min-width: 0; position: relative; }
  .stu-term__label {
    font-size: .58rem;
    font-weight: 700;
    color: rgba(251, 191, 36, .9);
    text-transform: uppercase;
    letter-spacing: .12em;
    font-family: 'IBM Plex Mono', ui-monospace, monospace;
  }
  .stu-term__value {
    font-size: .82rem;
    font-weight: 700;
    color: #fff;
    margin-top: 1px;
    letter-spacing: -.01em;
  }

  /* ── Nav container ────────────────────────────────────── */
  .stu-nav {
    flex: 1;
    padding: 8px 14px 12px;
    overflow-y: auto;
  }
  .stu-nav::-webkit-scrollbar { width: 4px; }
  .stu-nav::-webkit-scrollbar-thumb {
    background: rgba(251, 191, 36, .2);
    border-radius: 99px;
  }
  .stu-nav::-webkit-scrollbar-thumb:hover { background: rgba(251, 191, 36, .4); }

  /* ── Section labels ───────────────────────────────────── */
  .stu-section {
    padding: 18px 6px 8px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .stu-section:first-child { padding-top: 10px; }
  .stu-section__text {
    font-size: .6rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(251, 191, 36, .75);
    white-space: nowrap;
    font-family: 'IBM Plex Mono', ui-monospace, monospace;
  }
  .stu-section__line {
    height: 1px;
    flex: 1;
    background: linear-gradient(90deg, rgba(251, 191, 36, .22), transparent);
  }

  /* ── Nav items ────────────────────────────────────────── */
  .stu-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    margin-bottom: 3px;
    text-decoration: none;
    color: rgba(255, 255, 255, .68);
    font-size: .88rem;
    font-weight: 500;
    transition: background .18s, color .18s, transform .18s;
    position: relative;
    letter-spacing: -.005em;
  }
  .stu-nav-item:hover {
    background: rgba(255, 255, 255, .07);
    color: #fff;
  }
  .stu-nav-item:hover .stu-icon {
    background: rgba(251, 191, 36, .18);
    color: #fbbf24;
  }
  .stu-nav-item.active {
    background: linear-gradient(90deg,
      rgba(251, 191, 36, .2) 0%,
      rgba(251, 191, 36, .04) 100%);
    color: #fbbf24;
    font-weight: 700;
  }
  .stu-nav-item.active::before {
    content: '';
    position: absolute;
    left: -14px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 26px;
    background: #fbbf24;
    border-radius: 0 4px 4px 0;
    box-shadow: 0 0 14px rgba(251, 191, 36, .65);
  }
  .stu-nav-item.active::after { display: none; }
  .stu-nav-item.active .stu-icon {
    background: rgba(251, 191, 36, .22);
    color: #fbbf24;
    box-shadow: 0 0 0 1px rgba(251, 191, 36, .3);
  }

  /* ── Icons (monochrome, theme-aligned) ────────────────── */
  .stu-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    background: rgba(255, 255, 255, .04);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: rgba(255, 255, 255, .65);
    transition: background .18s, color .18s, box-shadow .18s;
  }
  .stu-icon svg { width: 15px; height: 15px; }

  /* ── Badge on nav item (e.g. holds count, balance alert) ── */
  .stu-nav-badge {
    margin-left: auto;
    background: #fbbf24;
    color: #0a1f44;
    font-size: .62rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 99px;
    font-family: 'IBM Plex Mono', ui-monospace, monospace;
    min-width: 22px;
    text-align: center;
    line-height: 1.3;
  }
  .stu-nav-badge--danger { background: #ef4444; color: #fff; }
  .stu-nav-badge--dot {
    background: #ef4444;
    min-width: 8px;
    width: 8px;
    height: 8px;
    padding: 0;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, .25);
  }

  /* ── User footer ──────────────────────────────────────── */
  .stu-footer {
    padding: 12px;
    border-top: 1px solid rgba(255, 255, 255, .06);
    background: rgba(0, 0, 0, .15);
  }
  .stu-user-card {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 12px;
    border-radius: 12px;
    background: rgba(255, 255, 255, .04);
    border: 1px solid rgba(255, 255, 255, .07);
    transition: background .18s, border-color .18s;
    position: relative;
  }
  .stu-user-card::before { display: none; }
  .stu-user-card:hover {
    background: rgba(255, 255, 255, .08);
    border-color: rgba(251, 191, 36, .25);
  }

  .stu-avatar {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: .82rem;
    font-weight: 800;
    color: #0a1f44;
    flex-shrink: 0;
    letter-spacing: -.5px;
    box-shadow: 0 4px 12px rgba(251, 191, 36, .35);
  }
  .stu-user-info { flex: 1; min-width: 0; }
  .stu-user-name {
    font-size: .82rem;
    font-weight: 700;
    color: rgba(255, 255, 255, .92);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -.005em;
  }
  .stu-user-role {
    font-size: .62rem;
    color: #fbbf24;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-top: 1px;
    font-weight: 600;
    font-family: 'IBM Plex Mono', ui-monospace, monospace;
  }
  .stu-logout-btn {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, .1);
    background: transparent;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .18s, border-color .18s;
  }
  .stu-logout-btn:hover {
    background: rgba(239, 68, 68, .15);
    border-color: rgba(239, 68, 68, .35);
  }
  .stu-logout-btn svg {
    width: 14px; height: 14px;
    color: rgba(255, 255, 255, .55);
    transition: color .18s;
  }
  .stu-logout-btn:hover svg { color: #fca5a5; }

  /* ── Glassmorphic top header for students ─────────────── */
  .enc-header {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, .94) !important;
  }
  </style>
  @endif

  {{-- ── Shared Dashboard Design System (sd-*) ─────────────── --}}
  @if(auth()->check())
  <style>
  :root {
    --sd-bg:      #f0f4f8;
    --sd-card:    #ffffff;
    --sd-border:  rgba(15,23,42,.07);
    --sd-navy:    #0f1e3c;
    --sd-primary: #4f46e5;
    --sd-success: #10b981;
    --sd-warning: #f59e0b;
    --sd-danger:  #ef4444;
    --sd-muted:   #64748b;
    --sd-radius:  16px;
    --sd-shadow:  0 4px 24px rgba(15,23,42,.07);
  }

  /* ── Announcements ─────────────────────────────────────────── */
  .sd-announce-wrap { margin-bottom: 24px; }
  .sd-announce-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
  .sd-announce-header h2 { font-size: 1rem; font-weight: 700; color: var(--sd-navy); margin: 0; }
  .sd-announce-count { background: var(--sd-primary); color: #fff; font-size: .72rem; font-weight: 700; padding: .15rem .5rem; border-radius: 999px; }
  .sd-announce-list { display: flex; flex-direction: column; gap: 10px; }
  .sd-announce-item {
    display: flex; gap: 14px; align-items: flex-start; position: relative;
    background: var(--sd-card); border: 1px solid var(--sd-border); border-left: 4px solid transparent;
    border-radius: var(--sd-radius); padding: 10px 40px 10px 14px; box-shadow: var(--sd-shadow);
    transition: transform .18s ease, box-shadow .18s ease; animation: slideDown .35s ease both;
  }
  .sd-announce-item:hover { transform: translateY(-1px); box-shadow: 0 8px 32px rgba(15,23,42,.1); }
  .sd-announce-item--high   { border-left-color: var(--sd-danger);  background: linear-gradient(135deg,#fff5f5,#ffffff); }
  .sd-announce-item--medium { border-left-color: var(--sd-warning); background: linear-gradient(135deg,#fffbeb,#ffffff); }
  .sd-announce-item--low    { border-left-color: var(--sd-primary); background: linear-gradient(135deg,#eef2ff,#ffffff); }
  .sd-announce-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .sd-announce-item--high   .sd-announce-icon { background: rgba(239,68,68,.12);  color: var(--sd-danger); }
  .sd-announce-item--medium .sd-announce-icon { background: rgba(245,158,11,.12); color: var(--sd-warning); }
  .sd-announce-item--low    .sd-announce-icon { background: rgba(79,70,229,.12);  color: var(--sd-primary); }
  .sd-announce-body { flex: 1; min-width: 0; }
  .sd-announce-title { font-weight: 700; color: var(--sd-navy); font-size: .92rem; }
  .sd-announce-msg   { font-size: .83rem; color: var(--sd-muted); margin-top: 3px; line-height: 1.45; }
  .sd-announce-date  { font-size: .75rem; color: #94a3b8; margin-top: 6px; }
  .sd-priority-badge { flex-shrink: 0; font-size: .7rem; font-weight: 700; padding: .25rem .65rem; border-radius: 999px; align-self: flex-start; white-space: nowrap; }
  .badge--high   { background: rgba(239,68,68,.1);  color: #dc2626; }
  .badge--medium { background: rgba(245,158,11,.1); color: #d97706; }
  .badge--low    { background: rgba(79,70,229,.1);  color: #4338ca; }
  .sd-dismiss-btn { position: absolute; top: 10px; right: 12px; width: 22px; height: 22px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #94a3b8; transition: background .15s, color .15s; padding: 0; }
  .sd-dismiss-btn:hover { background: #f1f5f9; color: #374151; }

  /* ── Hero ──────────────────────────────────────────────────── */
  .sd-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #312e81 50%, #1e1b4b 100%);
    border-radius: 20px; padding: 28px 32px;
    display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;
    margin-bottom: 20px; position: relative; overflow: hidden;
    box-shadow: 0 8px 40px rgba(30,58,138,.25);
  }
  .sd-hero::before { content: ''; position: absolute; top: -60px; right: -60px; width: 220px; height: 220px; border-radius: 50%; background: rgba(255,255,255,.05); }
  .sd-hero::after  { content: ''; position: absolute; bottom: -80px; right: 120px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,.04); }
  .sd-hero__left h1 { font-size: 1.55rem; font-weight: 800; color: #fff; margin: 0 0 4px; }
  .sd-hero__left p  { font-size: .88rem; color: rgba(255,255,255,.6); margin: 0; }
  .sd-hero__pills   { display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1; }
  .sd-hero__pill {
    background: rgba(255,255,255,.22); border: 1px solid rgba(255,255,255,.30); border-radius: 999px;
    padding: .4rem 1rem; font-size: .8rem; font-weight: 700; color: #fff;
    display: flex; align-items: center; gap: 6px; backdrop-filter: blur(8px);
  }
  .sd-hero__pill svg { width: 14px; height: 14px; opacity: .85; }

  /* ── Stat Strip ─────────────────────────────────────────────── */
  .sd-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 20px; }
  .sd-stat { background: var(--sd-card); border: 1px solid var(--sd-border); border-radius: var(--sd-radius); padding: 18px 16px; box-shadow: var(--sd-shadow); display: flex; align-items: center; gap: 12px; transition: transform .18s ease, box-shadow .18s ease; }
  .sd-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(15,23,42,.1); }
  .sd-stat__icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .sd-stat__icon svg { width: 20px; height: 20px; }
  .si--blue   { background: rgba(79,70,229,.1);  color: var(--sd-primary); }
  .si--green  { background: rgba(16,185,129,.1); color: var(--sd-success); }
  .si--orange { background: rgba(245,158,11,.1); color: var(--sd-warning); }
  .si--red    { background: rgba(239,68,68,.1);  color: var(--sd-danger);  }
  .sd-stat__val   { font-size: 1.3rem; font-weight: 800; color: var(--sd-navy); line-height: 1.1; }
  .sd-stat__label { font-size: .74rem; color: var(--sd-muted); margin-top: 2px; font-weight: 500; }

  /* ── Card ───────────────────────────────────────────────────── */
  .sd-card { background: var(--sd-card); border: 1px solid var(--sd-border); border-radius: var(--sd-radius); box-shadow: var(--sd-shadow); overflow: hidden; }
  .sd-card__head { padding: 16px 20px 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .sd-card__title { font-size: .92rem; font-weight: 700; color: var(--sd-navy); }
  .sd-card__meta  { font-size: .75rem; color: #94a3b8; }
  .sd-card__body  { padding: 16px 20px 20px; }

  /* ── Layout Grid ────────────────────────────────────────────── */
  .sd-main-grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }
  @media(max-width:900px){ .sd-main-grid { grid-template-columns: 1fr; } }

  /* ── Schedule Timeline ──────────────────────────────────────── */
  .sd-schedule-item { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
  .sd-schedule-item:last-child { border-bottom: none; padding-bottom: 0; }
  .sd-schedule-time { min-width: 88px; font-size: .75rem; font-weight: 600; color: var(--sd-muted); padding-top: 2px; font-family: monospace; }
  .sd-schedule-dot-col { display: flex; flex-direction: column; align-items: center; padding-top: 5px; }
  .sd-schedule-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--sd-primary); flex-shrink: 0; }
  .sd-schedule-line { width: 1px; flex: 1; background: #e2e8f0; margin-top: 4px; min-height: 24px; }
  .sd-schedule-item:last-child .sd-schedule-line { display: none; }
  .sd-schedule-info   { flex: 1; }
  .sd-schedule-subj   { font-size: .88rem; font-weight: 700; color: var(--sd-navy); }
  .sd-schedule-detail { font-size: .76rem; color: var(--sd-muted); margin-top: 2px; }
  .sd-schedule-room   { font-size: .72rem; background: #f1f5f9; color: var(--sd-muted); padding: .15rem .5rem; border-radius: 6px; font-weight: 600; align-self: flex-start; flex-shrink: 0; }

  /* ── Row / Badge ────────────────────────────────────────────── */
  .sd-login-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 8px; }
  .sd-login-row:last-child { margin-bottom: 0; }
  .sd-login-type { font-size: .84rem; font-weight: 600; color: var(--sd-navy); }
  .sd-login-time { font-size: .75rem; color: var(--sd-muted); margin-top: 2px; }
  .sd-badge-pill { font-size: .72rem; font-weight: 700; padding: .2rem .65rem; border-radius: 999px; white-space: nowrap; }
  .pill--success { background: rgba(16,185,129,.1); color: #059669; }
  .pill--danger  { background: rgba(239,68,68,.1);  color: #dc2626; }
  .pill--neutral { background: #f1f5f9; color: #475569; }
  .pill--warning { background: rgba(245,158,11,.1); color: #d97706; }

  /* ── Quick Action Buttons ───────────────────────────────────── */
  .sd-quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .sd-quick-btn { display: flex; flex-direction: column; align-items: flex-start; gap: 6px; padding: 14px; border-radius: 14px; border: 1px solid var(--sd-border); text-decoration: none; transition: background .15s, border-color .15s, transform .15s; background: var(--sd-card); }
  .sd-quick-btn:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
  .sd-quick-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
  .sd-quick-icon svg { width: 16px; height: 16px; }
  .sd-quick-label { font-size: .82rem; font-weight: 700; color: var(--sd-navy); }
  .sd-quick-desc  { font-size: .72rem; color: var(--sd-muted); }

  /* ── Enc-buttons ────────────────────────────────────────────── */
  .enc-button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: .5rem 1rem; border-radius: 999px; font-size: .8rem; font-weight: 700; text-decoration: none; border: none; cursor: pointer; transition: transform .15s, box-shadow .15s; }
  .enc-button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
  .enc-button--primary   { background: var(--sd-primary); color: #fff; }
  .enc-button--secondary { background: #f1f5f9; color: var(--sd-navy); }
  .enc-button--sm { padding: .4rem .85rem; font-size: .76rem; }

  @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
  </style>
  @endif

  @stack('head')
</head>
<body>

<div class="enc-shell">

  {{-- ════════════════════════════════
       SIDEBAR
  ═══════════════════════════════════ --}}
  <aside class="enc-sidebar" id="enc-sidebar">

  @if(auth()->user()->role_id === '01')
  {{-- ╔══════════════════════════════════════╗
       ║   STUDENT SIDEBAR                    ║
       ║   Organized by the rhythm of a       ║
       ║   student's day: Overview → Learning ║
       ║   → Records → Finance → Account      ║
       ╚══════════════════════════════════════╝ --}}
  @php
    // School year (June-onwards = current year → next year; else previous → current)
    $sy_start = (int) date('n') >= 6 ? (int) date('Y') : (int) date('Y') - 1;
    $sy_label = $sy_start . '–' . ($sy_start + 1);
  @endphp

    {{-- Brand --}}
    <a href="{{ route('student.dashboard') }}" class="stu-brand" style="text-decoration:none;">
      <div class="stu-brand__glow"></div>
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="stu-brand__logo">
    </a>

    {{-- School identity --}}
    <div class="stu-school">
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="stu-school__seal">
      <div style="min-width:0;flex:1;">
        <div class="stu-school__name">Phil. Academy of Sakya</div>
        <div class="stu-school__sub">Student Portal</div>
      </div>
    </div>

    {{-- Current term indicator --}}
    <div class="stu-term">
      <div class="stu-term__icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
      </div>
      <div class="stu-term__body">
        <div class="stu-term__label">School Year</div>
        <div class="stu-term__value">{{ $sy_label }}</div>
      </div>
    </div>

    {{-- Navigation --}}
    <nav class="stu-nav">

      {{-- ───── OVERVIEW ───── --}}
      <div class="stu-section">
        <span class="stu-section__text">Overview</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.dashboard') }}"
         class="stu-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Home --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
          </svg>
        </span>
        Dashboard
      </a>

      {{-- ───── MY LEARNING ───── --}}
      <div class="stu-section">
        <span class="stu-section__text">My Learning</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.schedule') }}"
         class="stu-nav-item {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Calendar / Schedule --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
          </svg>
        </span>
        Class Schedule
      </a>

      <a href="{{ route('student.course-offerings') }}"
         class="stu-nav-item {{ request()->routeIs('student.course-offerings') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Book / Subjects --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
          </svg>
        </span>
        My Subjects
      </a>

      <a href="{{ route('student.report-card') }}"
         class="stu-nav-item {{ request()->routeIs('student.report-card') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Chart bars / Grades --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
          </svg>
        </span>
        Grade Report
      </a>

      <a href="{{ route('student.program-curriculum') }}"
         class="stu-nav-item {{ request()->routeIs('student.program-curriculum') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Academic cap / Curriculum --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
          </svg>
        </span>
        Curriculum
      </a>

      {{-- ───── RECORDS ───── --}}
      <div class="stu-section">
        <span class="stu-section__text">Records</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.admission-documents') }}"
         class="stu-nav-item {{ request()->routeIs('student.admission-documents') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Document --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </span>
        Documents
      </a>

      <a href="{{ route('student.academic-holds') }}"
         class="stu-nav-item {{ request()->routeIs('student.academic-holds') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Shield / Hold --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </span>
        Academic Holds
      </a>

      {{-- ───── FINANCE ───── --}}
      <div class="stu-section">
        <span class="stu-section__text">Finance</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.account-balance') }}"
         class="stu-nav-item {{ request()->routeIs('student.account-balance') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Wallet / Balance --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
          </svg>
        </span>
        Account Balance
      </a>

      {{-- ───── ACCOUNT ───── --}}
      <div class="stu-section">
        <span class="stu-section__text">Account</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.settings.index') }}"
         class="stu-nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
        <span class="stu-icon">
          {{-- Cog / Settings --}}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </span>
        Settings
      </a>

    </nav>

    {{-- User footer --}}
    <div class="stu-footer">
      <div class="stu-user-card">
        <div class="stu-avatar">
          {{ strtoupper(substr(auth()->user()->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
        </div>
        <div class="stu-user-info">
          <div class="stu-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
          <div class="stu-user-role">Student</div>
        </div>
        <button type="button" class="stu-logout-btn" title="Sign out" onclick="openLogoutModal()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
        </button>
      </div>
    </div>

  @elseif(auth()->user()->role_id === '02')
  {{-- ╔══════════════════════════════════════╗
       ║   FACULTY SIDEBAR                    ║
       ╚══════════════════════════════════════╝ --}}

    {{-- Brand --}}
    <a href="{{ route('faculty.dashboard') }}" class="enc-sidebar__brand" style="text-decoration:none;display:block;">
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="enc-sidebar__logo-img">
    </a>

    {{-- School identity --}}
    <div class="enc-sidebar__school">
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="enc-sidebar__school-seal">
      <div class="enc-sidebar__school-text">
        <span class="enc-sidebar__school-name">Phil. Academy of Sakya</span>
        <span class="enc-sidebar__school-sub">Faculty Portal · {{ date('Y') }}</span>
      </div>
    </div>

    {{-- Navigation --}}
    <nav class="enc-sidebar__nav">
      <div class="enc-sidebar__section-label">Overview</div>

      <a href="{{ route('faculty.dashboard') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
        Dashboard
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">My Classes</div>

      <a href="{{ route('faculty.classes') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.classes') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
        Teaching Load
      </a>

      <a href="{{ route('faculty.gradebook') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.gradebook') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
        </svg>
        Gradebook
      </a>

      <a href="{{ route('faculty.attendance') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.attendance') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
        Attendance
      </a>

      <a href="{{ route('complaints.manage') }}"
         class="enc-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
        </svg>
        Grade Complaints
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Resources</div>

      <a href="{{ route('faculty.my-schedule') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.my-schedule') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
        </svg>
        My Schedule
      </a>

      <a href="{{ route('faculty.announcements') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.announcements') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Announcements
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Account</div>

      <a href="{{ route('faculty.settings.index') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.settings.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Settings
      </a>
    </nav>

    {{-- User Footer --}}
    <div class="enc-sidebar__footer">
      <div class="enc-sidebar__user" style="cursor:default;">
        <div class="enc-sidebar__avatar">
          {{ strtoupper(substr(auth()->user()->first_name ?? 'F', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
        </div>
        <div class="enc-sidebar__user-info">
          <div class="enc-sidebar__user-name">{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</div>
          <div class="enc-sidebar__user-role">Faculty</div>
        </div>
        <button type="button" onclick="openLogoutModal()" title="Sign out"
                style="width:26px;height:26px;border-radius:7px;border:1px solid rgba(255,255,255,.12);background:transparent;display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;color:rgba(255,255,255,.4);">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
        </button>
      </div>
    </div>

  @elseif(auth()->user()->role_id === '03')
  {{-- ╔══════════════════════════════════════╗
       ║   REGISTRAR SIDEBAR                  ║
       ╚══════════════════════════════════════╝ --}}

    <a href="{{ route('registrar.dashboard') }}" class="enc-sidebar__brand" style="text-decoration:none;display:block;">
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="enc-sidebar__logo-img">
    </a>

    <div class="enc-sidebar__school">
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="enc-sidebar__school-seal">
      <div class="enc-sidebar__school-text">
        <span class="enc-sidebar__school-name">Phil. Academy of Sakya</span>
        <span class="enc-sidebar__school-sub">Registrar Portal · {{ date('Y') }}</span>
      </div>
    </div>

    <nav class="enc-sidebar__nav">

      <div class="enc-sidebar__section-label">Overview</div>

      <a href="{{ route('registrar.dashboard') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
        Dashboard
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Students</div>

      <a href="{{ route('registrar.students') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.students') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        Student Records
      </a>

      <a href="{{ route('registrar.enrollment') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.enrollment') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/>
        </svg>
        Enrollment
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Documents</div>

      <a href="{{ route('registrar.requests') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.requests') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        Document Requests
      </a>

      <a href="{{ route('registrar.report-cards') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.report-cards') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
        </svg>
        Report Cards
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Academics</div>

      <a href="{{ route('registrar.grades') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.grades') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
        Grades & Records
      </a>

      <a href="{{ route('registrar.calendar') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.calendar') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        Academic Calendar
      </a>

      <a href="{{ route('complaints.manage') }}"
         class="enc-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
        </svg>
        Grade Complaints
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Resources</div>

      <a href="{{ route('registrar.announcements') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.announcements') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Announcements
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Account</div>

      <a href="{{ route('registrar.settings.index') }}"
         class="enc-nav-item {{ request()->routeIs('registrar.settings.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Settings
      </a>

    </nav>

    <div class="enc-sidebar__footer">
      <div class="enc-sidebar__user" style="cursor:default;">
        <div class="enc-sidebar__avatar">
          {{ strtoupper(substr(auth()->user()->first_name ?? 'R', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
        </div>
        <div class="enc-sidebar__user-info">
          <div class="enc-sidebar__user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
          <div class="enc-sidebar__user-role">Registrar</div>
        </div>
        <button type="button" onclick="openLogoutModal()" title="Sign out" style="background:transparent;border:none;cursor:pointer;padding:4px;border-radius:6px;display:flex;align-items:center;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:rgba(255,255,255,.35);">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
        </button>
      </div>
    </div>

  @else
  {{-- ╔══════════════════════════════════════╗
       ║   ADMIN SIDEBAR                      ║
       ╚══════════════════════════════════════╝ --}}

    <a href="{{ route('admin.dashboard') }}" class="enc-sidebar__brand" style="text-decoration:none;display:block;">
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="enc-sidebar__logo-img">
    </a>

    <div class="enc-sidebar__school">
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="enc-sidebar__school-seal">
      <div class="enc-sidebar__school-text">
        <span class="enc-sidebar__school-name">Phil. Academy of Sakya</span>
        <span class="enc-sidebar__school-sub">Admin Portal · {{ date('Y') }}</span>
      </div>
    </div>

    <nav class="enc-sidebar__nav">
      <div class="enc-sidebar__section-label">Overview</div>

      <a href="{{ route('admin.dashboard') }}"
         class="enc-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
        Dashboard
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Academics</div>

      <a href="{{ route('admin.users.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        User Management
      </a>

      <a href="{{ route('admin.grades.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.grades.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
        Grades & Records
      </a>

      <a href="{{ route('admin.announcements.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Announcements
      </a>

      <a href="{{ route('admin.schedules.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        Faculty Schedules
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Security</div>

      <a href="{{ route('admin.threat.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.threat.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        Threat Events
        @if(isset($activeThreats) && $activeThreats > 0)
          <span class="enc-nav-badge">{{ $activeThreats }}</span>
        @endif
      </a>

      <a href="{{ route('admin.audit.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        Audit Log
      </a>

      <a href="{{ route('admin.compliance.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.compliance.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
        Compliance & Reports
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Account</div>
      <a href="{{ route('admin.settings.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Settings
      </a>
    </nav>

    <div class="enc-sidebar__footer">
      <a href="{{ route('admin.security-settings') }}" class="enc-sidebar__user">
        <div class="enc-sidebar__avatar">
          {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'D', 0, 1)) }}
        </div>
        <div class="enc-sidebar__user-info">
          <div class="enc-sidebar__user-name">{{ auth()->user()->first_name ?? 'Administrator' }} {{ auth()->user()->last_name ?? '' }}</div>
          <div class="enc-sidebar__user-role">{{ auth()->user()->role_label ?? 'Admin' }}</div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:rgba(255,255,255,.3)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
      </a>
    </div>

  @endif

  </aside>

  {{-- ════════════════════════════════
       MAIN CONTENT
  ═══════════════════════════════════ --}}
  <div class="enc-main">

    {{-- Top Header --}}
    <header class="enc-header">
      <div class="enc-header__left">
        <div class="enc-header__breadcrumb">
          <span>{{ auth()->user()->role_label ?? 'Portal' }}</span>
          <span class="enc-header__breadcrumb-sep">›</span>
          <span>@yield('breadcrumb', 'Dashboard')</span>
        </div>
      </div>

      <div class="enc-header__right">
        <div class="enc-header__time" id="enc-clock">--:-- --</div>

        {{-- Notifications --}}
        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
        <a href="{{ route('notifications.index') }}" class="enc-icon-btn" title="Notifications">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2" style="pointer-events:none;">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
          </svg>
          @if($unreadCount > 0)
            <span style="position:absolute;top:-4px;right:-4px;background:#e11d48;color:#fff;
              border-radius:999px;font-size:.6rem;font-weight:800;min-width:16px;height:16px;
              display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;
              pointer-events:none;">
              {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
          @endif
        </a>

        {{-- Logout --}}
        <button type="button" class="enc-icon-btn" title="Sign out" onclick="openLogoutModal()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
        </button>
      </div>
    </header>

    {{-- Page Body --}}
    <main class="enc-page">
      @yield('content')
    </main>

  </div><!-- /.enc-main -->
</div><!-- /.enc-shell -->

{{-- ══════════════════════════════════════════════════════
     LOGOUT CONFIRMATION MODAL — shared across all roles
═══════════════════════════════════════════════════════ --}}
<div id="logout-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">

  {{-- Backdrop --}}
  <div id="logout-backdrop"
       onclick="closeLogoutModal()"
       style="position:absolute;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);"></div>

  {{-- Dialog --}}
  <div id="logout-dialog" style="
      position:relative;z-index:1;
      background:#fff;border-radius:20px;
      width:100%;max-width:400px;margin:0 16px;
      box-shadow:0 24px 64px rgba(15,23,42,.18);
      overflow:hidden;
      transform:scale(.95) translateY(10px);
      opacity:0;
      transition:transform .22s cubic-bezier(.34,1.56,.64,1), opacity .18s ease;">

    {{-- Top accent bar --}}
    <div style="height:4px;background:linear-gradient(90deg,#ef4444,#f87171);"></div>

    {{-- Body --}}
    <div style="padding:32px 28px 24px;">

      {{-- Icon --}}
      <div style="width:52px;height:52px;border-radius:14px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:#ef4444;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
        </svg>
      </div>

      {{-- Heading --}}
      <h2 style="font-size:1.15rem;font-weight:700;color:#0f172a;margin:0 0 6px;">Sign out of EncryptEd?</h2>
      <p style="font-size:.875rem;color:#64748b;margin:0 0 20px;line-height:1.55;">
        You'll be returned to the login page. Any unsaved work may be lost.
      </p>

      {{-- User info strip --}}
      <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;margin-bottom:24px;">
        <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#1e3a5f,#2d5fa8);display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;color:#fff;flex-shrink:0;letter-spacing:-.5px;">
          {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
        </div>
        <div style="min-width:0;">
          <div style="font-size:.88rem;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            {{ auth()->user()->full_name ?? 'User' }}
          </div>
          <div style="font-size:.74rem;color:#94a3b8;margin-top:1px;">
            {{ auth()->user()->email ?? '' }} &nbsp;·&nbsp; {{ auth()->user()->role_label ?? 'User' }}
          </div>
        </div>
      </div>

      {{-- Actions --}}
      <div style="display:flex;gap:10px;">
        <button onclick="closeLogoutModal()" type="button"
                style="flex:1;padding:.65rem 1rem;border:1px solid #e2e8f0;border-radius:10px;background:#fff;color:#374151;font-size:.875rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s;"
                onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
                onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
          Stay Signed In
        </button>
        <button onclick="document.getElementById('logout-form').submit()" type="button"
                style="flex:1;padding:.65rem 1rem;border:none;border-radius:10px;background:#ef4444;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:background .15s;"
                onmouseover="this.style.background='#dc2626'"
                onmouseout="this.style.background='#ef4444'">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
          Sign Out
        </button>
      </div>

    </div>
  </div>
</div>

{{-- Single logout form used by all triggers --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
  @csrf
</form>

<script>
  // ── Live clock ────────────────────────────────────────────────────────
  (function () {
    const el = document.getElementById('enc-clock');
    if (!el) return;
    function tick() {
      const now = new Date();
      el.textContent = now.toLocaleTimeString('en-PH', {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      });
    }
    tick();
    setInterval(tick, 1000);
  })();

  // ── Logout modal ──────────────────────────────────────────────────────
  function openLogoutModal() {
    const modal  = document.getElementById('logout-modal');
    const dialog = document.getElementById('logout-dialog');
    modal.style.display = 'flex';
    // Trigger animation on next frame
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        dialog.style.transform = 'scale(1) translateY(0)';
        dialog.style.opacity   = '1';
      });
    });
    document.addEventListener('keydown', _escListener);
  }

  function closeLogoutModal() {
    const modal  = document.getElementById('logout-modal');
    const dialog = document.getElementById('logout-dialog');
    dialog.style.transform = 'scale(.95) translateY(10px)';
    dialog.style.opacity   = '0';
    setTimeout(function () { modal.style.display = 'none'; }, 200);
    document.removeEventListener('keydown', _escListener);
  }

  function _escListener(e) {
    if (e.key === 'Escape') closeLogoutModal();
  }
</script>

@stack('scripts')

</body>
</html>
