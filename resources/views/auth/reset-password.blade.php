{{-- resources/views/auth/reset-password.blade.php --}}
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
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
    }
    body::before {
      content: '';
      position: fixed; inset: 0;
      background-image:
        linear-gradient(rgba(37,99,235,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,.04) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
    }
    .card {
      width: 100%; max-width: 440px;
      background: white; border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0,0,0,.4);
      animation: fadeUp .35s ease both;
      position: relative; z-index: 1;
    }
    .card::before {
      content: ''; display: block; height: 4px;
      background: linear-gradient(90deg, var(--accent-green) 0%, var(--accent-blue) 100%);
    }
    .card-header {
      padding: 28px 36px 20px;
      border-bottom: 1px solid var(--gray-100);
      background: var(--success-bg);
    }
    .header-top { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; }
    .header-icon {
      width: 40px; height: 40px;
      background: white; border: 1.5px solid var(--success-border);
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
    }
    .header-icon svg { width: 20px; height: 20px; color: var(--success); }
    .header-title { font-size: 1.05rem; font-weight: 700; color: var(--navy); }
    .header-sub { font-size: .78rem; color: var(--gray-500); }
    .card-body { padding: 24px 36px 32px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
    .label { font-size: .72rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase; letter-spacing: .07em; }
    .input-wrap { position: relative; }
    .input {
      width: 100%; height: 44px; padding: 0 40px 0 14px;
      border: 1.5px solid var(--gray-200); border-radius: var(--radius-md);
      font-size: .875rem; color: var(--gray-700); font-family: var(--font-body);
      background: white; outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .input:focus { border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .input.is-error { border-color: var(--danger); }
    .toggle-icon {
      position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
      width: 16px; height: 16px; color: var(--gray-300); cursor: pointer; transition: color .15s;
    }
    .toggle-icon:hover { color: var(--gray-500); }
    .field-error { font-size: .73rem; color: var(--danger); margin-top: 3px; }
    .pw-rules {
      background: var(--gray-50); border: 1px solid var(--gray-100);
      border-radius: var(--radius-md); padding: 12px 14px; margin-bottom: 16px;
    }
    .pw-rules-title { font-size:.7rem; font-weight:700; color:var(--gray-400); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
    .pw-rule { display:flex; align-items:center; gap:7px; font-size:.75rem; color:var(--gray-400); margin-bottom:4px; transition:color .2s; }
    .pw-rule.met { color: var(--success); }
    .pw-rule svg { width:12px; height:12px; flex-shrink:0; }
    .pw-rule .check { display:none; } .pw-rule .cross { display:block; }
    .pw-rule.met .check { display:block; } .pw-rule.met .cross { display:none; }
    .submit {
      width: 100%; height: 46px; background: var(--navy); color: white;
      border: none; border-radius: var(--radius-md); font-size: .9rem; font-weight: 700;
      font-family: var(--font-body); cursor: pointer; transition: background .15s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .submit:hover { background: var(--navy-light); }
    .submit svg { width: 16px; height: 16px; }
    .card-footer {
      padding: 12px 36px 16px; border-top: 1px solid var(--gray-100);
      background: var(--gray-50); text-align: center;
      font-size: .72rem; color: var(--gray-300); font-family: var(--font-mono);
    }
  </style>
</head>
<body>

<div class="card">

  <div class="card-header">
    <div class="header-top">
      <div class="header-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div class="header-title">OTP Verified</div>
    </div>
    <div class="header-sub">Set your new password to complete account recovery.</div>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('password.do-reset') }}">
      @csrf

      <div class="form-group">
        <label class="label">New Password</label>
        <div class="input-wrap">
          <input type="password" name="password" id="new-pw"
                 class="input {{ $errors->has('password') ? 'is-error' : '' }}"
                 placeholder="8-64 characters" autocomplete="new-password"
                 oninput="checkRules(this.value)">
          <svg class="toggle-icon" onclick="toggleField('new-pw',this)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        @error('password')
          <div class="field-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="pw-rules">
        <div class="pw-rules-title">Requirements</div>
        <div class="pw-rule" id="r-len">
          <svg class="check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          8 to 64 characters
        </div>
        <div class="pw-rule" id="r-up">
          <svg class="check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          One uppercase letter
        </div>
        <div class="pw-rule" id="r-lo">
          <svg class="check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          One lowercase letter
        </div>
        <div class="pw-rule" id="r-num">
          <svg class="check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          One number
        </div>
        <div class="pw-rule" id="r-sp">
          <svg class="check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          <svg class="cross" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          One special character (@#$%^&amp;!?_)
        </div>
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label class="label">Confirm New Password</label>
        <div class="input-wrap">
          <input type="password" name="password_confirmation" id="conf-pw"
                 class="input" placeholder="Re-enter password" autocomplete="new-password">
          <svg class="toggle-icon" onclick="toggleField('conf-pw',this)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
      </div>

      <button type="submit" class="submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
        Set New Password
      </button>
    </form>
  </div>

  <div class="card-footer">SHA-256 &nbsp;·&nbsp; AES-256 &nbsp;·&nbsp; bcrypt &nbsp;·&nbsp; RA 10173</div>
</div>

<script>
  function toggleField(id, icon) {
    const f = document.getElementById(id);
    f.type = f.type === 'password' ? 'text' : 'password';
    icon.style.color = f.type === 'text' ? 'var(--accent-blue)' : '';
  }
  function checkRules(v) {
    const s = (id, ok) => document.getElementById(id).classList.toggle('met', ok);
    s('r-len', v.length >= 8 && v.length <= 64);
    s('r-up',  /[A-Z]/.test(v));
    s('r-lo',  /[a-z]/.test(v));
    s('r-num', /[0-9]/.test(v));
    s('r-sp',  /[@#$%^&!?_*]/.test(v));
  }
</script>

</body>
</html>
