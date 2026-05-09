{{-- resources/views/emails/otp.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EncryptEd — Password Reset OTP</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f1f5f9;
      color: #334155;
      padding: 40px 20px;
    }
    .wrapper {
      max-width: 520px;
      margin: 0 auto;
    }
    .card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,.08);
    }
    .card-top {
      height: 5px;
      background: linear-gradient(90deg, #0f1e3c 0%, #2563eb 60%, #3b82f6 100%);
    }
    .card-header {
      background: #0f1e3c;
      padding: 28px 36px;
      text-align: center;
    }
    .card-header-title {
      color: white;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .card-header-sub {
      color: rgba(255,255,255,.5);
      font-size: 12px;
      margin-top: 4px;
      font-family: 'Courier New', monospace;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }
    .card-body {
      padding: 36px;
    }
    .greeting {
      font-size: 16px;
      color: #0f1e3c;
      font-weight: 600;
      margin-bottom: 12px;
    }
    .intro-text {
      font-size: 14px;
      color: #64748b;
      line-height: 1.6;
      margin-bottom: 28px;
    }
    .otp-box {
      background: #f8fafc;
      border: 2px dashed #e2e8f0;
      border-radius: 12px;
      padding: 24px;
      text-align: center;
      margin-bottom: 24px;
    }
    .otp-label {
      font-size: 11px;
      font-weight: 700;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 12px;
      font-family: 'Courier New', monospace;
    }
    .otp-code {
      font-size: 44px;
      font-weight: 700;
      letter-spacing: 0.18em;
      color: #0f1e3c;
      font-family: 'Courier New', monospace;
      line-height: 1;
    }
    .otp-expiry {
      font-size: 12px;
      color: #94a3b8;
      margin-top: 10px;
      font-family: 'Courier New', monospace;
    }
    .otp-expiry strong { color: #d97706; }
    .warning-box {
      background: #fffbeb;
      border-left: 4px solid #d97706;
      border-radius: 6px;
      padding: 12px 16px;
      margin-bottom: 24px;
      font-size: 13px;
      color: #92400e;
      line-height: 1.5;
    }
    .warning-box strong { display: block; margin-bottom: 2px; }
    .divider {
      height: 1px;
      background: #f1f5f9;
      margin: 24px 0;
    }
    .footer-text {
      font-size: 12px;
      color: #94a3b8;
      line-height: 1.6;
    }
    .card-footer {
      background: #f8fafc;
      border-top: 1px solid #f1f5f9;
      padding: 16px 36px;
      text-align: center;
    }
    .badges {
      display: inline-flex;
      gap: 8px;
      margin-bottom: 8px;
    }
    .badge {
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 10px;
      font-family: 'Courier New', monospace;
      letter-spacing: 0.04em;
    }
    .badge-blue  { background: #eff6ff; color: #2563eb; }
    .badge-green { background: #f0fdf4; color: #16a34a; }
    .badge-amber { background: #fffbeb; color: #d97706; }
    .footer-copy {
      font-size: 11px;
      color: #cbd5e1;
      font-family: 'Courier New', monospace;
    }
    .school-name {
      font-size: 12px;
      color: #64748b;
      margin-bottom: 4px;
      font-weight: 600;
    }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="card-top"></div>

    <div class="card-header">
      <div class="card-header-title">EncryptEd</div>
      <div class="card-header-sub">Phil. Academy of Sakya — Secure Portal</div>
    </div>

    <div class="card-body">
      <div class="greeting">Hello, {{ $firstName }}!</div>
      <p class="intro-text">
        We received a request to reset your EncryptEd password.
        Use the one-time password (OTP) below to continue.
        If you did not request this, you can safely ignore this email —
        your account remains secure.
      </p>

      <div class="otp-box">
        <div class="otp-label">Your One-Time Password</div>
        <div class="otp-code">{{ $otp }}</div>
        <div class="otp-expiry">
          Expires in <strong>{{ $expiryMinutes }} minutes</strong>
          &nbsp;·&nbsp; {{ now()->addMinutes($expiryMinutes)->format('h:i A') }}
        </div>
      </div>

      <div class="warning-box">
        <strong>Security Notice</strong>
        This OTP is valid for <strong>{{ $expiryMinutes }} minutes</strong> and
        can only be used <strong>once</strong>. After 3 incorrect attempts it will be invalidated.
        Never share this code with anyone — EncryptEd staff will never ask for it.
      </div>

      <div class="divider"></div>

      <p class="footer-text">
        This email was sent to you because a password reset was requested for your
        EncryptEd account. This action has been logged in the system audit trail
        in compliance with the Data Privacy Act of 2012 (RA 10173).
      </p>
    </div>

    <div class="card-footer">
      <div class="badges">
        <span class="badge badge-blue">SHA-256</span>
        <span class="badge badge-green">AES-256</span>
        <span class="badge badge-amber">RA 10173</span>
      </div>
      <div class="school-name">Phil. Academy of Sakya</div>
      <div class="footer-copy">EncryptEd Academic Management Platform &copy; {{ date('Y') }}</div>
    </div>
  </div>
</div>
</body>
</html>
