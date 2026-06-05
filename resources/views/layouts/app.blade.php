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
     STUDENT SIDEBAR — Clean professional student portal style
  ═══════════════════════════════════════════════════════════ */

  .enc-sidebar {
    background: #1b2b3e;
    border-right: 1px solid rgba(255,255,255,.06);
  }

  /* ── Brand ────────────────────────────────────────────── */
  .stu-brand {
    padding: 20px 18px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,.07);
  }
  .stu-brand__glow { display: none; }
  .stu-brand__logo {
    width: 145px;
    height: auto;
    display: block;
    mix-blend-mode: lighten;
    filter: brightness(1.1);
  }

  /* ── School identity ──────────────────────────────────── */
  .stu-school {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px 13px;
    border-bottom: 1px solid rgba(255,255,255,.07);
  }
  .stu-school__seal {
    width: 34px; height: 34px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    mix-blend-mode: lighten;
    border: 1.5px solid rgba(255,255,255,.15);
  }
  .stu-school__name {
    font-size: .73rem;
    font-weight: 600;
    color: rgba(255,255,255,.8);
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .stu-school__sub {
    font-size: .62rem;
    color: rgba(255,255,255,.35);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-top: 2px;
  }

  /* ── Nav container ────────────────────────────────────── */
  .stu-nav {
    flex: 1;
    padding: 8px 10px;
    overflow-y: auto;
  }
  .stu-nav::-webkit-scrollbar { width: 3px; }
  .stu-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

  /* ── Section labels ───────────────────────────────────── */
  .stu-section {
    padding: 16px 8px 4px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .stu-section__text {
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: rgba(255,255,255,.3);
    white-space: nowrap;
  }
  .stu-section__line {
    height: 1px;
    flex: 1;
    background: rgba(255,255,255,.07);
  }

  /* ── Nav items ────────────────────────────────────────── */
  .stu-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 10px;
    margin-bottom: 2px;
    text-decoration: none;
    color: rgba(255,255,255,.55);
    font-size: .855rem;
    font-weight: 500;
    transition: background .15s, color .15s;
    position: relative;
  }
  .stu-nav-item:hover {
    background: rgba(255,255,255,.07);
    color: rgba(255,255,255,.9);
  }
  .stu-nav-item.active {
    background: rgba(99,102,241,.2);
    color: #fff;
    font-weight: 700;
    border-left: 3px solid #818cf8;
    padding-left: 7px;
  }
  .stu-nav-item.active::after { display: none; }

  /* ── Icon bubbles ─────────────────────────────────────── */
  .stu-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .stu-icon svg { width: 14px; height: 14px; }
  .stu-nav-item:hover .stu-icon,
  .stu-nav-item.active .stu-icon { transform: none; box-shadow: none; }

  /* Soft, flat icon colors — no gradients, no glow */
  .si-rose    { background: rgba(244, 63, 94, .18);  color: #f87171; }
  .si-amber   { background: rgba(245,158, 11, .18);  color: #fbbf24; }
  .si-emerald { background: rgba( 16,185,129, .18);  color: #34d399; }
  .si-sky     { background: rgba( 56,189,248, .18);  color: #38bdf8; }
  .si-violet  { background: rgba(167,139,250, .18);  color: #a78bfa; }
  .si-yellow  { background: rgba(253,224, 71, .18);  color: #fde047; }
  .si-teal    { background: rgba( 45,212,191, .18);  color: #2dd4bf; }
  .si-orange  { background: rgba(251,146, 60, .18);  color: #fb923c; }

  /* ── User footer ──────────────────────────────────────── */
  .stu-footer {
    padding: 10px 10px 12px;
    border-top: 1px solid rgba(255,255,255,.07);
  }
  .stu-user-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 10px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    transition: background .15s;
  }
  .stu-user-card::before { display: none; }
  .stu-user-card:hover { background: rgba(255,255,255,.09); }

  .stu-avatar {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #2d5fa8;
    display: flex; align-items: center; justify-content: center;
    font-size: .76rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    letter-spacing: -.5px;
  }
  .stu-user-info { flex: 1; min-width: 0; }
  .stu-user-name {
    font-size: .8rem;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .stu-user-role {
    font-size: .65rem;
    color: rgba(255,255,255,.35);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-top: 1px;
  }
  .stu-logout-btn {
    width: 26px; height: 26px;
    border-radius: 7px;
    border: 1px solid rgba(255,255,255,.1);
    background: transparent;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .15s, border-color .15s;
  }
  .stu-logout-btn:hover { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.25); }
  .stu-logout-btn svg { width: 13px; height: 13px; color: rgba(255,255,255,.4); }
  .stu-logout-btn:hover svg { color: #fca5a5; }

  /* ── Glassmorphic top header for students ─────────────── */
  .enc-header {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    background: rgba(255,255,255,.92) !important;
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

  /* ── Dark Mode ────────────────────────────────────────── */
  body.dark-mode {
    --sd-bg:      #0f172a;
    --sd-card:    #1e293b;
    --sd-border:  rgba(255,255,255,.08);
    --sd-navy:    #e2e8f0;
    --sd-muted:   #94a3b8;
    --sd-shadow:  0 4px 24px rgba(0,0,0,.4);
  }
  body.dark-mode .enc-main            { background: #0f172a; }
  body.dark-mode .enc-header          { background: #1e293b; border-bottom-color: rgba(255,255,255,.07); }
  body.dark-mode .enc-header__breadcrumb,
  body.dark-mode .enc-page__title,
  body.dark-mode .enc-page__subtitle  { color: #e2e8f0; }
  body.dark-mode .enc-card,
  body.dark-mode .sd-card,
  body.dark-mode .enc-card__header    { background: #1e293b; border-color: rgba(255,255,255,.08); color: #e2e8f0; }
  body.dark-mode .enc-card__title,
  body.dark-mode .enc-card__meta      { color: #e2e8f0; }
  body.dark-mode .enc-card__body      { color: #cbd5e1; }
  body.dark-mode table thead tr       { background: #0f172a; }
  body.dark-mode table tbody tr       { border-color: rgba(255,255,255,.06); }
  body.dark-mode table td,
  body.dark-mode table th             { color: #cbd5e1; }
  body.dark-mode input,
  body.dark-mode select,
  body.dark-mode textarea             { background: #0f172a; border-color: rgba(255,255,255,.12); color: #e2e8f0; }
  body.dark-mode input::placeholder,
  body.dark-mode textarea::placeholder { color: #475569; }

  /* ── Global academic-year picker (header) ─────────────── */
  .enc-year-picker {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--yellow-pale, #fefce8);
    border: 1.5px solid var(--yellow, #fbbf24);
    border-radius: 10px;
    padding: 5px 10px;
    transition: box-shadow .18s ease, border-color .18s ease;
  }
  .enc-year-picker:hover { box-shadow: 0 0 0 3px rgba(251,191,36,.18); }
  .enc-year-picker__icon { width: 16px; height: 16px; color: var(--yellow-dark, #d97706); flex-shrink: 0; }
  .enc-year-picker select {
    border: none;
    background: transparent;
    font-size: .82rem;
    font-weight: 700;
    color: var(--navy, #0a1f44);
    cursor: pointer;
    outline: none;
    padding-right: 2px;
    max-width: 220px;
  }
  .enc-year-picker select:focus { outline: none; }
  @media (max-width: 640px) {
    .enc-year-picker__icon { display: none; }
    .enc-year-picker select { max-width: 140px; }
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

  /* ═══════════════════════════════════════════════════════════
     DARK MODE CSS — Comprehensive dark theme for all pages
  ═══════════════════════════════════════════════════════════ */
  body.dark-mode {
    --sd-bg: #0f1419;
    --sd-surface: #1a1f2e;
    --sd-border: #2d3748;
    --sd-text: #e2e8f0;
    --sd-muted: #a0aec0;
    --sd-navy: #e2e8f0;
    background-color: #0f1419;
    color: #e2e8f0;
  }

  body.dark-mode {
    --card-bg: #1a1f2e;
    --input-bg: #2d3748;
    --hover-bg: #374151;
  }

  /* Page & card backgrounds */
  body.dark-mode .enc-shell { background: #0f1419; }
  body.dark-mode .enc-page,
  body.dark-mode .sd-page { background: #0f1419; color: #e2e8f0; }
  body.dark-mode .enc-card,
  body.dark-mode .sd-card,
  body.dark-mode .enc-page__card { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .enc-card__header { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .enc-card__body { background: #1a1f2e; }
  body.dark-mode .enc-card__footer { background: #0f1419; border-color: #2d3748; }

  /* Sidebar dark theme */
  body.dark-mode .enc-sidebar { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .enc-sidebar__brand,
  body.dark-mode .enc-sidebar__school { border-color: #2d3748; }
  body.dark-mode .enc-sidebar__section-label { color: #64748b; }
  body.dark-mode .enc-nav-item { color: #cbd5e1; }
  body.dark-mode .enc-nav-item:hover { background: #2d3748; color: #f1f5f9; }
  body.dark-mode .enc-nav-item.active { color: #60a5fa; background: rgba(96, 165, 250, 0.1); }
  body.dark-mode .enc-sidebar__footer { border-color: #2d3748; }
  body.dark-mode .enc-sidebar__user { color: #cbd5e1; }
  body.dark-mode .enc-sidebar__user-name { color: #f1f5f9; }
  body.dark-mode .enc-sidebar__user-role { color: #a0aec0; }

  /* Form elements */
  body.dark-mode .enc-input,
  body.dark-mode .enc-select,
  body.dark-mode .enc-textarea,
  body.dark-mode .st-input,
  body.dark-mode input[type="text"],
  body.dark-mode input[type="email"],
  body.dark-mode input[type="password"],
  body.dark-mode input[type="number"],
  body.dark-mode select,
  body.dark-mode textarea {
    background: #2d3748;
    color: #e2e8f0;
    border-color: #3f4757;
  }
  body.dark-mode .enc-input:focus,
  body.dark-mode .enc-select:focus,
  body.dark-mode .enc-textarea:focus,
  body.dark-mode .st-input:focus,
  body.dark-mode input:focus,
  body.dark-mode select:focus,
  body.dark-mode textarea:focus {
    background: #3f4757;
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
  }

  /* Buttons */
  body.dark-mode .enc-btn,
  body.dark-mode .st-btn,
  body.dark-mode button[type="submit"] {
    background: #1d4ed8;
    color: #fff;
    border-color: #1d4ed8;
  }
  body.dark-mode .enc-btn:hover,
  body.dark-mode .st-btn:hover,
  body.dark-mode button[type="submit"]:hover {
    background: #1e40af;
    border-color: #1e40af;
  }

  /* Tables */
  body.dark-mode .enc-table,
  body.dark-mode .gr-table,
  body.dark-mode table {
    background: #1a1f2e;
    border-color: #2d3748;
  }
  body.dark-mode .enc-table th,
  body.dark-mode .gr-table th,
  body.dark-mode table th {
    background: #0f1419;
    color: #cbd5e1;
    border-color: #2d3748;
  }
  body.dark-mode .enc-table td,
  body.dark-mode .gr-table td,
  body.dark-mode table td {
    border-color: #2d3748;
    color: #e2e8f0;
  }
  body.dark-mode .enc-table tr:hover,
  body.dark-mode .gr-table tr:hover,
  body.dark-mode table tr:hover {
    background: #2d3748;
  }

  /* Alerts and badges */
  body.dark-mode .enc-alert,
  body.dark-mode .st-alert { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .enc-alert--success { background: rgba(34, 197, 94, 0.1); border-color: #22c55e; }
  body.dark-mode .enc-alert--error { background: rgba(239, 68, 68, 0.1); border-color: #ef4444; }
  body.dark-mode .enc-alert--warning { background: rgba(245, 158, 11, 0.1); border-color: #f59e0b; }
  body.dark-mode .enc-alert--info { background: rgba(59, 130, 246, 0.1); border-color: #3b82f6; }

  body.dark-mode .badge { background: #2d3748; color: #e2e8f0; }
  body.dark-mode .badge--primary { background: #1d4ed8; color: #fff; }
  body.dark-mode .badge--success { background: rgba(34, 197, 94, 0.2); color: #86efac; }
  body.dark-mode .badge--danger { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

  /* Status badges */
  body.dark-mode .att-status,
  body.dark-mode .grade-badge { border: none; }
  body.dark-mode .att-status.present,
  body.dark-mode .grade-badge.locked { background: rgba(34, 197, 94, 0.2); color: #86efac; }
  body.dark-mode .att-status.absent { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
  body.dark-mode .att-status.late { background: rgba(245, 158, 11, 0.2); color: #fde047; }
  body.dark-mode .att-status.excused,
  body.dark-mode .grade-badge.finalized { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
  body.dark-mode .grade-badge.submitted { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; }

  /* Stats cards */
  body.dark-mode .att-stat,
  body.dark-mode .gr-stat { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .att-stat__label,
  body.dark-mode .gr-stat__label { color: #a0aec0; }
  body.dark-mode .att-stat__value,
  body.dark-mode .gr-stat__value { color: #f1f5f9; }

  /* Settings pages */
  body.dark-mode .st-page { background: #0f1419; color: #e2e8f0; }
  body.dark-mode .st-sidenav { background: transparent; border-color: #2d3748; }
  body.dark-mode .st-sidenav__item { color: #cbd5e1; }
  body.dark-mode .st-sidenav__item:hover { background: #2d3748; color: #f1f5f9; }
  body.dark-mode .st-sidenav__item.active { color: #60a5fa; background: rgba(96, 165, 250, 0.1); }
  body.dark-mode .st-tab { display: block; }
  body.dark-mode .st-card__head { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .st-card__body { background: #1a1f2e; }
  body.dark-mode .st-card__foot { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .st-label { color: #cbd5e1; }
  body.dark-mode .st-hint { color: #a0aec0; }
  body.dark-mode .st-toggle-row { border-color: #2d3748; }
  body.dark-mode .st-toggle-label { color: #f1f5f9; }
  body.dark-mode .st-toggle-desc { color: #a0aec0; }

  /* Switch/toggle components */
  body.dark-mode .sw { background: #2d3748; }
  body.dark-mode .sw__track { background: #3f4757; }
  body.dark-mode input:checked + .sw__track { background: #1d4ed8; }

  /* Enrollment cards & boxes */
  body.dark-mode .enc-card { background: #1a1f2e; }
  body.dark-mode .component-box { background: #2d3748; }
  body.dark-mode .component-box { border-color: #3f4757; }
  body.dark-mode .component-value { color: #60a5fa; }

  /* Text styling */
  body.dark-mode a { color: #60a5fa; }
  body.dark-mode a:hover { color: #93c5fd; }
  body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
    color: #f1f5f9;
  }

  /* Modals & dialogs */
  body.dark-mode [role="dialog"] { background: #1a1f2e; color: #e2e8f0; border-color: #2d3748; }
  body.dark-mode .modal-backdrop { background: rgba(0, 0, 0, 0.7); }

  /* Pagination */
  body.dark-mode .pagination a,
  body.dark-mode .pagination span { color: #cbd5e1; }
  body.dark-mode .pagination a:hover { background: #2d3748; }
  body.dark-mode .pagination .active { background: #1d4ed8; color: #fff; }

  /* Message threads */
  body.dark-mode .msg-bubble { background: #2d3748; color: #e2e8f0; }
  body.dark-mode .msg-bubble.mine { background: #1d4ed8; color: #fff; }

  /* Dropdown menus */
  body.dark-mode .dropdown-menu { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .dropdown-menu a { color: #cbd5e1; }
  body.dark-mode .dropdown-menu a:hover { background: #2d3748; color: #f1f5f9; }

  /* Progress bars & indicators */
  body.dark-mode .att-bar { background: #2d3748; }
  body.dark-mode .progress { background: #2d3748; }

  /* Transition for smooth switching */
  body.dark-mode * {
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
  }
  </style>
  @endif

  @stack('head')

  <script>
  // Apply dark mode on page load based on localStorage
  (function() {
    const isDarkMode = localStorage.getItem('dark_mode') === '1' ||
                       (document.body.classList.contains('dark-mode'));
    if (isDarkMode) {
      document.body.classList.add('dark-mode');
      localStorage.setItem('dark_mode', '1');
    }
  })();
  </script>
</head>
<body class="{{ auth()->check() && auth()->user()->pref('dark_mode') ? 'dark-mode' : '' }}">

<div class="enc-shell">

  {{-- ════════════════════════════════
       SIDEBAR
  ═══════════════════════════════════ --}}
  <aside class="enc-sidebar" id="enc-sidebar">

  @if(auth()->user()->role_id === '01')
  {{-- ╔══════════════════════════════════════╗
       ║   STUDENT SIDEBAR — Aurora Dark      ║
       ╚══════════════════════════════════════╝ --}}

    {{-- Brand --}}
    <a href="{{ route('student.dashboard') }}" class="stu-brand" style="text-decoration:none;">
      <div class="stu-brand__glow"></div>
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="stu-brand__logo">
    </a>

    {{-- School identity --}}
    <div class="stu-school">
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="stu-school__seal">
      <div>
        <div class="stu-school__name">Phil. Academy of Sakya</div>
        <div class="stu-school__sub">Student Portal · {{ date('Y') }}</div>
      </div>
    </div>

    {{-- Navigation --}}
    <nav class="stu-nav">

      {{-- My Account section --}}
      <div class="stu-section">
        <span class="stu-section__text">My Account</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.dashboard') }}"
         class="stu-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <span class="stu-icon si-rose">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </span>
        User Profile
      </a>

      <a href="{{ route('student.academic-holds') }}"
         class="stu-nav-item {{ request()->routeIs('student.academic-holds') ? 'active' : '' }}">
        <span class="stu-icon si-amber">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </span>
        Academic Holds
      </a>

      <a href="{{ route('student.payments.index') }}"
         class="stu-nav-item {{ request()->routeIs('student.payments.*') ? 'active' : '' }}">
        <span class="stu-icon si-emerald">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
          </svg>
        </span>
        Payments
      </a>

      {{-- Academics section --}}
      <div class="stu-section">
        <span class="stu-section__text">Academics</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.admission-documents') }}"
         class="stu-nav-item {{ request()->routeIs('student.admission-documents') ? 'active' : '' }}">
        <span class="stu-icon si-sky">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </span>
        Admission Documents
      </a>

      <a href="{{ route('student.course-offerings') }}"
         class="stu-nav-item {{ request()->routeIs('student.course-offerings') ? 'active' : '' }}">
        <span class="stu-icon si-violet">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
          </svg>
        </span>
        Course Offerings
      </a>

      <a href="{{ route('student.report-card') }}"
         class="stu-nav-item {{ request()->routeIs('student.report-card') ? 'active' : '' }}">
        <span class="stu-icon si-yellow">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
          </svg>
        </span>
        Grade Report
      </a>

      <a href="{{ route('student.grade-archive') }}"
         class="stu-nav-item {{ request()->routeIs('student.grade-archive') ? 'active' : '' }}">
        <span class="stu-icon si-yellow">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
          </svg>
        </span>
        Grade Archive
      </a>

      <a href="{{ route('student.program-curriculum') }}"
         class="stu-nav-item {{ request()->routeIs('student.program-curriculum') ? 'active' : '' }}">
        <span class="stu-icon si-teal">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
          </svg>
        </span>
        Program Curriculum
      </a>

      <a href="{{ route('student.schedule') }}"
         class="stu-nav-item {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
        <span class="stu-icon si-orange">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
          </svg>
        </span>
        Schedule & Assessment
      </a>

      <a href="{{ route('student.attendance') }}"
         class="stu-nav-item {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
        <span class="stu-icon si-indigo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </span>
        My Attendance
      </a>

      <a href="{{ route('assignments.student.index') }}"
         class="stu-nav-item {{ request()->routeIs('assignments.student.*') ? 'active' : '' }}">
        <span class="stu-icon si-amber">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
          </svg>
        </span>
        My Assignments
      </a>

      <a href="{{ route('documents.student.index') }}"
         class="stu-nav-item {{ request()->routeIs('documents.student.*') ? 'active' : '' }}">
        <span class="stu-icon si-teal">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </span>
        Document Requests
      </a>

      <a href="{{ route('calendar.index') }}"
         class="stu-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
        <span class="stu-icon si-sky">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
          </svg>
        </span>
        School Calendar
      </a>

      <a href="{{ route('student.inbox') }}"
         class="stu-nav-item {{ request()->routeIs('student.inbox*') ? 'active' : '' }}">
        <span class="stu-icon si-violet">
          @php $stuUnread = \App\Models\Message::where('recipient_id', auth()->id())->whereNull('read_at')->count(); @endphp
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
          </svg>
        </span>
        Inbox
        @if($stuUnread > 0)
          <span style="margin-left:auto;background:#ef4444;color:#fff;border-radius:99px;font-size:.65rem;font-weight:700;padding:.1rem .45rem;">{{ $stuUnread }}</span>
        @endif
      </a>

      {{-- Settings --}}
      <div class="stu-section">
        <span class="stu-section__text">Account</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.settings.index') }}"
         class="stu-nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
        <span class="stu-icon si-violet">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
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

      <a href="{{ route('faculty.inbox') }}"
         class="enc-nav-item {{ request()->routeIs('faculty.inbox*') ? 'active' : '' }}">
        @php $facUnread = \App\Models\Message::where('recipient_id', auth()->id())->whereNull('read_at')->count(); @endphp
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
        </svg>
        Student Inbox
        @if($facUnread > 0)
          <span style="margin-left:auto;background:#ef4444;color:#fff;border-radius:99px;font-size:.65rem;font-weight:700;padding:.1rem .45rem;">{{ $facUnread }}</span>
        @endif
      </a>

      <a href="{{ route('assignments.faculty.index') }}"
         class="enc-nav-item {{ request()->routeIs('assignments.faculty.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
        </svg>
        Assignments
      </a>

      <a href="{{ route('leave.faculty.index') }}"
         class="enc-nav-item {{ request()->routeIs('leave.faculty.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        Leave Requests
      </a>

      <a href="{{ route('calendar.index') }}"
         class="enc-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
        </svg>
        School Calendar
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

      <a href="{{ route('calendar.index') }}"
         class="enc-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        School Calendar
      </a>

      <a href="{{ route('complaints.manage') }}"
         class="enc-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
        </svg>
        Grade Complaints
      </a>

      <a href="{{ route('documents.registrar.index') }}"
         class="enc-nav-item {{ request()->routeIs('documents.registrar.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
        </svg>
        Doc Requests (New)
      </a>

      <a href="{{ route('leave.admin.index') }}"
         class="enc-nav-item {{ request()->routeIs('leave.admin.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Leave Management
      </a>

      <a href="{{ route('analytics.index') }}"
         class="enc-nav-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        Analytics
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">DepEd SF Forms</div>

      <a href="{{ route('sf.sf1') }}"
         class="enc-nav-item {{ request()->routeIs('sf.sf1') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
        </svg>
        SF1 Class List
      </a>

      <a href="{{ route('sf.sf2') }}"
         class="enc-nav-item {{ request()->routeIs('sf.sf2') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        SF2 Attendance
      </a>

      <a href="{{ route('sf.sf9') }}"
         class="enc-nav-item {{ request()->routeIs('sf.sf9') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
        </svg>
        SF9 Report Card
      </a>

      <a href="{{ route('sf.sf10') }}"
         class="enc-nav-item {{ request()->routeIs('sf.sf10') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
        SF10 Permanent
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
        Schedules
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Academic Setup</div>

      <a href="{{ route('admin.academic-years.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Academic Years
      </a>

      <a href="{{ route('admin.sections.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Sections
      </a>

      <a href="{{ route('admin.subjects.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        Subjects
      </a>

      <a href="{{ route('admin.classrooms.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4a2 2 0 012-2h14a2 2 0 012 2v4M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01"/>
        </svg>
        Classrooms
      </a>

      <div class="enc-sidebar__section-label" style="margin-top:8px;">Enrollment</div>

      <a href="{{ route('admin.payments.index') }}"
         class="enc-nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
        </svg>
        Payments
      </a>

      <a href="{{ route('analytics.index') }}"
         class="enc-nav-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        Analytics
      </a>

      <a href="{{ route('calendar.index') }}"
         class="enc-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
        </svg>
        School Calendar
      </a>

      <a href="{{ route('leave.admin.index') }}"
         class="enc-nav-item {{ request()->routeIs('leave.admin.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Leave Management
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

    {{-- Mobile overlay --}}
    <div class="enc-sidebar-overlay" id="enc-sidebar-overlay"></div>

    {{-- Top Header --}}
    <header class="enc-header">
      <div class="enc-header__left">
        {{-- Hamburger (mobile only) --}}
        <button class="enc-hamburger" id="enc-hamburger" aria-label="Toggle menu" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
          </svg>
        </button>
        <div class="enc-header__breadcrumb">
          <span>{{ auth()->user()->role_label ?? 'Portal' }}</span>
          <span class="enc-header__breadcrumb-sep">›</span>
          <span>@yield('breadcrumb', 'Dashboard')</span>
        </div>
      </div>

      <div class="enc-header__right">
        {{-- Global Academic-Year selector (staff only) --}}
        @if(auth()->user()->role_id !== '01' && isset($globalAcademicYears) && $globalAcademicYears->isNotEmpty())
        <form method="POST" action="{{ route('academic-year.switch') }}" id="enc-year-form" class="enc-year-picker" title="Working academic year">
          @csrf
          <svg class="enc-year-picker__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
          </svg>
          <select name="academic_year_id" onchange="document.getElementById('enc-year-form').submit()" aria-label="Academic year">
            @foreach($globalAcademicYears as $ay)
              <option value="{{ $ay->id }}" {{ (int)$globalActiveYearId === (int)$ay->id ? 'selected' : '' }}>
                S.Y. {{ $ay->year_label }}{{ $ay->status === 'active' ? ' • active' : '' }}
              </option>
            @endforeach
          </select>
        </form>
        @endif

        <div class="enc-header__time" id="enc-clock">--:-- --</div>

        {{-- Notifications Dropdown --}}
        @php
          $unreadCount   = auth()->user()->unreadNotifications()->count();
          $recentNotifs  = \App\Models\Notification::where('user_id', auth()->id())
                             ->orderByDesc('created_at')->limit(8)->get();
        @endphp
        <div class="notif-wrap" id="notif-wrap">
          <button type="button" class="enc-icon-btn" id="notif-bell" title="Notifications" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" style="pointer-events:none;">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            @if($unreadCount > 0)
              <span id="notif-badge" style="position:absolute;top:-4px;right:-4px;background:#e11d48;color:#fff;
                border-radius:999px;font-size:.6rem;font-weight:800;min-width:16px;height:16px;
                display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;
                pointer-events:none;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
              </span>
            @else
              <span id="notif-badge" style="display:none;position:absolute;top:-4px;right:-4px;background:#e11d48;color:#fff;
                border-radius:999px;font-size:.6rem;font-weight:800;min-width:16px;height:16px;
                align-items:center;justify-content:center;padding:0 3px;line-height:1;pointer-events:none;"></span>
            @endif
          </button>

          <div class="notif-dropdown" id="notif-dropdown">
            <div class="notif-dropdown__head">
              <span class="notif-dropdown__title">
                Notifications
                @if($unreadCount > 0)
                  <span style="background:#e11d48;color:#fff;font-size:.65rem;padding:2px 6px;border-radius:99px;margin-left:6px;">{{ $unreadCount }}</span>
                @endif
              </span>
              @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="notif-mark-all-btn">Mark all read</button>
                </form>
              @endif
            </div>
            <div class="notif-list" id="notif-list">
              @forelse($recentNotifs as $notif)
                <div class="notif-item {{ $notif->isUnread() ? 'notif-item--unread' : '' }}">
                  <div class="notif-dot {{ $notif->isUnread() ? '' : 'notif-dot--read' }}"></div>
                  <div class="notif-body">
                    <div class="notif-title">{{ $notif->title }}</div>
                    <div class="notif-text">{{ $notif->body }}</div>
                    <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                  </div>
                  @if($notif->isUnread())
                    <form method="POST" action="{{ route('notifications.mark-read', $notif) }}" style="margin:0;flex-shrink:0;">
                      @csrf
                      <button type="submit" title="Mark as read"
                        style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:.75rem;padding:2px 4px;border-radius:4px;transition:color .12s;"
                        onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'">✓</button>
                    </form>
                  @endif
                </div>
              @empty
                <div class="notif-empty">
                  <div style="font-size:1.8rem;margin-bottom:6px;">🔔</div>
                  <div>No notifications yet</div>
                </div>
              @endforelse
            </div>
            <div class="notif-dropdown__foot">
              <a href="{{ route('notifications.index') }}">View all notifications →</a>
            </div>
          </div>
        </div>

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

{{-- ══════════════════════════════════════════════════════
     GLOBAL CONFIRM / ALERT MODAL — replaces confirm() & alert()
═══════════════════════════════════════════════════════ --}}
<div id="enc-confirm-modal" style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center;">
  <div id="enc-confirm-backdrop" style="position:absolute;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);"></div>
  <div id="enc-confirm-dialog" style="
      position:relative;z-index:1;
      background:#fff;border-radius:20px;
      width:100%;max-width:420px;margin:0 16px;
      box-shadow:0 24px 64px rgba(15,23,42,.18);
      overflow:hidden;
      transform:scale(.95) translateY(10px);
      opacity:0;
      transition:transform .22s cubic-bezier(.34,1.56,.64,1),opacity .18s ease;">
    <div id="enc-confirm-top" style="height:4px;background:linear-gradient(90deg,#7c3aed,#a78bfa);"></div>
    <div style="padding:28px 26px 24px;">
      <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:22px;">
        <div id="enc-confirm-icon-wrap" style="width:46px;height:46px;border-radius:13px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg id="enc-confirm-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:#d97706;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </div>
        <div style="flex:1;min-width:0;">
          <h3 id="enc-confirm-title" style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 5px;"></h3>
          <p id="enc-confirm-message" style="font-size:.875rem;color:#64748b;margin:0;line-height:1.55;"></p>
        </div>
      </div>
      <div id="enc-confirm-actions" style="display:flex;gap:10px;justify-content:flex-end;">
        <button id="enc-confirm-cancel" type="button"
          style="padding:.6rem 1.1rem;border:1px solid #e2e8f0;border-radius:9px;background:#fff;color:#374151;font-size:.875rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s;"
          onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
          onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
          Cancel
        </button>
        <button id="enc-confirm-ok" type="button"
          style="padding:.6rem 1.25rem;border:none;border-radius:9px;background:#7c3aed;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;transition:background .15s;"
          onmouseover="this.style.background='#6d28d9'"
          onmouseout="this.style.background=encConfirmOkColor||'#7c3aed'">
          Confirm
        </button>
      </div>
    </div>
  </div>
</div>

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

  // ── Hamburger + sidebar toggle ────────────────────────────────────────
  (function () {
    const burger  = document.getElementById('enc-hamburger');
    const sidebar = document.getElementById('enc-sidebar');
    const overlay = document.getElementById('enc-sidebar-overlay');
    if (!burger || !sidebar) return;

    function openSidebar() {
      sidebar.classList.add('open');
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }

    burger.addEventListener('click', function () {
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar on nav link click (mobile UX)
    sidebar.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 900) closeSidebar();
      });
    });
  })();

  // ── Notification dropdown ─────────────────────────────────────────────
  (function () {
    const bell     = document.getElementById('notif-bell');
    const dropdown = document.getElementById('notif-dropdown');
    const badge    = document.getElementById('notif-badge');
    if (!bell || !dropdown) return;

    bell.addEventListener('click', function (e) {
      e.stopPropagation();
      const open = dropdown.classList.toggle('open');
      bell.setAttribute('aria-expanded', open);
    });

    document.addEventListener('click', function (e) {
      if (!document.getElementById('notif-wrap').contains(e.target)) {
        dropdown.classList.remove('open');
        bell.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        dropdown.classList.remove('open');
        bell.setAttribute('aria-expanded', 'false');
      }
    });

    // Poll unread count every 30s and update badge
    function updateBadge() {
      fetch('{{ route("notifications.unread-count") }}')
        .then(r => r.json())
        .then(data => {
          if (!badge) return;
          if (data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        })
        .catch(() => {});
    }
    setInterval(updateBadge, 30000);
    window.addEventListener('focus', updateBadge);
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

  // ── Global confirm / alert modal ─────────────────────────────────────
  let encConfirmOkColor = '#7c3aed';
  let _encConfirmResolve = null;

  function _encConfirmShow(message, opts) {
    opts = opts || {};
    const title       = opts.title       || 'Confirm Action';
    const confirmText = opts.confirmText || 'Confirm';
    const cancelText  = opts.cancelText  || null;
    const type        = opts.type        || 'warning'; // warning | danger | info | success

    const iconWrap = document.getElementById('enc-confirm-icon-wrap');
    const icon     = document.getElementById('enc-confirm-icon');
    const topBar   = document.getElementById('enc-confirm-top');
    const okBtn    = document.getElementById('enc-confirm-ok');
    const cancelBtn= document.getElementById('enc-confirm-cancel');

    const themes = {
      warning: { bg:'#fef3c7', color:'#d97706', bar:'linear-gradient(90deg,#f59e0b,#fbbf24)',
        btnBg:'#f59e0b', btnHover:'#d97706',
        icon:'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' },
      danger:  { bg:'#fee2e2', color:'#dc2626', bar:'linear-gradient(90deg,#ef4444,#f87171)',
        btnBg:'#ef4444', btnHover:'#dc2626',
        icon:'M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L10.051 3.378c.866-1.5 3.032-1.5 3.898 0L21.303 16.126zM12 15.75h.007v.008H12v-.008z' },
      info:    { bg:'#dbeafe', color:'#2563eb', bar:'linear-gradient(90deg,#3b82f6,#60a5fa)',
        btnBg:'#3b82f6', btnHover:'#2563eb',
        icon:'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z' },
      success: { bg:'#d1fae5', color:'#059669', bar:'linear-gradient(90deg,#10b981,#34d399)',
        btnBg:'#10b981', btnHover:'#059669',
        icon:'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const t = themes[type] || themes.warning;

    iconWrap.style.background = t.bg;
    icon.style.color = t.color;
    icon.querySelector('path').setAttribute('d', t.icon);
    topBar.style.background = t.bar;
    okBtn.textContent = confirmText;
    okBtn.style.background = t.btnBg;
    encConfirmOkColor = t.btnBg;
    okBtn.onmouseover = function(){ this.style.background = t.btnHover; };
    okBtn.onmouseout  = function(){ this.style.background = t.btnBg; };

    if (cancelText) {
      cancelBtn.textContent = cancelText;
      cancelBtn.style.display = '';
    } else {
      cancelBtn.style.display = 'none';
    }

    document.getElementById('enc-confirm-title').textContent   = title;
    document.getElementById('enc-confirm-message').textContent = message;

    const modal  = document.getElementById('enc-confirm-modal');
    const dialog = document.getElementById('enc-confirm-dialog');
    modal.style.display = 'flex';
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        dialog.style.transform = 'scale(1) translateY(0)';
        dialog.style.opacity   = '1';
      });
    });
  }

  function _encConfirmClose(result) {
    const modal  = document.getElementById('enc-confirm-modal');
    const dialog = document.getElementById('enc-confirm-dialog');
    dialog.style.transform = 'scale(.95) translateY(10px)';
    dialog.style.opacity   = '0';
    setTimeout(function () { modal.style.display = 'none'; }, 200);
    if (_encConfirmResolve) { _encConfirmResolve(result); _encConfirmResolve = null; }
  }

  document.getElementById('enc-confirm-ok').addEventListener('click', function () { _encConfirmClose(true); });
  document.getElementById('enc-confirm-cancel').addEventListener('click', function () { _encConfirmClose(false); });
  document.getElementById('enc-confirm-backdrop').addEventListener('click', function () { _encConfirmClose(false); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.getElementById('enc-confirm-modal').style.display === 'flex') {
      _encConfirmClose(false);
    }
  });

  window.encConfirm = function (message, opts) {
    return new Promise(function (resolve) {
      _encConfirmResolve = resolve;
      _encConfirmShow(message, opts);
    });
  };

  window.encAlert = function (message, opts) {
    opts = Object.assign({ type: 'info', title: 'Notice', confirmText: 'OK' }, opts || {});
    opts.cancelText = null;
    return window.encConfirm(message, opts);
  };

  // ── data-confirm interceptor — auto-wires any form/button ────────────
  document.addEventListener('submit', function (e) {
    const form = e.target;
    const msg  = form.getAttribute('data-confirm');
    if (!msg) return;
    e.preventDefault();
    const type  = form.getAttribute('data-confirm-type')  || 'warning';
    const title = form.getAttribute('data-confirm-title') || 'Confirm Action';
    const ok    = form.getAttribute('data-confirm-ok')    || 'Confirm';
    window.encConfirm(msg, { type: type, title: title, confirmText: ok }).then(function (confirmed) {
      if (confirmed) { form.removeAttribute('data-confirm'); form.submit(); }
    });
  }, true);

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[data-confirm], a[data-confirm]');
    if (!btn) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    const msg   = btn.getAttribute('data-confirm');
    const type  = btn.getAttribute('data-confirm-type')  || 'danger';
    const title = btn.getAttribute('data-confirm-title') || 'Confirm Action';
    const ok    = btn.getAttribute('data-confirm-ok')    || 'Confirm';
    window.encConfirm(msg, { type: type, title: title, confirmText: ok }).then(function (confirmed) {
      if (confirmed) {
        const formId = btn.getAttribute('form');
        const form   = formId ? document.getElementById(formId) : btn.closest('form');
        if (form) { form.removeAttribute('data-confirm'); form.submit(); }
      }
    });
  }, true);
</script>

@stack('scripts')

</body>
</html>
