{{-- resources/views/admin/registrars/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Registrars Management')
@section('breadcrumb', 'Registrars')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Registrars Management</h1>
      <p class="enc-page__subtitle">View and manage all active registrars with detailed information</p>
    </div>
  </div>
</div>

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--teal">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['total_registrars'] }}</div>
      <div class="enc-stat-label">Total Registrars</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--info">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292m0-5.292a4 4 0 110 5.292m0-5.292A4.353 4.353 0 005.364 9M12 4.354L5.364 9m0 0a1 1 0 11-1.414 1.414m1.414-1.414a4 4 0 015.656 5.656m-5.656-5.656a4.353 4.353 0 00-1.414 5.656M9 20.354a4 4 0 110-5.292m0 5.292a4 4 0 110-5.292m0 5.292A4.353 4.353 0 0112 20.354m0-10a4 4 0 014.243-3.999"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['male_registrars'] }}</div>
      <div class="enc-stat-label">Male Registrars</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--pink">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292m0-5.292a4 4 0 110 5.292m0-5.292A4.353 4.353 0 005.364 9M12 4.354L5.364 9m0 0a1 1 0 11-1.414 1.414m1.414-1.414a4 4 0 015.656 5.656m-5.656-5.656a4.353 4.353 0 00-1.414 5.656M9 20.354a4 4 0 110-5.292m0 5.292a4 4 0 110-5.292m0 5.292A4.353 4.353 0 0112 20.354m0-10a4 4 0 014.243-3.999"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['female_registrars'] }}</div>
      <div class="enc-stat-label">Female Registrars</div>
    </div>
  </div>
</div>

{{-- Registrars Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.228 6.253 2.092 7.582 2.092 9s4.136 2.747 9.908 2.747c5.772 0 9.908-1.254 9.908-2.747 0-1.418-4.136-2.747-9.908-2.747zm13 0c.586.3.951.893.951 1.602 0 1.813-4.176 3.358-9.322 3.358-5.146 0-9.322-1.545-9.322-3.358 0-.709.365-1.302.951-1.602m16.694 12.286l-3.582-3.582m0 0l-2.16-3.97m-2.5 12.005A11.955 11.955 0 112.487 2.1"/>
      </svg>
      All Registrars
    </div>
    <span class="enc-card__meta">{{ $registrars->total() }} total records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.registrars.index') }}">
    <div class="enc-filter-bar">
      <div class="enc-filter-group">
        <span class="enc-filter-label">Search</span>
        <div class="enc-search-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ $search }}"
                 placeholder="Name, Username, Employee No..."
                 class="enc-input enc-input--search" style="width:220px;">
        </div>
      </div>
      <div class="enc-filter-divider"></div>
      <div class="enc-filter-group">
        <span class="enc-filter-label">Gender</span>
        <select name="gender" class="enc-select">
          <option value="">All Genders</option>
          <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Male</option>
          <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Female</option>
        </select>
      </div>
      <div class="enc-filter-divider"></div>
      <button type="submit" class="enc-btn enc-btn--primary enc-btn--sm">Filter</button>
      @if(request()->hasAny(['search','gender']))
        <a href="{{ route('admin.registrars.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
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
            <th>Employee No.</th>
            <th>Username</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Registered</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registrars as $registrar)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $registrar->first_name }} {{ $registrar->last_name }}
                </div>
              </td>

              {{-- Employee No. --}}
              <td class="mono" style="font-size:.75rem;">
                @if($registrar->employee_number)
                  <span style="color:var(--info);">{{ $registrar->employee_number }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Username --}}
              <td class="mono">{{ $registrar->username }}</td>

              {{-- Gender --}}
              <td>
                @if($registrar->gender)
                  @if($registrar->gender === 'male')
                    <span class="enc-badge enc-badge--info">Male</span>
                  @else
                    <span class="enc-badge enc-badge--pink">Female</span>
                  @endif
                @else
                  <span style="color:var(--gray-200); font-size:.75rem;">Not specified</span>
                @endif
              </td>

              {{-- Email --}}
              <td style="font-size:.75rem;">
                <span style="color:var(--gray-600);">{{ substr($registrar->email, 0, 20) }}...</span>
              </td>

              {{-- Registered Date --}}
              <td class="mono" style="font-size:.72rem;">
                {{ $registrar->created_at->format('m/d/Y') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center;padding:30px;color:var(--gray-200);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;margin:0 auto 10px;opacity:0.3;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No registrars found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  @if($registrars->hasPages())
    <div class="enc-card__footer" style="text-align:center;">
      {{ $registrars->links() }}
    </div>
  @endif
</div>

<style>
  .enc-stat-icon--pink {
    background-color: rgba(236, 72, 153, 0.1);
    color: #ec4899;
  }

  .enc-stat-icon--info {
    background-color: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
  }

  .enc-stat-icon--teal {
    background-color: rgba(20, 184, 166, 0.1);
    color: #14b8a6;
  }
</style>

@endsection
