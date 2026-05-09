{{-- resources/views/auth/force-reset.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Set New Password — EncryptEd</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    body {
      background: var(--navy-dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(37,99,235,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,.04) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
    }
    .reset-card {
      width: 100%;
      max-width: 460px;
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0,0,0,.4);
      animation: fadeUp .35s ease both;
      position: relative;
      z-index: 1;
    }
    .reset-card::before {
      content: '';
      display: block;
      height: 4px;
      background: linear-gradient(90deg, var(--accent-amber) 0%, var(--danger) 100%);
    }
    .reset-header {
      padding: 28px 36px 20px;
      border-bottom: 1px solid var(--gray-100);
      background: var(--warning-bg);
    }
    .reset-header-top {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 8px;
    }
    .reset-header-icon {
      width: 40px; height: 40px;
      background: var(--warning-bg);
      border: 1.5px solid var(--warning-border);
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .reset-header-icon svg { width: 20px; height: 20px; color: var(--warning); }
    .reset-title { font-size: 1.1rem; font-weight: 700; color: var(--navy); letter-spacing:-.01em; }
    .reset-subtitle { font-size: .78rem; color: var(--gray-500); line-height: 1.5; }
    .reset-body { padding: 24px 36px 32px; }
    .reset-form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
    .reset-label { font-size: .72rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase; letter-spacing: .07em; }
    .reset-input {
      width: 100%; height: 44px; padding: 0 40px 0 14px;
      border: 1.5px solid var(--gray-200); border-radius: var(--radius-md);
      font-size: .875rem; color: var(--gray-700); font-family: var(--font-body);
      background: white; outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .reset-input:focus { border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .reset-input.is-error { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(220,38,38,.08); }
    .reset-input-wrap { position: relative; }
    .reset-input-wrap .toggle-icon {
      position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
      width: 16px; height: 16px; color: var(--gray-300); cursor: pointer; transition: color .15s;
    }
    .reset-input-wrap .toggle-icon:hover { color: var(--gray-500); }
    .field-error { font-size: .73rem; color: var(--danger); margin-top: 3px; display:flex; align-items:center; gap:4px; }
    .field-error svg { width:12px; height:12px; flex-shrink:0; }

    /* Password rules checklist */
    .pw-rules {
      background: var(--gray-50); border: 1px solid var(--gray-100);
      border-radius: var(--radius-md); padding: 12px 14px; margin-bottom: 16px;
    }
    .pw-rules-title { font-size:.7rem; font-weight:700; color:var(--gray-400); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
    .pw-rule { display:flex; align-items:center; gap:7px; font-size:.75rem; color:var(--gray-400); margin-bottom:4px; transition:color .2s; }
    .pw-rule.met { color: var(--success); }
    .pw-rule svg { width:12px; height:12px; flex-shrink:0; }
    .pw-rule .check-icon { display:none; }
    .pw-rule .cross-icon { display:block; }
    .pw-rule.met .check-icon { display:block; }
    .pw-rule.met .cross-icon { display:none; }

    .reset-submit {
      width: 100%; height: 46px; background: var(--navy); color: white;
      border: none; border-radius: var(--radius-md); font-size: .9rem; font-weight: 700;
      font-family: var(--font-body); cursor: pointer; transition: background .15s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .reset-submit:hover { background: var(--navy-light); }
    .reset-submit svg { width: 16px; height: 16px; }
    .reset-footer {
      padding: 12px 36px 18px; text-align:center;
      border-top: 1px solid var(--gray-100); background: var(--gray-50);
    }
    .reset-footer-text { font-size:.72rem; color:var(--gray-300); font-family:var(--font-mono); }
  </style>
</head>
<body>

<div class="reset-card">

  <div class="reset-header">
    <div class="reset-header-top">
      <div class="reset-header-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
      </div>
      <div class="reset-title">Password Reset Required</div>
    </div>
    <div class="reset-subtitle">
      You are using a temporary password. Set a new secure password before continuing.
    </div>
  </div>

  <div class="reset-body">

    @if(session('info'))
      <div style="background:var(--info-bg);border:1px solid var(--info-border);border-left:4px solid var(--info);border-radius:var(--radius-md);padding:10px 14px;margin-bottom:16px;font-size:.8rem;color:#155e75;">
        {{ session('info') }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.force-reset.update') }}">
      @csrf

      {{-- Current (temp) password --}}
      <div class="reset-form-group">
        <label class="reset-label">Current (Temporary) Password</label>
        <div class="reset-input-wrap">
          <input type="password" name="current_password" id="cur-pw"
                 class="reset-input {{ $errors->has('current_password') ? 'is-error' : '' }}"
                 placeholder="Enter the temporary password" autocomplete="current-password">
          <svg class="toggle-icon" onclick="toggleField('cur-pw', this)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        @error('current_password')
          <div class="field-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
            {{ $message }}
          </div>
        @enderror
      </div>

      {{-- New password --}}
      <div class="reset-form-group">
        <label class="reset-label">New Password</label>
        <div class="reset-input-wrap">
          <input type="password" name="password" id="new-pw"
                 class="reset-input {{ $errors->has('password') ? 'is-error' : '' }}"
                 placeholder="8–64 characters" autocomplete="new-password"
                 oninput="checkRules(this.value)">
          <svg class="toggle-icon" onclick="toggleField('new-pw', this)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        @error('password')
          <div class="field-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
            {{ $message }}
          </div>
        @enderror
      </div>

      {{-- Password rules --}}
      <div class="pw-rules">
        <div class="pw-rules-title">Password Requirements</div>
        <div class="pw-rule" id="rule-length">
          <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          8 to 64 characters
        </div>
        <div class="pw-rule" id="rule-upper">
          <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          At least one uppercase letter (A–Z)
        </div>
        <div class="pw-rule" id="rule-lower">
          <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          At least one lowercase letter (a–z)
        </div>
        <div class="pw-rule" id="rule-number">
          <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          At least one number (0–9)
        </div>
        <div class="pw-rule" id="rule-special">
          <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          At least one special character (@, #, $, %, ^, &amp;, !, ?, _)
        </div>
        <div class="pw-rule" id="rule-nospace">
          <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          No spaces, backslashes, or forward slashes
        </div>
      </div>

      {{-- Confirm password --}}
      <div class="reset-form-group">
        <label class="reset-label">Confirm New Password</label>
        <div class="reset-input-wrap">
          <input type="password" name="password_confirmation" id="conf-pw"
                 class="reset-input {{ $errors->has('password_confirmation') ? 'is-error' : '' }}"
                 placeholder="Re-enter new password" autocomplete="new-password">
          <svg class="toggle-icon" onclick="toggleField('conf-pw', this)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
      </div>

      <button type="submit" class="reset-submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
        Set New Password &amp; Continue
      </button>

    </form>
  </div>

  <div class="reset-footer">
    <div class="reset-footer-text">
      Logged in as: <strong>{{ auth()->user()->username }}</strong> ·
      Role: {{ auth()->user()->role_label }}
    </div>
  </div>

</div>

<script>
  function toggleField(id, icon) {
    const f = document.getElementById(id);
    f.type = f.type === 'password' ? 'text' : 'password';
    icon.style.color = f.type === 'text' ? 'var(--accent-blue)' : '';
  }

  function checkRules(val) {
    const set = (id, met) => document.getElementById(id).classList.toggle('met', met);
    set('rule-length',  val.length >= 8 && val.length <= 64);
    set('rule-upper',   /[A-Z]/.test(val));
    set('rule-lower',   /[a-z]/.test(val));
    set('rule-number',  /[0-9]/.test(val));
    set('rule-special', /[@#$%^&!?_*]/.test(val));
    set('rule-nospace', !/[\s\\/]/.test(val) && val.length > 0);
  }
</script>

</body>
</html>
