@extends('layouts.app')
@section('title', 'Student Settings')
@section('breadcrumb', 'Settings')

@push('head')
@include('settings._styles')
@endpush

@section('content')
<div class="st-page">

  <div class="st-page__header">
    <div>
      <h1 class="st-page__title">Settings</h1>
      <p class="st-page__sub">Manage your profile, security, and notification preferences.</p>
    </div>
  </div>

  <div class="st-layout">

    <nav class="st-sidenav">
      <a href="#profile"       class="st-sidenav__item" onclick="stSwitch('profile',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        Profile Info
      </a>
      <a href="#emergency"     class="st-sidenav__item" onclick="stSwitch('emergency',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
        Emergency Contact
      </a>
      <a href="#notifications" class="st-sidenav__item" onclick="stSwitch('notifications',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
        Notifications
      </a>
      <a href="#display"       class="st-sidenav__item" onclick="stSwitch('display',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/></svg>
        Display Options
      </a>
      <a href="#password"      class="st-sidenav__item" onclick="stSwitch('password',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Account Security
      </a>
    </nav>

    <div class="st-content">

      {{-- Profile Info --}}
      <div id="tab-profile" class="st-tab">
        @if(session('success_profile'))
          <div class="st-alert st-alert--success">{{ session('success_profile') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Profile Information</div>
            <div class="st-card__desc">Update your home address and contact number.</div>
          </div>
          <form method="POST" action="{{ route('student.settings.profile') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-row">
                <div class="st-field">
                  <label class="st-label">Full Name</label>
                  <input type="text" class="st-input" value="{{ $user->full_name }}" disabled>
                  <div class="st-hint">Name changes must be requested through the Registrar.</div>
                </div>
                <div class="st-field">
                  <label class="st-label">LRN</label>
                  <input type="text" class="st-input" value="{{ $user->lrn ?? '—' }}" disabled>
                </div>
              </div>
              <div class="st-row">
                <div class="st-field">
                  <label class="st-label">Phone Number</label>
                  <input type="text" name="phone" class="st-input" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 09xxxxxxxxx">
                </div>
                <div class="st-field">
                  <label class="st-label">Gender</label>
                  <input type="text" class="st-input" value="{{ ucfirst($user->gender ?? '—') }}" disabled>
                </div>
              </div>
              <div class="st-field">
                <label class="st-label">Home Address</label>
                <textarea name="address" class="st-input st-textarea" rows="3" placeholder="Street, Barangay, City, Province">{{ old('address', $user->address) }}</textarea>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Profile</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Emergency Contact --}}
      <div id="tab-emergency" class="st-tab" style="display:none;">
        @if(session('success_emergency'))
          <div class="st-alert st-alert--success">{{ session('success_emergency') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Emergency Contact</div>
            <div class="st-card__desc">Provide a person to contact in case of an emergency at school.</div>
          </div>
          <form method="POST" action="{{ route('student.settings.emergency') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-row">
                <div class="st-field">
                  <label class="st-label">Contact Name</label>
                  <input type="text" name="emergency_name" class="st-input" value="{{ old('emergency_name', $user->pref('emergency_contact_name')) }}" placeholder="Full name">
                </div>
                <div class="st-field">
                  <label class="st-label">Relationship</label>
                  <select name="emergency_relationship" class="st-select">
                    @foreach(['Parent','Guardian','Sibling','Spouse','Relative','Other'] as $rel)
                      <option value="{{ $rel }}" {{ $user->pref('emergency_contact_relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="st-field">
                <label class="st-label">Contact Phone</label>
                <input type="text" name="emergency_phone" class="st-input" value="{{ old('emergency_phone', $user->pref('emergency_contact_phone')) }}" placeholder="09xxxxxxxxx">
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Contact</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Notifications --}}
      <div id="tab-notifications" class="st-tab" style="display:none;">
        @if(session('success_prefs'))
          <div class="st-alert st-alert--success">{{ session('success_prefs') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Notification Preferences</div>
            <div class="st-card__desc">Choose how you want to be notified about enrollment and academic updates.</div>
          </div>
          <form method="POST" action="{{ route('student.settings.preferences') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Email Notifications</div>
                  <div class="st-toggle-desc">Receive updates on enrollment status, grades, and announcements via email.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="email_notifications" value="1" {{ $user->pref('email_notifications', true) ? 'checked' : '' }}>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">SMS Notifications <span class="st-badge">Coming Soon</span></div>
                  <div class="st-toggle-desc">Get text message alerts for urgent enrollment or grade updates.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="sms_notifications" value="1" {{ $user->pref('sms_notifications') ? 'checked' : '' }} disabled>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Preferences</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Display Options --}}
      <div id="tab-display" class="st-tab" style="display:none;">
        @if(session('success_prefs'))
          <div class="st-alert st-alert--success">{{ session('success_prefs') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Display Options</div>
            <div class="st-card__desc">Customize how the portal looks and behaves for you.</div>
          </div>
          <form method="POST" action="{{ route('student.settings.preferences') }}">
            @csrf
            <input type="hidden" name="email_notifications" value="{{ $user->pref('email_notifications', true) ? '1' : '0' }}">
            <input type="hidden" name="sms_notifications"   value="{{ $user->pref('sms_notifications') ? '1' : '0' }}">
            <div class="st-card__body">
              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Dark Mode <span class="st-badge">Coming Soon</span></div>
                  <div class="st-toggle-desc">Switch the portal to a darker color scheme.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="dark_mode" value="1" {{ $user->pref('dark_mode') ? 'checked' : '' }} disabled>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Display Options</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Account Security --}}
      <div id="tab-password" class="st-tab" style="display:none;">
        @if(session('success_password'))
          <div class="st-alert st-alert--success">{{ session('success_password') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Account Security</div>
            <div class="st-card__desc">Change your password. Use at least 8 characters.</div>
          </div>
          <form method="POST" action="{{ route('student.settings.password') }}">
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
const initTab = '{{ session("tab", "profile") }}';
stSwitch(initTab, document.querySelector('[onclick*="' + initTab + '"]'));
@if($errors->any()) stSwitch('password', document.querySelector('[onclick*="password"]')); @endif
@if(session('success_password')) stSwitch('password', document.querySelector('[onclick*="password"]')); @endif
@if(session('success_emergency')) stSwitch('emergency', document.querySelector('[onclick*="emergency"]')); @endif
@if(session('success_prefs')) stSwitch('notifications', document.querySelector('[onclick*="notifications"]')); @endif
</script>
@endpush
