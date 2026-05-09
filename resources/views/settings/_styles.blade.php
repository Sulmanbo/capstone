<style>
/* ── Page shell ─────────────────────────────────────────────────────── */
.st-page__header { margin-bottom: 24px; }
.st-page__title  { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
.st-page__sub    { font-size: .875rem; color: #94a3b8; margin: 0; }

/* ── Layout ─────────────────────────────────────────────────────────── */
.st-layout { display: grid; grid-template-columns: 200px 1fr; gap: 24px; align-items: start; }
@media(max-width:840px){ .st-layout { grid-template-columns: 1fr; } }

/* ── Side nav ───────────────────────────────────────────────────────── */
.st-sidenav {
  position: sticky; top: 24px;
  background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
  padding: 8px; overflow: hidden;
}
.st-sidenav__item {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 12px; border-radius: 9px;
  text-decoration: none; color: #64748b;
  font-size: .845rem; font-weight: 500;
  transition: background .13s, color .13s;
  cursor: pointer;
}
.st-sidenav__item svg { width: 15px; height: 15px; flex-shrink: 0; }
.st-sidenav__item:hover { background: #f8fafc; color: #0f172a; }
.st-sidenav__item.active { background: #eef2ff; color: #4338ca; font-weight: 700; }

/* ── Section card ───────────────────────────────────────────────────── */
.st-card {
  background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
  overflow: hidden; margin-bottom: 20px;
}
.st-card__head {
  padding: 20px 24px 16px; border-bottom: 1px solid #f1f5f9;
}
.st-card__title { font-size: .95rem; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
.st-card__desc  { font-size: .8rem; color: #94a3b8; }
.st-card__body  { padding: 24px; }
.st-card__foot  {
  padding: 16px 24px; border-top: 1px solid #f8fafc;
  background: #fafafa; display: flex; align-items: center;
}

/* ── Form elements ──────────────────────────────────────────────────── */
.st-field  { margin-bottom: 18px; }
.st-field:last-child { margin-bottom: 0; }
.st-label  {
  display: block; font-size: .72rem; font-weight: 700; color: #374151;
  margin-bottom: 7px; text-transform: uppercase; letter-spacing: .04em;
}
.st-input, .st-select, .st-textarea {
  width: 100%; padding: .6rem .85rem;
  border: 1px solid #e2e8f0; border-radius: 10px;
  font-size: .875rem; color: #0f172a; background: #fff;
  transition: border-color .15s, box-shadow .15s;
  box-sizing: border-box;
}
.st-input:focus, .st-select:focus, .st-textarea:focus {
  outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.st-input:disabled, .st-select:disabled, .st-textarea:disabled {
  background: #f8fafc; color: #94a3b8; cursor: not-allowed;
}
.st-input--error { border-color: #ef4444 !important; }
.st-textarea { resize: vertical; min-height: 80px; }
.st-select   { height: 42px; }
.st-time-input { width: 110px !important; padding: .5rem .7rem !important; }
.st-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.st-hint  { font-size: .73rem; color: #94a3b8; margin-top: 5px; }
.st-error { font-size: .74rem; color: #ef4444; margin-top: 4px; }
.st-muted { font-size: .82rem; color: #64748b; margin: 0; line-height: 1.6; }
.st-divider { height: 1px; background: #f1f5f9; margin: 20px 0; }

/* ── Toggle switch ──────────────────────────────────────────────────── */
.sw { position: relative; display: inline-block; width: 42px; height: 24px; flex-shrink: 0; }
.sw input { opacity: 0; width: 0; height: 0; }
.sw__track {
  position: absolute; inset: 0; border-radius: 24px;
  background: #e2e8f0; transition: background .2s; cursor: pointer;
}
.sw input:checked ~ .sw__track { background: #4f46e5; }
.sw input:disabled ~ .sw__track { background: #e2e8f0 !important; opacity: .5; cursor: not-allowed; }
.sw__thumb {
  position: absolute; top: 3px; left: 3px;
  width: 18px; height: 18px; border-radius: 50%;
  background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.2);
  transition: transform .2s; pointer-events: none;
}
.sw input:checked ~ .sw__thumb { transform: translateX(18px); }

/* ── Toggle row ─────────────────────────────────────────────────────── */
.st-toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 0; border-bottom: 1px solid #f8fafc; gap: 16px;
}
.st-toggle-row:last-child { border-bottom: none; padding-bottom: 0; }
.st-toggle-info { flex: 1; min-width: 0; }
.st-toggle-label { font-size: .875rem; font-weight: 600; color: #0f172a; }
.st-toggle-desc  { font-size: .78rem; color: #94a3b8; margin-top: 2px; }

/* ── Radio group ────────────────────────────────────────────────────── */
.st-radio-group { display: flex; flex-direction: column; gap: 8px; }
.st-radio {
  display: flex; align-items: center; gap: 10px;
  font-size: .875rem; color: #374151; cursor: pointer;
}
.st-radio input { display: none; }
.st-radio__box {
  width: 18px; height: 18px; border-radius: 50%;
  border: 2px solid #d1d5db; flex-shrink: 0;
  transition: border-color .15s, background .15s;
  position: relative;
}
.st-radio__box::after {
  content: ''; position: absolute; top: 50%; left: 50%;
  transform: translate(-50%,-50%) scale(0);
  width: 8px; height: 8px; border-radius: 50%;
  background: #4f46e5; transition: transform .15s;
}
.st-radio input:checked ~ .st-radio__box { border-color: #4f46e5; }
.st-radio input:checked ~ .st-radio__box::after { transform: translate(-50%,-50%) scale(1); }

/* ── Buttons ────────────────────────────────────────────────────────── */
.st-btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: .55rem 1.2rem; border: none; border-radius: 9px;
  background: #0f172a; color: #fff;
  font-size: .875rem; font-weight: 700; cursor: pointer;
  transition: background .15s; text-decoration: none;
}
.st-btn:hover { background: #1e293b; }
.st-btn-outline {
  display: inline-flex; align-items: center; gap: 7px;
  padding: .5rem 1.1rem; border: 1px solid #e2e8f0; border-radius: 9px;
  background: #fff; color: #374151;
  font-size: .875rem; font-weight: 600; cursor: pointer;
  text-decoration: none; transition: border-color .15s, background .15s;
}
.st-btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }

/* ── Alert ──────────────────────────────────────────────────────────── */
.st-alert {
  padding: 12px 16px; border-radius: 10px;
  font-size: .85rem; font-weight: 600; margin-bottom: 16px;
}
.st-alert--success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.st-alert--error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ── Badge ──────────────────────────────────────────────────────────── */
.st-badge {
  display: inline-block; font-size: .65rem; font-weight: 700;
  padding: .15rem .5rem; border-radius: 20px;
  background: #f1f5f9; color: #64748b;
  vertical-align: middle; margin-left: 6px;
}

/* ── Info grid ──────────────────────────────────────────────────────── */
.st-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.st-info-item { display: flex; flex-direction: column; gap: 3px; }
.st-info-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; }
.st-info-val   { font-size: .9rem; font-weight: 600; color: #0f172a; }

/* ── Code badge ─────────────────────────────────────────────────────── */
.st-code {
  display: inline-block; padding: .15rem .5rem; border-radius: 5px;
  background: #f1f5f9; color: #475569; font-family: monospace; font-size: .82rem;
}

/* ── Table ──────────────────────────────────────────────────────────── */
.st-table { width: 100%; border-collapse: collapse; font-size: .845rem; }
.st-table th {
  text-align: left; padding: 10px 16px;
  font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
  color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9;
}
.st-table td {
  padding: 13px 16px; color: #374151; border-bottom: 1px solid #f8fafc;
}
.st-table tr:last-child td { border-bottom: none; }

/* ── Consultation grid ──────────────────────────────────────────────── */
.st-consult-grid  { display: flex; flex-direction: column; gap: 0; }
.st-consult-row   {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 0; border-bottom: 1px solid #f8fafc;
}
.st-consult-row:last-child { border-bottom: none; }
.st-consult-day   { width: 100px; font-size: .875rem; font-weight: 600; color: #374151; flex-shrink: 0; }
.st-consult-times { display: flex; align-items: center; gap: 8px; transition: opacity .2s; }
</style>
