{{-- resources/views/auth/verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Verify OTP — EncryptEd</title>
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
    .otp-card {
      width: 100%;
      max-width: 420px;
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0,0,0,.4);
      animation: fadeUp .35s ease both;
      position: relative;
      z-index: 1;
    }
    .otp-card::before {
      content: '';
      display: block;
      height: 4px;
      background: linear-gradient(90deg, var(--navy) 0%, var(--accent-blue) 60%, var(--accent-sky) 100%);
    }
    .otp-header {
      padding: 28px 36px 20px;
      border-bottom: 1px solid var(--gray-100);
      background: var(--gray-50);
      text-align: center;
    }
    .otp-header-icon {
      width: 52px; height: 52px;
      background: #eff6ff;
      border: 2px solid #bfdbfe;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 14px;
    }
    .otp-header-icon svg { width: 24px; height: 24px; color: var(--accent-blue); }
    .otp-title { font-size: 1.1rem; font-weight: 700; color: var(--navy); }
    .otp-subtitle { font-size: .78rem; color: var(--gray-400); margin-top: 4px; line-height: 1.5; }
    .otp-body { padding: 28px 36px 32px; }

    /* 6 individual digit boxes */
    .otp-inputs {
      display: flex;
      gap: 8px;
      justify-content: center;
      margin-bottom: 24px;
    }
    .otp-digit {
      width: 48px; height: 58px;
      border: 2px solid var(--gray-200);
      border-radius: var(--radius-md);
      font-size: 1.5rem;
      font-weight: 700;
      font-family: var(--font-mono);
      color: var(--navy);
      text-align: center;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
      background: white;
    }
    .otp-digit:focus {
      border-color: var(--accent-blue);
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .otp-digit.is-error { border-color: var(--danger); }

    .field-error {
      font-size: .73rem;
      color: var(--danger);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
    }
    .field-error svg { width: 12px; height: 12px; }

    .otp-timer {
      text-align: center;
      font-size: .75rem;
      font-family: var(--font-mono);
      color: var(--gray-400);
      margin-bottom: 20px;
    }
    .otp-timer #countdown { color: var(--warning); font-weight: 700; }
    .otp-timer #countdown.urgent { color: var(--danger); animation: blink 1s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }

    .otp-submit {
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
      margin-bottom: 14px;
    }
    .otp-submit:hover { background: var(--navy-light); }
    .otp-submit svg { width: 16px; height: 16px; }

    .otp-resend {
      text-align: center;
      font-size: .78rem;
      color: var(--gray-400);
    }
    .otp-resend a { color: var(--accent-blue); font-weight: 600; }
    .otp-resend a:hover { color: #1d4ed8; }

    .otp-footer {
      padding: 12px 36px 16px;
      border-top: 1px solid var(--gray-100);
      background: var(--gray-50);
      text-align: center;
      font-size: .72rem;
      color: var(--gray-300);
      font-family: var(--font-mono);
    }

    /* status message */
    .status-msg {
      background: var(--success-bg);
      border: 1px solid var(--success-border);
      border-left: 4px solid var(--success);
      border-radius: var(--radius-md);
      padding: 10px 14px;
      margin-bottom: 18px;
      font-size: .8rem;
      color: #166534;
    }
  </style>
</head>
<body>

<div class="otp-card">

  <div class="otp-header">
    <div class="otp-header-icon">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
      </svg>
    </div>
    <div class="otp-title">Enter Your OTP</div>
    <div class="otp-subtitle">
      A 6-digit code was sent to your email.<br>
      Enter it below to continue.
    </div>
  </div>

  <div class="otp-body">

    @if(session('status'))
      <div class="status-msg">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.verify-otp.submit') }}" id="otp-form">
      @csrf

      {{-- Hidden input that gets filled with the combined OTP --}}
      <input type="hidden" name="otp" id="otp-combined">

      {{-- 6 individual digit boxes --}}
      <div class="otp-inputs">
        @for($i = 1; $i <= 6; $i++)
          <input type="text"
                 inputmode="numeric"
                 maxlength="1"
                 pattern="[0-9]"
                 class="otp-digit {{ $errors->has('otp') ? 'is-error' : '' }}"
                 id="digit-{{ $i }}"
                 autocomplete="off">
        @endfor
      </div>

      @error('otp')
        <div class="field-error">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
          </svg>
          {{ $message }}
        </div>
      @enderror

      {{-- Countdown timer --}}
      <div class="otp-timer">
        Code expires in <span id="countdown">10:00</span>
      </div>

      <button type="submit" class="otp-submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Verify OTP
      </button>

    </form>

    <div class="otp-resend">
      Didn't receive it?
      <a href="{{ route('password.request') }}">Send a new OTP</a>
    </div>

  </div>

  <div class="otp-footer">
    OTP is valid for 10 minutes &nbsp;·&nbsp; Max 3 attempts &nbsp;·&nbsp; RA 10173 Compliant
  </div>

</div>

<script>
  // ── Auto-advance between digit boxes ───────────────────────────────────
  const digits = document.querySelectorAll('.otp-digit');

  digits.forEach((input, index) => {
    input.addEventListener('input', (e) => {
      // Allow only numbers
      input.value = input.value.replace(/[^0-9]/g, '');
      if (input.value && index < digits.length - 1) {
        digits[index + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && index > 0) {
        digits[index - 1].focus();
      }
    });

    // Handle paste — spread digits across boxes
    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData)
        .getData('text').replace(/[^0-9]/g, '').slice(0, 6);
      pasted.split('').forEach((char, i) => {
        if (digits[i]) digits[i].value = char;
      });
      const next = Math.min(pasted.length, digits.length - 1);
      digits[next].focus();
    });
  });

  // Auto-focus first digit on load
  digits[0].focus();

  // ── Combine digits into hidden input on submit ─────────────────────────
  document.getElementById('otp-form').addEventListener('submit', function (e) {
    const combined = Array.from(digits).map(d => d.value).join('');
    document.getElementById('otp-combined').value = combined;

    if (combined.length !== 6) {
      e.preventDefault();
      alert('Please enter all 6 digits.');
    }
  });

  // ── Countdown timer (10 minutes) ───────────────────────────────────────
  let totalSeconds = 10 * 60;
  const countdownEl = document.getElementById('countdown');

  const timer = setInterval(() => {
    totalSeconds--;
    if (totalSeconds <= 0) {
      clearInterval(timer);
      countdownEl.textContent = 'EXPIRED';
      countdownEl.classList.add('urgent');
      return;
    }
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    countdownEl.textContent = `${m}:${s.toString().padStart(2, '0')}`;
    if (totalSeconds <= 60) countdownEl.classList.add('urgent');
  }, 1000);
</script>

</body>
</html>
