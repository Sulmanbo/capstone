@extends('layouts.app')
@section('title', 'Registrar Settings')
@section('breadcrumb', 'Settings')

@push('head')
@include('settings._styles')
@endpush

@section('content')
<div class="st-page">

  <div class="st-page__header">
    <div>
      <h1 class="st-page__title">Settings</h1>
      <p class="st-page__sub">Configure enrollment workflows, document handling, and export preferences.</p>
    </div>
  </div>

  <div class="st-layout">

    <nav class="st-sidenav">
      <a href="#workflow" class="st-sidenav__item" onclick="stSwitch('workflow',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"/></svg>
        Enrollment & Workflow
      </a>
      <a href="#documents" class="st-sidenav__item" onclick="stSwitch('documents',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        Document Approvals
      </a>
      <a href="#exports"   class="st-sidenav__item" onclick="stSwitch('exports',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        Export Defaults
      </a>
      <a href="#password"  class="st-sidenav__item" onclick="stSwitch('password',this)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        Account Security
      </a>
    </nav>

    <div class="st-content">

      {{-- Enrollment & Workflow --}}
      <div id="tab-workflow" class="st-tab">
        @if(session('success_workflow'))
          <div class="st-alert st-alert--success">{{ session('success_workflow') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Enrollment & Workflow</div>
            <div class="st-card__desc">Configure enrollment notifications and processing preferences.</div>
          </div>
          <form method="POST" action="{{ route('registrar.settings.workflow') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-field">
                <label class="st-label">Notification Method</label>
                <div class="st-radio-group">
                  @foreach(['email' => 'Email only', 'sms' => 'SMS only', 'both' => 'Email & SMS'] as $val => $label)
                    <label class="st-radio">
                      <input type="radio" name="notification_method" value="{{ $val }}" {{ $user->pref('notification_method', 'email') === $val ? 'checked' : '' }}>
                      <span class="st-radio__box"></span>
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
              </div>

              <div class="st-field" style="max-width:240px;">
                <label class="st-label">Enrollment Reminder (days before close)</label>
                <input type="number" name="enrollment_reminder" class="st-input" value="{{ $user->pref('enrollment_reminder', 7) }}" min="1" max="30">
                <div class="st-hint">Send a reminder this many days before the enrollment window closes.</div>
              </div>

              <div class="st-divider"></div>

              <div class="st-field">
                <label class="st-label">Enrollment Windows</label>
                <p class="st-muted">Open and close dates for enrollment periods are set in the
                  <a href="{{ route('admin.academic-years.index') }}" style="color:#4f46e5;text-decoration:none;font-weight:600;">Academic Years</a> module.
                </p>
              </div>

              <div class="st-toggle-row">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Capacity Override</div>
                  <div class="st-toggle-desc">Allow enrollment beyond the class capacity limit when needed.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="capacity_override" value="1" {{ $user->pref('capacity_override') ? 'checked' : '' }}>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Workflow Settings</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Document Approvals --}}
      <div id="tab-documents" class="st-tab" style="display:none;">
        @if(session('success_workflow'))
          <div class="st-alert st-alert--success">{{ session('success_workflow') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Document Approvals</div>
            <div class="st-card__desc">Define how submitted student requirements are routed for review.</div>
          </div>
          <form method="POST" action="{{ route('registrar.settings.workflow') }}">
            @csrf
            <input type="hidden" name="notification_method" value="{{ $user->pref('notification_method', 'email') }}">
            <input type="hidden" name="enrollment_reminder" value="{{ $user->pref('enrollment_reminder', 7) }}">
            <input type="hidden" name="capacity_override"   value="{{ $user->pref('capacity_override') ? '1' : '0' }}">
            <div class="st-card__body">
              <div class="st-field">
                <label class="st-label">Document Routing</label>
                <div class="st-radio-group">
                  <label class="st-radio">
                    <input type="radio" name="document_routing" value="sequential" {{ $user->pref('document_routing', 'sequential') === 'sequential' ? 'checked' : '' }}>
                    <span class="st-radio__box"></span>
                    <div>
                      <strong>Sequential</strong>
                      <div class="st-toggle-desc">Each approver reviews after the previous one finishes.</div>
                    </div>
                  </label>
                  <label class="st-radio" style="margin-top:10px;">
                    <input type="radio" name="document_routing" value="parallel" {{ $user->pref('document_routing', 'sequential') === 'parallel' ? 'checked' : '' }}>
                    <span class="st-radio__box"></span>
                    <div>
                      <strong>Parallel</strong>
                      <div class="st-toggle-desc">All approvers receive the document at the same time.</div>
                    </div>
                  </label>
                </div>
              </div>
              <div class="st-divider"></div>
              <p class="st-muted">Document requirement types and checklist items are managed through the student profile module.</p>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Document Settings</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Export Defaults --}}
      <div id="tab-exports" class="st-tab" style="display:none;">
        @if(session('success_export'))
          <div class="st-alert st-alert--success">{{ session('success_export') }}</div>
        @endif
        <div class="st-card">
          <div class="st-card__head">
            <div class="st-card__title">Export Defaults</div>
            <div class="st-card__desc">Choose the default format and structure for roster and report exports.</div>
          </div>
          <form method="POST" action="{{ route('registrar.settings.export') }}">
            @csrf
            <div class="st-card__body">
              <div class="st-field">
                <label class="st-label">Default Export Format</label>
                <div class="st-radio-group">
                  @foreach(['xlsx' => 'Excel (.xlsx)', 'csv' => 'CSV (.csv)', 'pdf' => 'PDF (.pdf)'] as $val => $label)
                    <label class="st-radio">
                      <input type="radio" name="export_format" value="{{ $val }}" {{ $user->pref('export_format', 'xlsx') === $val ? 'checked' : '' }}>
                      <span class="st-radio__box"></span>
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
              </div>

              <div class="st-field" style="margin-top:4px;">
                <label class="st-label">CSV Delimiter</label>
                <select name="export_delimiter" class="st-select" style="width:200px;">
                  <option value="comma"     {{ $user->pref('export_delimiter', 'comma') === 'comma'     ? 'selected' : '' }}>Comma (,)</option>
                  <option value="semicolon" {{ $user->pref('export_delimiter', 'comma') === 'semicolon' ? 'selected' : '' }}>Semicolon (;)</option>
                  <option value="tab"       {{ $user->pref('export_delimiter', 'comma') === 'tab'       ? 'selected' : '' }}>Tab</option>
                </select>
                <div class="st-hint">Applies to CSV exports only.</div>
              </div>

              <div class="st-toggle-row" style="margin-top:8px;">
                <div class="st-toggle-info">
                  <div class="st-toggle-label">Include Column Headers</div>
                  <div class="st-toggle-desc">Add a header row as the first line of every export file.</div>
                </div>
                <label class="sw">
                  <input type="checkbox" name="include_headers" value="1" {{ $user->pref('include_headers', true) ? 'checked' : '' }}>
                  <span class="sw__track"></span><span class="sw__thumb"></span>
                </label>
              </div>
            </div>
            <div class="st-card__foot">
              <button type="submit" class="st-btn">Save Export Defaults</button>
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
          <form method="POST" action="{{ route('registrar.settings.password') }}">
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
stSwitch('workflow', document.querySelector('[onclick*="workflow"]'));
@if($errors->any())           stSwitch('password',  document.querySelector('[onclick*="password"]'));  @endif
@if(session('success_password')) stSwitch('password',  document.querySelector('[onclick*="password"]'));  @endif
@if(session('success_export'))   stSwitch('exports',   document.querySelector('[onclick*="exports"]'));   @endif
@if(session('success_workflow'))  stSwitch('workflow',  document.querySelector('[onclick*="workflow"]'));  @endif
</script>
@endpush
