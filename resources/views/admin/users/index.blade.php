{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'User Management')
@section('breadcrumb', 'User Management')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">User Management</h1>
      <p class="enc-page__subtitle">Create, manage, and monitor all platform accounts</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.users.create') }}" class="enc-btn enc-btn--primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
        Create Account
      </a>
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

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  <a href="{{ route('admin.users.index', ['role' => '01']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('role_id','01')->count() }}</div>
      <div class="enc-stat-label">Students</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['role' => '02']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('role_id','02')->count() }}</div>
      <div class="enc-stat-label">Faculty</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['role' => '03']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('role_id','03')->count() }}</div>
      <div class="enc-stat-label">Registrars</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['status' => 'locked']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('status','locked')->count() }}</div>
      <div class="enc-stat-label">Locked</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['status' => 'deactivated']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('status','deactivated')->count() }}</div>
      <div class="enc-stat-label">Deactivated</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['gender' => 'male']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('gender','male')->count() }}</div>
      <div class="enc-stat-label">Male</div>
    </div>
  </a>
  <a href="{{ route('admin.users.index', ['gender' => 'female']) }}" class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--pink">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A9.75 9.75 0 106.25 20.25M12 9v10.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ \App\Models\User::where('gender','female')->count() }}</div>
      <div class="enc-stat-label">Female</div>
    </div>
  </a>
</div>

{{-- Users Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
      All Accounts
    </div>
    <span class="enc-card__meta">{{ $users->total() }} total records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.users.index') }}">
    <div class="enc-filter-bar">
      <div class="enc-filter-group">
        <span class="enc-filter-label">Search</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ request('search') }}"
                 placeholder="Username, LRN, Employee No..."
                 class="enc-input enc-input--search" style="width:220px;">
        </div>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Role</span>
        <select name="role" class="enc-select">
          <option value="">All Roles</option>
          <option value="01" {{ request('role')=='01' ? 'selected' : '' }}>Student (R01)</option>
          <option value="02" {{ request('role')=='02' ? 'selected' : '' }}>Faculty (R02)</option>
          <option value="03" {{ request('role')=='03' ? 'selected' : '' }}>Registrar (R03)</option>
          <option value="04" {{ request('role')=='04' ? 'selected' : '' }}>Admin (R04)</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Status</span>
        <select name="status" class="enc-select">
          <option value="">All Status</option>
          <option value="active"      {{ request('status')=='active'      ? 'selected' : '' }}>Active</option>
          <option value="deactivated" {{ request('status')=='deactivated' ? 'selected' : '' }}>Deactivated</option>
          <option value="locked"      {{ request('status')=='locked'      ? 'selected' : '' }}>Locked</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Filter</button>
      @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
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
            <th>Identifier</th>
            <th>Gender</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $user->first_name }} {{ $user->last_name }}
                </div>
                <div style="font-size:.7rem;color:var(--gray-400);">{{ $user->email }}</div>
              </td>

              {{-- Username --}}
              <td class="mono">{{ $user->username }}</td>

              {{-- LRN or Employee No --}}
              <td class="mono" style="font-size:.75rem;">
                @if($user->lrn)
                  <span style="color:var(--info);">LRN: {{ $user->lrn }}</span>
                @elseif($user->employee_number)
                  <span style="color:var(--accent-blue);">EMP: {{ $user->employee_number }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Gender --}}
              <td>
                @if($user->gender)
                  <span class="enc-badge enc-badge--{{ $user->gender === 'male' ? 'info' : 'warning' }}">
                    {{ ucfirst($user->gender) }}
                  </span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Role --}}
              <td>
                @php
                  $roleClass = match($user->role_id) {
                    '04' => 'danger', '03' => 'warning',
                    '02' => 'info',   '01' => 'neutral',
                    default => 'neutral'
                  };
                @endphp
                <span class="enc-badge enc-badge--{{ $roleClass }}">
                  {{ $user->role_label }}
                </span>
              </td>

              {{-- Status --}}
              <td>
                @if($user->status === 'active')
                  <span class="enc-badge enc-badge--success">Active</span>
                @elseif($user->status === 'locked')
                  <span class="enc-badge enc-badge--danger">Locked</span>
                @else
                  <span class="enc-badge enc-badge--neutral">Deactivated</span>
                @endif
                @if($user->password_reset_required)
                  <span class="enc-badge enc-badge--warning" style="margin-left:4px;font-size:.6rem;">Reset Req.</span>
                @endif
              </td>

              {{-- Last Login --}}
              <td class="mono" style="font-size:.72rem;">
                {{ $user->last_login_at ? $user->last_login_at->format('m/d/Y H:i') : 'Never' }}
              </td>

              {{-- Actions --}}
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <a href="{{ route('admin.users.edit', $user) }}"
                     class="enc-btn enc-btn--outline enc-btn--sm" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Edit
                  </a>

                  <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
                        onsubmit="return confirm('Reset password for {{ $user->username }}?')">
                    @csrf
                    <button type="submit" class="enc-btn enc-btn--ghost enc-btn--sm" title="Reset Password">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                      </svg>
                      Reset PW
                    </button>
                  </form>

                  <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                        onsubmit="return confirm('Change status for {{ $user->username }}?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="enc-btn enc-btn--sm {{ $user->status === 'active' ? 'enc-btn--danger' : 'enc-btn--outline' }}"
                            title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                      @if($user->status === 'active')
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Deactivate
                      @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activate
                      @endif
                    </button>
                  </form>

                  {{-- Permanent Delete --}}
                  @if(auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('PERMANENTLY DELETE {{ $user->username }}?\n\nThis cannot be undone and will be logged in the audit trail.')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="enc-btn enc-btn--ghost enc-btn--sm"
                              title="Delete permanently"
                              style="color:var(--danger);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                        Delete
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8">
                <div class="enc-empty" style="padding:40px;">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;opacity:.4;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                  </svg>
                  <div class="enc-empty__title">No accounts found</div>
                  <div class="enc-empty__sub">Try adjusting your filters or create a new account</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="enc-pagination">
      <div class="enc-pagination__info">
        Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} accounts
      </div>
      <div class="enc-pagination__pages">
        @if($users->onFirstPage())
          <button class="enc-page-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
          </button>
        @else
          <a href="{{ $users->previousPageUrl() }}" class="enc-page-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
          </a>
        @endif
        @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
          <a href="{{ $url }}" class="enc-page-btn {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        @if($users->hasMorePages())
          <a href="{{ $users->nextPageUrl() }}" class="enc-page-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          </a>
        @else
          <button class="enc-page-btn" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          </button>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection
