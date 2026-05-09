{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login — EncryptEd</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    /* ── Login Page Overrides ───────────────────── */
    body {
      background: var(--navy-dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    /* Subtle grid background pattern */
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

    .login-card {
      width: 100%;
      max-width: 420px;
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0,0,0,.4), 0 8px 20px rgba(0,0,0,.3);
      animation: fadeUp .35s ease both;
      position: relative;
      z-index: 1;
    }

    /* Top accent bar */
    .login-card::before {
      content: '';
      display: block;
      height: 4px;
      background: linear-gradient(90deg, var(--navy) 0%, var(--accent-blue) 60%, var(--accent-sky) 100%);
    }

    .login-header {
      padding: 32px 36px 24px;
      text-align: center;
      border-bottom: 1px solid var(--gray-100);
      background: var(--gray-50);
    }

    .login-logos {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 20px;
    }

    .login-logo-enc {
      height: 44px;
      width: auto;
      /* Black bg logo — use drop-shadow + darken blend to show on light bg */
      filter: invert(1) brightness(.15);
    }

    .login-logo-divider {
      width: 1px;
      height: 36px;
      background: var(--gray-200);
    }

    .login-logo-school {
      height: 48px;
      width: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--gray-200);
      /* School seal also has black bg */
      filter: invert(1) brightness(.85) saturate(1.2);
    }

    .login-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--navy);
      letter-spacing: -.02em;
    }

    .login-subtitle {
      font-size: .78rem;
      color: var(--gray-400);
      margin-top: 3px;
      font-family: var(--font-mono);
    }

    .login-body {
      padding: 28px 36px 32px;
    }

    .login-form-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-bottom: 16px;
    }

    .login-label {
      font-size: .72rem;
      font-weight: 700;
      color: var(--gray-500);
      text-transform: uppercase;
      letter-spacing: .07em;
    }

    .login-input {
      width: 100%;
      height: 44px;
      padding: 0 14px;
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius-md);
      font-size: .875rem;
      color: var(--gray-700);
      font-family: var(--font-body);
      background: white;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
    }

    .login-input:focus {
      border-color: var(--accent-blue);
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }

    .login-input.is-error {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(220,38,38,.08);
    }

    .login-input-wrap {
      position: relative;
    }

    .login-input-wrap svg {
      position: absolute;
      right: 13px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px; height: 16px;
      color: var(--gray-300);
      cursor: pointer;
      transition: color .15s;
    }

    .login-input-wrap svg:hover { color: var(--gray-500); }

    .login-input--padded { padding-right: 40px; }

    .login-error {
      font-size: .75rem;
      color: var(--danger);
      margin-top: 4px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .login-error svg { width: 12px; height: 12px; flex-shrink: 0; }

    .login-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      font-size: .78rem;
    }

    .login-remember {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--gray-500);
      cursor: pointer;
    }

    .login-remember input[type="checkbox"] {
      width: 14px; height: 14px;
      accent-color: var(--accent-blue);
      cursor: pointer;
    }

    .login-forgot {
      color: var(--accent-blue);
      font-weight: 600;
      transition: color .15s;
    }

    .login-forgot:hover { color: #1d4ed8; }

    .login-submit {
      width: 100%;
      height: 46px;
      background: var(--navy);
      color: white;
      border: none;
      border-radius: var(--radius-md);
      font-size: .9rem;
      font-weight: 700;
      font-family: var(--font-body);
      cursor: pointer;
      transition: background .15s, transform .1s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      letter-spacing: .01em;
    }

    .login-submit:hover  { background: var(--navy-light); }
    .login-submit:active { transform: scale(.99); }

    .login-submit svg { width: 16px; height: 16px; }

    .login-footer {
      padding: 14px 36px 20px;
      text-align: center;
      border-top: 1px solid var(--gray-100);
      background: var(--gray-50);
    }

    .login-footer-text {
      font-size: .72rem;
      color: var(--gray-300);
      font-family: var(--font-mono);
      line-height: 1.5;
    }

    .login-security-badges {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 8px;
    }

    .login-sec-badge {
      font-size: .62rem;
      font-family: var(--font-mono);
      font-weight: 600;
      padding: 2px 7px;
      border-radius: 3px;
      letter-spacing: .04em;
    }

    .login-sec-badge--sha { background: #eff6ff; color: var(--accent-blue); }
    .login-sec-badge--aes { background: var(--success-bg); color: var(--success); }
    .login-sec-badge--ra  { background: var(--warning-bg); color: var(--warning); }

    /* Alert for lockout / general errors */
    .login-alert {
      background: var(--danger-bg);
      border: 1px solid var(--danger-border);
      border-left: 4px solid var(--danger);
      border-radius: var(--radius-md);
      padding: 10px 14px;
      margin-bottom: 18px;
      font-size: .8rem;
      color: #991b1b;
      display: flex;
      gap: 8px;
      align-items: flex-start;
    }

    .login-alert svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }
  </style>
</head>
<body>

<div class="login-card">

  {{-- Header with logos --}}
  <div class="login-header">
    <div class="login-logos">
      <img src="{{ asset('images/EncryptEd.png') }}"
           alt="EncryptEd"
           class="login-logo-enc">
      <div class="login-logo-divider"></div>
      <img src="{{ asset('images/logo.png') }}"
           alt="Phil. Academy of Sakya"
           class="login-logo-school">
    </div>
    <div class="login-title">Academic Management Portal</div>
    <div class="login-subtitle">Philippine Academy of Sakya</div>
  </div>

  {{-- Login Form --}}
  <div class="login-body">

    {{-- Session / Lockout error --}}
    @if(session('error'))
      <div class="login-alert">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" autocomplete="off">
      @csrf

      {{-- Username --}}
      <div class="login-form-group">
        <label class="login-label" for="username">
          Username / LRN / Employee No.
        </label>
        <input
          type="text"
          id="username"
          name="username"
          value="{{ old('username') }}"
          class="login-input {{ $errors->has('username') ? 'is-error' : '' }}"
          placeholder="Enter your ID or username"
          autocomplete="username"
          autofocus>
        @error('username')
          <div class="login-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
            </svg>
            {{ $message }}
          </div>
        @enderror
      </div>

      {{-- Password --}}
      <div class="login-form-group">
        <label class="login-label" for="password">Password</label>
        <div class="login-input-wrap">
          <input
            type="password"
            id="password"
            name="password"
            class="login-input login-input--padded {{ $errors->has('password') ? 'is-error' : '' }}"
            placeholder="Enter your password"
            autocomplete="current-password"
            id="password-field">
          {{-- Show/hide toggle --}}
          <svg id="toggle-pw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" onclick="togglePassword()">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        @error('password')
          <div class="login-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
            </svg>
            {{ $message }}
          </div>
        @enderror
      </div>

      {{-- Remember + Forgot --}}
      <div class="login-options">
        <label class="login-remember">
          <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
        <a href="{{ route('password.request') }}" class="login-forgot">Forgot password?</a>
      </div>

      {{-- Submit --}}
      <button type="submit" class="login-submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Sign In
      </button>

    </form>
  </div>

  {{-- Footer --}}
  <div class="login-footer">
    <div class="login-security-badges">
    </div>
    <div class="login-footer-text" style="margin-top:8px;">
    
    </div>
  </div>

</div>

<script>
  function togglePassword() {
    const field = document.getElementById('password');
    const icon  = document.getElementById('toggle-pw');
    if (field.type === 'password') {
      field.type = 'text';
      icon.style.color = 'var(--accent-blue)';
    } else {
      field.type = 'password';
      icon.style.color = '';
    }
  }
</script>

</body>
</html>
