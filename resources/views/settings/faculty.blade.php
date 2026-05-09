@extends('layouts.app')
@section('title', 'Faculty Settings')
@section('breadcrumb', 'Settings')

@push('head')
@include('settings._styles')
@endpush

@section('content')
<div class="st-page">

  <div class="st-page__header">
    <div>
      <h1 class="st-page__title">Settings</h1>
      <p class="st-page__sub">Configure your consultation hours, alerts, and contact preferences.</p>
    </div>
  </div>

  <div class="st-layout">

    <nav class="st-sidenav">
      <a href="#contact"      class="st-sidenav__item" onclick="stSwitch('contact',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
        Contact Preferences
      </a>
      <a href="#consultation" class="st-sidenav__item" onclick="stSwitch('consultation',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Consultation Hours
      </a>
      <a href="#alerts"       class="st-sidenav__item" onclick="stSwitch('alerts',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
        Alerts & Reminders
      </a>
      <a href="#password"     class="st-sidenav__item" onclick="stSwitch('password',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Account Security
      </a>
    </nav>

    <div class="st-content">

      {{-- Contact Preferences --}}
      <div id="tab-contact" class="st-tab">
        @if(session('success_contact'))
          <div class="st-alert st-alert--success">{{ session('success_contact') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Contact Preferences</div>
            <div class="st-card__desc">Set your phone number and preferred communication method for student inquiries.</div>
          </div>
          <form method="POST" action="{{ route('faculty.settings.contact') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-row">
                <div class="st-field">
                  <label class="st-label">Full Name</label>
                  <input type="text" class="st-input" value="{{ $user->full_name }}" disabled>
                </div>
                <div class="st-field">
                  <label class="st-label">Employee ID</label>
                  <input type="text" class="st-input" value="{{ $user->employee_number ?? '—' }}" disabled>
                </div>
              </div>
              <div class="st-field">
                <label class="st-label">Phone Number</label>
                <input type="text" name="phone" class="st-input" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 09xxxxxxxxx">
              </div>
              <div class="st-field">
                <label class="st-label">Preferred Contact Method</label>
                <div class="st-radio-group">
                  @foreach(['email' => 'Email only', 'phone' => 'Phone only', 'both' => 'Email & Phone'] as $val => $label)
                    <label class="st-radio">
                      <input type="radio" name="contact_method" value="{{ $val }}" {{ $user->pref('contact_method', 'email') === $val ? 'checked' : '' }}>
                      <span class="st-radio__box"></span>
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Contact Info</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Consultation Hours --}}
      <div id="tab-consultation" class="st-tab" style="display:none;">
        @if(session('success_consultation'))
          <div class="st-alert st-alert--success">{{ session('success_consultation') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Consultation Hours</div>
            <div class="st-card__desc">Set your availability for student advising sessions each day of the week.</div>
          </div>
          <form method="POST" action="{{ route('faculty.settings.consultation') }}">
            @csrf
            <div class="st-card__body">
              @php
                $days = ['monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday'];
                $saved = $user->pref('consultation_hours', []);
              @endphp
              <div class="st-consult-grid">
                @foreach($days as $key => $label)
                  @php $d = $saved[$key] ?? ['available'=>false,'start'=>'08:00','end'=>'17:00']; @endphp
                  <div class="st-consult-row">
                    <label class="sw" style="margin-right:12px;">
                      <input type="checkbox" name="days_{{ $key }}" value="1" class="consult-toggle" data-day="{{ $key }}" {{ $d['available'] ? 'checked' : '' }}>
                      <span class="sw__track"></span><span class="sw__thumb"></span>
                    </label>
                    <span class="st-consult-day">{{ $label }}</span>
                    <div class="st-consult-times" id="times_{{ $key }}" style="{{ $d['available'] ? '' : 'opacity:.35;pointer-events:none;' }}">
                      <input type="time" name="start_{{ $key }}" class="st-input st-time-input" value="{{ $d['start'] }}">
                      <span style="color:#94a3b8;font-size:.8rem;">to</span>
                      <input type="time" name="end_{{ $key }}" class="st-input st-time-input" value="{{ $d['end'] }}">
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="st-divider"></div>
              <div class="st-field" style="max-width:220px;">
                <label class="st-label">Max Students per Slot</label>
                <input type="number" name="advising_slots" class="st-input" value="{{ $user->pref('advising_slots', 5) }}" min="1" max="50">
                <div class="st-hint">How many students can book one consultation slot.</div>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Consultation Hours</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Alerts --}}
      <div id="tab-alerts" class="st-tab" style="display:none;">
        @if(session('success_alerts'))
          <div class="st-alert st-alert--success">{{ session('success_alerts') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Alerts & Reminders</div>
            <div class="st-card__desc">Configure reminders for grading deadlines and school announcements.</div>
          </div>
          <form method="POST" action="{{ route('faculty.settings.alerts') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Grading Deadline Reminders</div>
                  <div class="st-toggle-desc">Receive a reminder before grade submission deadlines close.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="grading_alerts" value="1" {{ $user->pref('grading_alerts', true) ? 'checked' : '' }}>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
              <div class="st-field" style="margin-top:16px;max-width:220px;">
                <label class="st-label">Remind me how many days before?</label>
                <select name="grading_alert_days" class="st-select">
                  @foreach([1=>'1 day',2=>'2 days',3=>'3 days',5=>'5 days',7=>'1 week'] as $val => $label)
                    <option value="{{ $val }}" {{ $user->pref('grading_alert_days', 3) == $val ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="st-divider"></div>
              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Announcement Notifications</div>
                  <div class="st-toggle-desc">Get notified when the admin posts a new faculty announcement.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="announcement_alerts" value="1" {{ $user->pref('announcement_alerts', true) ? 'checked' : '' }}>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Alert Settings</button>
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
          <form method="POST" action="{{ route('faculty.settings.password') }}">
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
stSwitch('contact', document.querySelector('[onclick*="contact"]'));
@if($errors->any()) stSwitch('password', document.querySelector('[onclick*="password"]')); @endif
@if(session('success_password'))    stSwitch('password',     document.querySelector('[onclick*="password"]'));     @endif
@if(session('success_consultation')) stSwitch('consultation', document.querySelector('[onclick*="consultation"]')); @endif
@if(session('success_alerts'))       stSwitch('alerts',       document.querySelector('[onclick*="alerts"]'));       @endif
@if(session('success_contact'))      stSwitch('contact',      document.querySelector('[onclick*="contact"]'));      @endif

// Toggle time input visibility based on consultation checkbox
document.querySelectorAll('.consult-toggle').forEach(function(cb) {
  cb.addEventListener('change', function() {
    const times = document.getElementById('times_' + this.dataset.day);
    times.style.opacity = this.checked ? '1' : '.35';
    times.style.pointerEvents = this.checked ? '' : 'none';
  });
});
</script>
@endpush
