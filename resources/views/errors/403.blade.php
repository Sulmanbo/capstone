{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 Forbidden — EncryptEd</title>
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    body {
      background: var(--navy-dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      font-family: var(--font-body);
    }
    .err-card {
      text-align: center;
      max-width: 420px;
      animation: fadeUp .3s ease both;
    }
    .err-code {
      font-size: 6rem;
      font-weight: 700;
      color: var(--danger);
      font-family: var(--font-mono);
      line-height: 1;
      letter-spacing: -.04em;
    }
    .err-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: white;
      margin: 12px 0 8px;
    }
    .err-msg {
      font-size: .85rem;
      color: rgba(255,255,255,.45);
      line-height: 1.6;
      margin-bottom: 28px;
    }
    .err-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(220,38,38,.15);
      border: 1px solid rgba(220,38,38,.3);
      color: #fca5a5;
      font-family: var(--font-mono);
      font-size: .72rem;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 28px;
    }
    .err-badge::before {
      content: '';
      width: 6px; height: 6px;
      background: var(--danger);
      border-radius: 50%;
    }
  </style>
</head>
<body>
  <div class="err-card">
    <div class="err-code">403</div>
    <div class="err-title">Access Denied</div>
    <div class="err-badge">PRIVILEGE_VIOLATION · LOGGED</div>
    <div class="err-msg">
      {{ $message ?? 'You do not have permission to access this resource.' }}
      This incident has been recorded in the audit trail.
    </div>
    <a href="{{ url()->previous() }}" class="enc-btn enc-btn--outline"
       style="display:inline-flex;background:transparent;color:rgba(255,255,255,.6);border-color:rgba(255,255,255,.15);">
      ← Go Back
    </a>
  </div>
</body>
</html>
