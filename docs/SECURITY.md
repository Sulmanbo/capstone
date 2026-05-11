# EncryptEd Security Posture

This document summarizes the security controls implemented in EncryptEd and how they map to the Functional Requirements Specification and RA 10173 (Data Privacy Act of 2012).

## Credential Storage

- **Passwords:** bcrypt with cost factor 12, unique salts handled by Laravel's `Hash` facade. Plain-text storage is prohibited.
- **Email lookup hash:** SHA-256 of the lowercased email is stored in `users.email_hash` so unique-lookup queries work without decryption.
- **No SHA-256 of passwords** anywhere in the system.

## Encryption at Rest (AES-256 via Laravel `Crypt`)

| Table | Encrypted columns |
|---|---|
| `users` | `email`, `phone`, `address`, `parent_name`, `parent_contact` |
| `applicants` | `date_of_birth`, `parent_guardian_name`, `parent_contact`, `parent_email`, `barangay`, `municipality`, `province`, `address` |

Plain-text columns (searchable, RBAC-protected): `first_name`, `last_name`, `username`, `lrn`, `employee_number`. LRN is kept plain to support registrar `LIKE` search; confidentiality is enforced via Role-Based Access Control.

## Authentication & Brute Force

- 30-minute idle session timeout (`config/session.php`).
- Account lockout: 5 consecutive failed attempts → 10-minute lock.
- Per-IP rate limit: `throttle:10,1` on `POST /login`.
- Password recovery rate limits: `throttle:5,1` on OTP send and reset, `throttle:10,1` on OTP verify.
- Login error messages are uniform regardless of whether the username exists, is locked, or is deactivated — prevents account enumeration. Timing equalization is performed by running a dummy `Hash::check` when the username is unknown.

## Session Security

- Cookies use `HttpOnly` and `SameSite=Lax` flags.
- `SESSION_SECURE_COOKIE` is set to `true` in production (`.env`); leave `false` only in local HTTP development.
- Session ID is regenerated on successful login.

## CSRF, XSS, Headers

- All state-changing `web` routes are CSRF-protected via Laravel's `VerifyCsrfToken`.
- Blade templates auto-escape output. Raw output (`{!! !!}`) is reviewed manually.
- `SecurityHeaders` middleware applies on every response:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()`
  - `Content-Security-Policy` with `frame-ancestors`, `form-action`, `object-src 'none'`, `base-uri 'self'`
  - `Strict-Transport-Security` is sent only over HTTPS.

## Injection Defense

- Primary defense: Eloquent parameter binding (all queries use the query builder or Eloquent).
- Secondary defense: `InjectionDefenseMiddleware` scans request input for SQL/XSS patterns. Configurable via `INJECTION_DEFENSE_MODE`:
  - `block`: matching requests get HTTP 403 (production default)
  - `monitor`: log only, allow request through (dev/test default)

## Audit Log Integrity

- All sensitive operations are logged via `AuditLog::record()`.
- Each row is chained with SHA-256: `row_hash = sha256(prev_hash || action_type || user_id || payload || source_ip || created_at)`.
- The `php artisan audit:verify` command walks the chain and detects tampering. Run it weekly or on demand.
- Audit logs older than 365 days are pruned by `php artisan audit:prune`, scheduled weekly on Sundays at 02:00. Security-critical events (`LOGIN_FAILED`, `ACCOUNT_LOCKED`, `INJECTION_BLOCKED`, etc.) are preserved indefinitely.

## Report Card Authenticity

- Every generated PDF embeds a unique SHA-256 token plus a QR code linking to a public verification endpoint.
- The verification endpoint recomputes the grade-data hash and compares it to the stored hash, surfacing any post-generation tampering. Mismatches raise a `report_card_tamper_detected` threat event.
- All downloads log `REPORT_CARD_GENERATED`; all verifications log `REPORT_CARD_VERIFIED`.

## Threat Monitoring

`ThreatEvent` rows surface in the admin Threat Monitoring page. Current detection coverage:

- `brute_force` (account lock after 5 fails)
- `login_attempt_on_locked_account`
- `injection_attempt`
- `report_card_tamper_detected`
- `rate_limit_exceeded` *(future — see roadmap)*

## RA 10173 (Data Privacy Act) Alignment

- **Data Minimization:** the database schema is restricted to data points required for academic management.
- **Right to Erasure:** the system supports cryptographic shredding via account deactivation + key rotation.
- **Consent:** the public admission form captures explicit Data Privacy Act consent before submission.
- **Audit Trail:** all access to and modification of personal data is logged for 1 year minimum (security-critical events indefinitely).

## Known Limitations / Roadmap

- CSP currently allows `'unsafe-inline'` for scripts and styles because Blade templates contain inline blocks. Migration to nonce-based CSP is scoped out of capstone delivery.
- HSTS is gated on HTTPS — local XAMPP development runs on plain HTTP.
- Database backups: a manual `mysqldump` workflow is documented in the README; automated `spatie/laravel-backup` integration is roadmapped.
