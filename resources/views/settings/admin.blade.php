@extends('layouts.app')
@section('title', 'Admin Settings')
@section('breadcrumb', 'Settings')

@push('head')
@include('settings._styles')
@endpush

@section('content')
<div class="st-page">

  <div class="st-page__header">
    <div>
      <h1 class="st-page__title">Settings</h1>
      <p class="st-page__sub">Manage system configuration, security, and your account.</p>
    </div>
  </div>

  <div class="st-layout">

    {{-- Left nav --}}
    <nav class="st-sidenav">
      <a href="#general"  class="st-sidenav__item" onclick="stSwitch('general',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        System
      </a>
      <a href="#security" class="st-sidenav__item" onclick="stSwitch('security',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        Security
      </a>
      <a href="#roles"    class="st-sidenav__item" onclick="stSwitch('roles',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        Role Definitions
      </a>
      <a href="#audit"    class="st-sidenav__item" onclick="stSwitch('audit',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        Audit Logs
      </a>
      <a href="#password" class="st-sidenav__item" onclick="stSwitch('password',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Change Password
      </a>
    </nav>

    {{-- Main content --}}
    <div class="st-content">

      {{-- ── System Configuration ──────────────────────── --}}
      <div id="tab-general" class="st-tab">
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">System Configuration</div>
            <div class="st-card__desc">View and manage the current academic year and system status.</div>
          </div>
          <div class="st-card__body">
            <div class="st-info-grid">
              <div class="st-info-item">
                <span class="st-info-label">Platform</span>
                <span class="st-info-val">EncryptEd — Phil. Academy of Sakya</span>
              </div>
              <div class="st-info-item">
                <span class="st-info-label">Your Role</span>
                <span class="st-info-val">{{ $user->role_label }}</span>
              </div>
              <div class="st-info-item">
                <span class="st-info-label">Employee ID</span>
                <span class="st-info-val">{{ $user->employee_number ?? '—' }}</span>
              </div>
              <div class="st-info-item">
                <span class="st-info-label">Last Login</span>
                <span class="st-info-val">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : '—' }}</span>
              </div>
            </div>
            <div class="st-divider"></div>
            <p class="st-muted">To manage academic years and grading quarters, visit the
              <a href="{{ route('admin.academic-years.index') }}" style="color:#4f46e5;text-decoration:none;font-weight:600;">Academic Years</a> section.
            </p>
          </div>
        </div>
      </div>

      {{-- ── Security ──────────────────────────────────── --}}
      <div id="tab-security" class="st-tab" style="display:none;">
        @if(session('success_security'))
          <div class="st-alert st-alert--success">{{ session('success_security') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Security Limits</div>
            <div class="st-card__desc">Enforce authentication policies and session controls.</div>
          </div>
          <form method="POST" action="{{ route('admin.settings.security') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Two-Factor Authentication</div>
                  <div class="st-toggle-desc">Require a second step when administrators log in. <span class="st-badge">Coming Soon</span></div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="two_factor_enabled" value="1" {{ $user->pref('two_factor_enabled') ? 'checked' : '' }} disabled>
                  <span class="sw__track"></span>
                  <span class="sw__thumb"></span>
                </label>
              </div>

              <div class="st-field" style="margin-top:20px;">
                <label class="st-label">Session Timeout</label>
                <div style="display:flex;align-items:center;gap:12px;">
                  <select name="session_timeout" class="st-select" style="width:180px;">
                    @foreach([15=>'15 minutes',30=>'30 minutes',60=>'1 hour',120=>'2 hours',240=>'4 hours'] as $val => $label)
                      <option value="{{ $val }}" {{ $user->pref('session_timeout', 60) == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                  </select>
                  <span class="st-muted">After this idle period, the system will require re-login.</span>
                </div>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Security Settings</button>
            </div>
          </form>
        </div>
      </div>

      {{-- ── Role Definitions ──────────────────────────── --}}
      <div id="tab-roles" class="st-tab" style="display:none;">
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Role Definitions</div>
            <div class="st-card__desc">System-defined roles and their access scope. Managed at the system level.</div>
          </div>
          <div class="st-card__body" style="padding:0;">
            <table class="st-table">
              <thead>
                <tr>
                  <th>Role ID</th><th>Role</th><th>Access Scope</th><th>Can Manage</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><span class="st-code">04</span></td>
                  <td><strong>Admin</strong></td>
                  <td>Full system access</td>
                  <td>All users, settings, audit, curriculum</td>
                </tr>
                <tr>
                  <td><span class="st-code">03</span></td>
                  <td><strong>Registrar</strong></td>
                  <td>Academic records</td>
                  <td>Enrollment, student records, documents</td>
                </tr>
                <tr>
                  <td><span class="st-code">02</span></td>
                  <td><strong>Faculty</strong></td>
                  <td>Teaching & grading</td>
                  <td>Class schedules, grades, attendance</td>
                </tr>
                <tr>
                  <td><span class="st-code">01</span></td>
                  <td><strong>Student</strong></td>
                  <td>Self-service portal</td>
                  <td>Own profile, grades (read-only), schedule</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- ── Audit Logs ────────────────────────────────── --}}
      <div id="tab-audit" class="st-tab" style="display:none;">
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Audit Logs</div>
            <div class="st-card__desc">Track all system changes and authentication events.</div>
          </div>
          <div class="st-card__body">
            <p class="st-muted" style="margin-bottom:16px;">The full audit log is available in the dedicated Audit Log module, which provides filtering, search, and export features.</p>
            <a href="{{ route('admin.audit.index') }}" class="st-btn">
              Open Audit Log
            </a>
            <a href="{{ route('admin.compliance.index') }}" class="st-btn-outline" style="margin-left:10px;">
              Compliance Reports
            </a>
          </div>
        </div>
      </div>

      {{-- ── Change Password ───────────────────────────── --}}
      <div id="tab-password" class="st-tab" style="display:none;">
        @if(session('success_password'))
          <div class="st-alert st-alert--success">{{ session('success_password') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Change Password</div>
            <div class="st-card__desc">Use a strong password of at least 8 characters.</div>
          </div>
          <form method="POST" action="{{ route('admin.settings.password') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-field">
                <label class="st-label">Current Password</label>
                <input type="password" name="current_password" class="st-input {{ $errors->has('current_password') ? 'st-input--error' : '' }}" autocomplete="current-password">
                @error('current_password')<div class="st-error">{{ $message }}</div>@enderror
              </div>
              <div class="st-row">
                <div class="st-field">
                  <label class="st-label">New Password</label>
                  <input type="password" name="new_password" class="st-input {{ $errors->has('new_password') ? 'st-input--error' : '' }}" autocomplete="new-password">
                  @error('new_password')<div class="st-error">{{ $message }}</div>@enderror
                </div>
                <div class="st-field">
                  <label class="st-label">Confirm New Password</label>
                  <input type="password" name="new_password_confirmation" class="st-input" autocomplete="new-password">
                </div>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Update Password</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function stSwitch(id, el) {
  document.querySelectorAll('.st-tab').forEach(t => t.style.display = 'none');
  document.querySelectorAll('.st-sidenav__item').forEach(a => a.classList.remove('active'));
  document.getElementById('tab-' + id).style.display = 'block';
  if (el) el.classList.add('active');
}
// Determine initial tab from session or default
const initTab = '{{ session("tab", "general") }}';
const firstEl = document.querySelector('[onclick*="' + initTab + '"]');
stSwitch(initTab, firstEl);
// Re-open correct tab if there are validation errors on password section
@if($errors->any()) stSwitch('password', document.querySelector('[onclick*="password"]')); @endif
@if(session('success_security')) stSwitch('security', document.querySelector('[onclick*="security"]')); @endif
@if(session('success_password')) stSwitch('password', document.querySelector('[onclick*="password"]')); @endif
</script>
@endpush
