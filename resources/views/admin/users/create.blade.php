@extends('layouts.app')

@section('title', 'Create Account')
@section('breadcrumb', 'Create Account')

@push('head')
<style>
.create-wrap { max-width: 600px; }

.create-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 1px 8px rgba(15,23,42,.05);
}

.create-card__head {
  padding: 22px 28px 18px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 10px;
}
.create-card__head svg { width: 18px; height: 18px; color: #6366f1; flex-shrink: 0; }
.create-card__head h2 { font-size: .95rem; font-weight: 700; color: #0f172a; margin: 0; }

.create-card__body { padding: 28px; }

.cf-group { margin-bottom: 20px; }
.cf-label {
  display: block;
  font-size: .76rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
  letter-spacing: .02em;
  text-transform: uppercase;
}
.cf-required { color: #ef4444; }
.cf-hint { font-size: .75rem; color: #94a3b8; font-weight: 400; margin-top: 5px; text-transform: none; letter-spacing: 0; }

.cf-input, .cf-select {
  width: 100%;
  padding: .6rem .85rem;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: .875rem;
  color: #0f172a;
  background: #fff;
  transition: border-color .15s, box-shadow .15s;
  box-sizing: border-box;
  height: 42px;
}
.cf-input::placeholder { color: #c4cdd6; }
.cf-input:focus, .cf-select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.cf-input.is-error, .cf-select.is-error { border-color: #ef4444; }
.cf-error { font-size: .74rem; color: #ef4444; margin-top: 4px; }

.cf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.cf-email-display {
  height: 42px;
  padding: .6rem .85rem;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
  background: #f8fafc;
  font-size: .875rem;
  color: #94a3b8;
  font-style: italic;
  display: flex;
  align-items: center;
  gap: 6px;
}
.cf-email-preview { color: #475569; font-style: normal; font-weight: 500; }

.cf-divider { height: 1px; background: #f1f5f9; margin: 24px 0; }

.cf-auto-note {
  display: flex;
  gap: 10px;
  padding: 14px 16px;
  background: #fafafa;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
  margin-bottom: 20px;
}
.cf-auto-note svg { width: 15px; height: 15px; color: #94a3b8; flex-shrink: 0; margin-top: 1px; }
.cf-auto-note p { font-size: .78rem; color: #64748b; margin: 0; line-height: 1.55; }
.cf-auto-note strong { color: #374151; font-weight: 600; }

.cf-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  padding-top: 4px;
}
.cf-btn-cancel {
  padding: .55rem 1.1rem;
  border: 1px solid #e2e8f0;
  border-radius: 9px;
  background: #fff;
  color: #475569;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: border-color .15s, background .15s;
}
.cf-btn-cancel:hover { border-color: #cbd5e1; background: #f8fafc; }
.cf-btn-submit {
  padding: .55rem 1.25rem;
  border: none;
  border-radius: 9px;
  background: #1e293b;
  color: #fff;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  transition: background .15s;
}
.cf-btn-submit svg { width: 15px; height: 15px; }
.cf-btn-submit:hover { background: #0f172a; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Create User Account</h1>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.users.index') }}" class="enc-btn enc-btn--outline">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Users
      </a>
    </div>
  </div>
</div>

<div class="create-wrap">
  <div class="create-card">

    <div class="create-card__head">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
      </svg>
      <h2>Account Information</h2>
    </div>

    <div class="create-card__body">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        {{-- Role --}}
        <div class="cf-group">
          <label class="cf-label" for="role_id">Role <span class="cf-required">*</span></label>
          <select name="role_id" id="role_id" class="cf-select {{ $errors->has('role_id') ? 'is-error' : '' }}">
            <option value="">— Select Role —</option>
            <option value="01" {{ old('role_id')=='01' ? 'selected' : '' }}>Student</option>
            <option value="02" {{ old('role_id')=='02' ? 'selected' : '' }}>Faculty</option>
            <option value="03" {{ old('role_id')=='03' ? 'selected' : '' }}>Registrar</option>
            <option value="04" {{ old('role_id')=='04' ? 'selected' : '' }}>Admin</option>
          </select>
          @error('role_id')
            <div class="cf-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="cf-divider"></div>

        {{-- Name row --}}
        <div class="cf-row">
          <div class="cf-group">
            <label class="cf-label" for="first_name">First Name <span class="cf-required">*</span></label>
            <input type="text" id="first_name" name="first_name"
                   value="{{ old('first_name') }}"
                   class="cf-input {{ $errors->has('first_name') ? 'is-error' : '' }}"
                   placeholder="e.g. Juan" autocomplete="off">
            @error('first_name')
              <div class="cf-error">{{ $message }}</div>
            @enderror
          </div>
          <div class="cf-group">
            <label class="cf-label" for="last_name">Last Name <span class="cf-required">*</span></label>
            <input type="text" id="last_name" name="last_name"
                   value="{{ old('last_name') }}"
                   class="cf-input {{ $errors->has('last_name') ? 'is-error' : '' }}"
                   placeholder="e.g. dela Cruz" autocomplete="off">
            @error('last_name')
              <div class="cf-error">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Email --}}
        <div class="cf-group">
          <label class="cf-label">Institutional Email</label>
          <div class="cf-email-display">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;color:#c4cdd6;flex-shrink:0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
            Auto-generated &nbsp;·&nbsp; <span class="cf-email-preview">[initials]@pas.edu.ph</span>
          </div>
        </div>

        {{-- Gender --}}
        <div class="cf-group">
          <label class="cf-label" for="gender">Gender <span class="cf-required">*</span></label>
          <select name="gender" id="gender" class="cf-select {{ $errors->has('gender') ? 'is-error' : '' }}">
            <option value="">— Select Gender —</option>
            <option value="male"   {{ old('gender')=='male'   ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
          </select>
          @error('gender')
            <div class="cf-error">{{ $message }}</div>
          @enderror
        </div>

        <div class="cf-divider"></div>

        {{-- Auto-generated summary note --}}
        <div class="cf-auto-note">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
          </svg>
          <p>The system will auto-generate a <strong>9-digit ID</strong>, <strong>institutional email</strong>, and a <strong>temporary password</strong>. The user must change their password on first login.</p>
        </div>

        <div class="cf-actions">
          <a href="{{ route('admin.users.index') }}" class="cf-btn-cancel">Cancel</a>
          <button type="submit" class="cf-btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Create Account
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

@endsection
