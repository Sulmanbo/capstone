{{-- resources/views/admin/security/locked-accounts.blade.php --}}
@extends('layouts.app')

@section('title', 'Locked Accounts')
@section('breadcrumb', 'Locked Accounts')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Locked Accounts</h1>
      <p class="enc-page__subtitle">View and manage accounts locked due to security policies</p>
    </div>
  </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
  <div class="enc-alert enc-alert--info" style="margin-bottom:20px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="enc-alert__body">
      <div>{!! session('success') !!}</div>
    </div>
  </div>
@endif

@if(session('error'))
  <div class="enc-alert enc-alert--danger" style="margin-bottom:20px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <div class="enc-alert__body">
      <div>{!! session('error') !!}</div>
    </div>
  </div>
@endif

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['total_locked'] }}</div>
      <div class="enc-stat-label">Total Locked</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['locked_students'] }}</div>
      <div class="enc-stat-label">Locked Students</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['locked_faculty'] }}</div>
      <div class="enc-stat-label">Locked Faculty</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['locked_registrars'] }}</div>
      <div class="enc-stat-label">Locked Registrars</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--purple">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['locked_admins'] }}</div>
      <div class="enc-stat-label">Locked Admins</div>
    </div>
  </div>
</div>

{{-- Locked Accounts Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
      All Locked Accounts
    </div>
    <span class="enc-card__meta">{{ $locked_accounts->total() }} total records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.locked-accounts.index') }}">
    <div class="enc-filter-bar">
      <div class="enc-filter-group">
        <span class="enc-filter-label">Search</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ $search }}"
                 placeholder="Name, Username, Email..."
                 class="enc-input enc-input--search" style="width:220px;">
        </div>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Role</span>
        <select name="role" class="enc-select">
          <option value="">All Roles</option>
          <option value="01" {{ $role === '01' ? 'selected' : '' }}>Student</option>
          <option value="02" {{ $role === '02' ? 'selected' : '' }}>Faculty</option>
          <option value="03" {{ $role === '03' ? 'selected' : '' }}>Registrar</option>
          <option value="04" {{ $role === '04' ? 'selected' : '' }}>Admin</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Filter</button>
      @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.locked-accounts.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
      @endif
    </div>
  </form>

  {{-- Table --}}
  <div class="enc-card__body enc-card__body--no-pad">
    <div class="enc-table-wrap">
      <table class="enc-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Locked Since</th>
            <th>Failed Attempts</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($locked_accounts as $account)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $account->first_name }} {{ $account->last_name }}
                </div>
              </td>

              {{-- Username --}}
              <td class="mono">{{ $account->username }}</td>

              {{-- Email --}}
              <td style="font-size:.75rem;">
                <span style="color:var(--gray-600);">{{ substr($account->email, 0, 25) }}...</span>
              </td>

              {{-- Role --}}
              <td>
                @php
                  $roleClass = match($account->role_id) {
                    '04' => 'danger', '03' => 'warning',
                    '02' => 'info',   '01' => 'neutral',
                    default => 'neutral'
                  };
                  $roleLabel = match($account->role_id) {
                    '01' => 'Student', '02' => 'Faculty',
                    '03' => 'Registrar', '04' => 'Admin',
                    default => 'Unknown'
                  };
                @endphp
                <span class="enc-badge enc-badge--{{ $roleClass }}">{{ $roleLabel }}</span>
              </td>

              {{-- Locked Since --}}
              <td class="mono" style="font-size:.72rem;">
                @if($account->locked_until)
                  <span style="color:var(--danger);">{{ $account->locked_until->format('m/d/Y H:i') }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Failed Attempts --}}
              <td style="text-align:center;">
                <span class="enc-badge enc-badge--danger" style="display:inline-block;">{{ $account->failed_attempts }}</span>
              </td>

              {{-- Actions --}}
              <td>
                <form method="POST" action="{{ route('admin.locked-accounts.unlock', $account) }}"
                      onsubmit="return confirm('Unlock account for {{ $account->username }}?')" style="display:inline;">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="enc-btn enc-btn--outline enc-btn--sm" title="Unlock Account">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V9a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9v9.75A2.25 2.25 0 005.25 21z"/>
                    </svg>
                    Unlock
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:30px;color:var(--gray-200);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;margin:0 auto 10px;opacity:0.3;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No locked accounts found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  @if($locked_accounts->hasPages())
    <div class="enc-card__footer" style="text-align:center;">
      {{ $locked_accounts->links() }}
    </div>
  @endif
</div>

<style>
  .enc-stat-icon--purple {
    background-color: rgba(168, 85, 247, 0.1);
    color: #a855f7;
  }
</style>

@endsection
