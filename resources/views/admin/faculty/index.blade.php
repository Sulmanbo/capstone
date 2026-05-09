{{-- resources/views/admin/faculty/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Faculty Management')
@section('breadcrumb', 'Faculty')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Faculty Management</h1>
      <p class="enc-page__subtitle">View and manage all active faculty members with detailed information</p>
    </div>
  </div>
</div>

{{-- Stats Row --}}
<div class="enc-stats" style="margin-bottom:20px;">
  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['total_faculty'] }}</div>
      <div class="enc-stat-label">Total Faculty</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--info">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292m0-5.292a4 4 0 110 5.292m0-5.292A4.353 4.353 0 005.364 9M12 4.354L5.364 9m0 0a1 1 0 11-1.414 1.414m1.414-1.414a4 4 0 015.656 5.656m-5.656-5.656a4.353 4.353 0 00-1.414 5.656M9 20.354a4 4 0 110-5.292m0 5.292a4 4 0 110-5.292m0 5.292A4.353 4.353 0 0112 20.354m0-10a4 4 0 014.243-3.999"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['male_faculty'] }}</div>
      <div class="enc-stat-label">Male Faculty</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--pink">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292m0-5.292a4 4 0 110 5.292m0-5.292A4.353 4.353 0 005.364 9M12 4.354L5.364 9m0 0a1 1 0 11-1.414 1.414m1.414-1.414a4 4 0 015.656 5.656m-5.656-5.656a4.353 4.353 0 00-1.414 5.656M9 20.354a4 4 0 110-5.292m0 5.292a4 4 0 110-5.292m0 5.292A4.353 4.353 0 0112 20.354m0-10a4 4 0 014.243-3.999"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $stats['female_faculty'] }}</div>
      <div class="enc-stat-label">Female Faculty</div>
    </div>
  </div>
</div>

{{-- Faculty Table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.228 6.253 2.092 7.582 2.092 9s4.136 2.747 9.908 2.747c5.772 0 9.908-1.254 9.908-2.747 0-1.418-4.136-2.747-9.908-2.747zm13 0c.586.3.951.893.951 1.602 0 1.813-4.176 3.358-9.322 3.358-5.146 0-9.322-1.545-9.322-3.358 0-.709.365-1.302.951-1.602m16.694 12.286l-3.582-3.582m0 0l-2.16-3.97m-2.5 12.005A11.955 11.955 0 112.487 2.1"/>
      </svg>
      All Faculty
    </div>
    <span class="enc-card__meta">{{ $faculty->total() }} total records</span>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.faculty.index') }}">
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
        <a href="{{ route('admin.faculty.index') }}" class="enc-btn enc-btn--ghost enc-btn--sm">Clear</a>
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
          @forelse($faculty as $member)
            <tr>
              {{-- Name --}}
              <td>
                <div style="font-weight:600;color:var(--navy);font-size:.82rem;">
                  {{ $member->first_name }} {{ $member->last_name }}
                </div>
              </td>

              {{-- Employee No. --}}
              <td class="mono" style="font-size:.75rem;">
                @if($member->employee_number)
                  <span style="color:var(--info);">{{ $member->employee_number }}</span>
                @else
                  <span style="color:var(--gray-200);">—</span>
                @endif
              </td>

              {{-- Username --}}
              <td class="mono">{{ $member->username }}</td>

              {{-- Gender --}}
              <td>
                @if($member->gender)
                  @if($member->gender === 'male')
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
                <span style="color:var(--gray-600);">{{ substr($member->email, 0, 20) }}...</span>
              </td>

              {{-- Registered Date --}}
              <td class="mono" style="font-size:.72rem;">
                {{ $member->created_at->format('m/d/Y') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center;padding:30px;color:var(--gray-200);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:40px;height:40px;margin:0 auto 10px;opacity:0.3;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No faculty members found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Pagination --}}
  @if($faculty->hasPages())
    <div class="enc-card__footer" style="text-align:center;">
      {{ $faculty->links() }}
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
</style>

@endsection
