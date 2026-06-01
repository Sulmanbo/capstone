<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Received — EncryptEd Academy</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
  .card { background: #fff; border-radius: 16px; box-shadow: 0 8px 40px rgba(15,23,42,.1); max-width: 560px; width: 100%; overflow: hidden; }
  .banner { background: linear-gradient(135deg, #059669, #10b981); padding: 2.5rem 2rem; text-align: center; }
  .check { width: 64px; height: 64px; background: rgba(255,255,255,.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; }
  .banner h1 { font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: .35rem; }
  .banner p  { font-size: .87rem; color: rgba(255,255,255,.85); }
  .body { padding: 2rem; }
  .ref-box { background: #f0fdf4; border: 2px dashed #86efac; border-radius: 10px; padding: 1rem 1.25rem; text-align: center; margin-bottom: 1.5rem; }
  .ref-label { font-size: .72rem; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: .07em; margin-bottom: .3rem; }
  .ref-number { font-size: 1.5rem; font-weight: 900; color: #15803d; letter-spacing: .08em; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; margin-bottom: 1.5rem; }
  .info-item { background: #f8fafc; border-radius: 8px; padding: .65rem .9rem; }
  .info-item .lbl { font-size: .68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .2rem; }
  .info-item .val { font-size: .88rem; font-weight: 700; color: #0f172a; }
  .steps { font-size: .82rem; color: #475569; line-height: 1.7; }
  .steps li { margin-bottom: .35rem; }
  .footer { text-align: center; margin-top: 1.75rem; }
  .btn { display: inline-block; padding: .65rem 1.5rem; background: #2563eb; color: #fff; border-radius: 999px; font-weight: 700; font-size: .87rem; text-decoration: none; }
  .school-badge { margin-top: 1rem; font-size: .75rem; color: #94a3b8; }
</style>
</head>
<body>
<div class="card">
  <div class="banner">
    <div class="check">✓</div>
    <h1>Application Submitted!</h1>
    <p>Thank you, {{ $applicant->first_name }}. Your application has been received.</p>
  </div>
  <div class="body">

    <div class="ref-box">
      <div class="ref-label">Your Reference Number</div>
      <div class="ref-number">{{ $applicant->reference_number }}</div>
    </div>
    <p style="font-size:.8rem;color:#64748b;text-align:center;margin-bottom:1.25rem;">
      Please keep this reference number. You will need it when following up on your application.
    </p>

    <div class="info-grid">
      <div class="info-item">
        <div class="lbl">Applicant Name</div>
        <div class="val">{{ $applicant->full_name }}</div>
      </div>
      <div class="info-item">
        <div class="lbl">Applying For</div>
        <div class="val">{{ $applicant->applying_for_grade }}</div>
      </div>
      <div class="info-item">
        <div class="lbl">Date Submitted</div>
        <div class="val">{{ $applicant->created_at->format('M d, Y') }}</div>
      </div>
      <div class="info-item">
        <div class="lbl">Status</div>
        <div class="val" style="color:#d97706;">Pending Review</div>
      </div>
    </div>

    <div class="steps">
      <strong style="font-size:.8rem;color:#374151;display:block;margin-bottom:.4rem;">What happens next?</strong>
      <ol style="padding-left:1.1rem;">
        <li>The admissions office will review your application.</li>
        <li>You may be contacted for additional documents or a scheduled interview.</li>
        <li>You will be notified of the decision via the contact number provided.</li>
      </ol>
    </div>

    <div class="footer">
      <a href="{{ route('apply') }}" class="btn">Submit Another Application</a>
      <div class="school-badge">EncryptEd Academy · Department of Education · Republic of the Philippines</div>
    </div>

  </div>
</div>
</body>
</html>
