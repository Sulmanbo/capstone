{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forgot Password — EncryptEd</title>
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

    .fp-card {
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

    .fp-card::before {
      content: '';
      display: block;
      height: 4px;
      background: linear-gradient(90deg, var(--navy) 0%, var(--accent-blue) 60%, var(--accent-sky) 100%);
    }

    .fp-header {
      padding: 28px 36px 20px;
      border-bottom: 1px solid var(--gray-100);
      background: var(--gray-50);
    }

    .fp-header-top {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 8px;
    }

    .fp-header-icon {
      width: 40px; height: 40px;
      background: #eff6ff;
      border: 1.5px solid #bfdbfe;
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .fp-header-icon svg { width: 20px; height: 20px; color: var(--accent-blue); }

    .fp-logos {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .fp-logo-enc {
      height: 36px;
      width: auto;
      filter: invert(1) brightness(.15);
    }

    .fp-logo-divider {
      width: 1px; height: 28px;
      background: var(--gray-200);
    }

    .fp-logo-school {
      height: 36px; width: 36px;
      border-radius: 50%;
      object-fit: cover;
      border: 1.5px solid var(--gray-200);
      filter: invert(1) brightness(.85) saturate(1.2);
    }

    .fp-title {
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--navy);
      letter-spacing: -.01em;
    }

    .fp-subtitle {
      font-size: .78rem;
      color: var(--gray-400);
      line-height: 1.55;
      margin-top: 2px;
    }

    .fp-body { padding: 24px 36px 28px; }

    .fp-form-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-bottom: 20px;
    }

    .fp-label {
      font-size: .72rem;
      font-weight: 700;
      color: var(--gray-500);
      text-transform: uppercase;
      letter-spacing: .07em;
    }

    .fp-input {
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

    .fp-input:focus {
      border-color: var(--accent-blue);
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }

    .fp-input.is-error {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(220,38,38,.08);
    }

    .field-error {
      font-size: .73rem;
      color: var(--danger);
      margin-top: 3px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .field-error svg { width: 12px; height: 12px; flex-shrink: 0; }

    .fp-submit {
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
      transition: background .15s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .fp-submit:hover { background: var(--navy-light); }
    .fp-submit svg { width: 16px; height: 16px; }

    .fp-back {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      margin-top: 16px;
      font-size: .8rem;
      color: var(--gray-400);
      transition: color .15s;
    }

    .fp-back:hover { color: var(--accent-blue); }
    .fp-back svg { width: 14px; height: 14px; }

    .fp-footer {
      padding: 12px 36px 18px;
      text-align: center;
      border-top: 1px solid var(--gray-100);
      background: var(--gray-50);
    }

    .fp-footer-text {
      font-size: .72rem;
      color: var(--gray-300);
      font-family: var(--font-mono);
    }

    /* Success state */
    .fp-success {
      background: var(--success-bg);
      border: 1px solid var(--success-border);
      border-left: 4px solid var(--success);
      border-radius: var(--radius-md);
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: .82rem;
      color: #166534;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .fp-success svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; color: var(--success); }
  </style>
</head>
<body>

<div class="fp-card">

  {{-- Header --}}
  <div class="fp-header">
    <div class="fp-logos">
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="fp-logo-enc">
      <div class="fp-logo-divider"></div>
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="fp-logo-school">
    </div>
    <div class="fp-header-top">
      <div class="fp-header-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
        </svg>
      </div>
      <div class="fp-title">Forgot Password</div>
    </div>
    <div class="fp-subtitle">
      Enter your institutional email address and we'll send you a
      password reset link if an account exists.
    </div>
  </div>

  {{-- Body --}}
  <div class="fp-body">

    {{-- Success message after submission --}}
    @if(session('status'))
      <div class="fp-success">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
          <strong style="display:block;margin-bottom:2px;">Reset link sent</strong>
          {{ session('status') }}
        </div>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="fp-form-group">
        <label class="fp-label" for="email">
          Institutional Email Address
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="{{ old('email') }}"
          class="fp-input {{ $errors->has('email') ? 'is-error' : '' }}"
          placeholder="yourname@institution.edu.ph"
          autocomplete="email"
          autofocus>
        @error('email')
          <div class="field-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
            </svg>
            {{ $message }}
          </div>
        @enderror
      </div>

      <button type="submit" class="fp-submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
        </svg>
        Send Reset Link
      </button>

      <a href="{{ route('login') }}" class="fp-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Login
      </a>

    </form>
  </div>

  {{-- Footer --}}
  <div class="fp-footer">
    <div class="fp-footer-text">
      Reset links expire after 60 minutes · RA 10173 Compliant
    </div>
  </div>

</div>

</body>
</html>
