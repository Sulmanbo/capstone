@extends('layouts.app')

@section('title', 'My Complaints')
@section('breadcrumb', 'Grade Complaints')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">My Grade Complaints</h1>
      <p class="enc-page__subtitle">Track the status of your submitted grade concerns.</p>
    </div>
    <a href="{{ route('complaints.create') }}" class="enc-btn enc-btn--primary">+ New Complaint</a>
  </div>
</div>

@if(session('success'))
<div class="enc-alert enc-alert--success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif

@if($complaints->isEmpty())
<div class="enc-card" style="padding:2.5rem;text-align:center;color:var(--gray-400);">
  No complaints submitted yet. If you have a concern about a grade, use the button above.
</div>
@else
<div class="enc-card" style="padding:0;overflow:hidden;">
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="ctable-th" style="text-align:left;">Subject</th>
          <th class="ctable-th">Quarter</th>
          <th class="ctable-th">Status</th>
          <th class="ctable-th" style="text-align:left;">Submitted</th>
          <th class="ctable-th" style="text-align:left;">Response</th>
        </tr>
      </thead>
      <tbody>
        @foreach($complaints as $c)
        <tr class="ctable-row">
          <td class="ctable-td" style="font-weight:600;">
            {{ $c->sectionSubject?->subject?->subject_name ?? '—' }}
          </td>
          <td class="ctable-td" style="text-align:center;">
            {{ $c->gradingQuarter ? 'Q'.$c->gradingQuarter->quarter_number : 'General' }}
          </td>
          <td class="ctable-td" style="text-align:center;">
            <span class="status-badge status-{{ $c->status }}">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
          </td>
          <td class="ctable-td" style="font-size:.82rem;color:var(--gray-500);">
            {{ $c->created_at->format('M d, Y') }}
          </td>
          <td class="ctable-td" style="font-size:.83rem;max-width:280px;">
            @if($c->response)
              <div style="color:var(--navy);margin-bottom:.2rem;">{{ Str::limit($c->response, 100) }}</div>
              @if($c->respondedBy)
              <div style="color:var(--gray-400);font-size:.76rem;">by {{ $c->respondedBy->full_name }} · {{ $c->responded_at?->format('M d') }}</div>
              @endif
            @else
              <span style="color:var(--gray-400);">Awaiting review</span>
            @endif
          </td>
        </tr>

        {{-- Expandable reason row --}}
        <tr style="border-bottom:1px solid rgba(15,23,42,.06);">
          <td colspan="5" style="padding:.5rem 1rem .9rem 1rem;font-size:.8rem;color:var(--gray-500);background:#f9fafb;">
            <strong>Your concern:</strong> {{ Str::limit($c->reason, 200) }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($complaints->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $complaints->links() }}
  </div>
  @endif
</div>
@endif
@endsection

@push('head')
<style>
.ctable-th { padding:11px 14px; font-size:.76rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid rgba(15,23,42,.08); text-align:center; }
.ctable-td { padding:12px 14px; border-bottom:1px solid rgba(15,23,42,.04); vertical-align:top; }
.ctable-row:hover td { background:rgba(15,23,42,.015); }
.status-badge { display:inline-block; padding:.25rem .7rem; border-radius:999px; font-size:.74rem; font-weight:700; }
.status-pending      { background:#fef9c3; color:#854d0e; }
.status-under_review { background:#dbeafe; color:#1e40af; }
.status-resolved     { background:#dcfce7; color:#166534; }
.status-dismissed    { background:#f3f4f6; color:#6b7280; }
.enc-btn { display:inline-flex; align-items:center; justify-content:center; padding:.6rem 1.25rem; border-radius:999px; font-weight:700; font-size:.87rem; text-decoration:none; border:none; cursor:pointer; }
.enc-btn--primary { background:var(--primary); color:#fff; }
.enc-alert--success { background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:.85rem 1rem; font-size:.87rem; color:#166534; }
</style>
@endpush
