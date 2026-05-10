@extends('layouts.app')

@section('title', 'Grade Complaints')
@section('breadcrumb', 'Grade Complaints Management')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Grade Complaints</h1>
      <p class="enc-page__subtitle">Review and respond to student grade concerns.</p>
    </div>
  </div>
</div>

@if(session('success'))
<div class="enc-alert enc-alert--success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif

@if($complaints->isEmpty())
<div class="enc-card" style="padding:2.5rem;text-align:center;color:var(--gray-400);">
  No grade complaints found.
</div>
@else

@foreach($complaints as $c)
<div class="enc-card complaint-card" style="padding:0;margin-bottom:1.1rem;overflow:hidden;">

  {{-- Card header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;padding:.9rem 1.25rem;border-bottom:1px solid rgba(15,23,42,.06);flex-wrap:wrap;gap:.5rem;">
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
      <span class="status-badge status-{{ $c->status }}">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
      <strong style="color:var(--navy);">{{ $c->student?->full_name ?? '—' }}</strong>
      <span style="color:var(--gray-400);font-size:.82rem;">{{ $c->student?->lrn ?? '' }}</span>
      <span style="color:var(--gray-400);">·</span>
      <span style="font-size:.85rem;color:var(--gray-500);">{{ $c->sectionSubject?->subject?->subject_name ?? '—' }}</span>
      @if($c->gradingQuarter)
        <span style="font-size:.82rem;background:rgba(15,23,42,.06);padding:.15rem .55rem;border-radius:999px;color:var(--navy);">Q{{ $c->gradingQuarter->quarter_number }}</span>
      @endif
    </div>
    <span style="font-size:.78rem;color:var(--gray-400);">{{ $c->created_at->format('M d, Y · h:i A') }}</span>
  </div>

  <div style="padding:1rem 1.25rem;">

    {{-- Grade snapshot if available --}}
    @if($c->grade)
    <div style="display:inline-flex;align-items:center;gap:.6rem;background:rgba(15,23,42,.04);border-radius:8px;padding:.45rem .85rem;margin-bottom:.85rem;font-size:.83rem;">
      <span style="color:var(--gray-500);">Grade on record:</span>
      <strong style="color:var(--navy);font-size:1rem;">{{ number_format($c->grade->final_grade, 0) }}</strong>
      <span style="color:var(--gray-400);">·</span>
      <span style="color:{{ $c->grade->final_grade >= 75 ? '#166534' : '#991b1b' }};">{{ $c->grade->descriptor ?? ($c->grade->final_grade >= 75 ? 'Passed' : 'Failed') }}</span>
    </div>
    @endif

    {{-- Student's reason --}}
    <div style="margin-bottom:1rem;">
      <div style="font-size:.75rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">Student's Concern</div>
      <div style="font-size:.87rem;color:var(--navy);line-height:1.55;white-space:pre-wrap;">{{ $c->reason }}</div>
    </div>

    {{-- Existing response --}}
    @if($c->response)
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;">
      <div style="font-size:.75rem;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">
        Response by {{ $c->respondedBy?->full_name ?? '—' }}
        @if($c->responded_at) · {{ $c->responded_at->format('M d, Y') }} @endif
      </div>
      <div style="font-size:.87rem;color:#14532d;line-height:1.55;">{{ $c->response }}</div>
    </div>
    @endif

    {{-- Response form (shown for open complaints) --}}
    @if($c->isOpen())
    <form method="POST" action="{{ route('complaints.respond', $c->id) }}" style="border-top:1px solid rgba(15,23,42,.06);padding-top:.9rem;display:grid;gap:.75rem;">
      @csrf
      @method('PATCH')

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
        <div>
          <label class="enc-label">Update Status</label>
          <select name="status" class="enc-input" required>
            <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
            <option value="resolved"     {{ old('status') == 'resolved'     ? 'selected' : '' }}>Resolved</option>
            <option value="dismissed"    {{ old('status') == 'dismissed'    ? 'selected' : '' }}>Dismissed</option>
          </select>
        </div>
      </div>

      <div>
        <label class="enc-label">Response / Remarks</label>
        <textarea name="response" class="enc-input" rows="3"
          placeholder="Provide a clear explanation or resolution..." required minlength="10" maxlength="2000">{{ old('response') }}</textarea>
      </div>

      <div>
        <button type="submit" class="enc-btn enc-btn--primary">Save Response</button>
      </div>
    </form>
    @endif

  </div>
</div>
@endforeach

@if($complaints->hasPages())
<div style="margin-top:1rem;">{{ $complaints->links() }}</div>
@endif
@endif
@endsection

@push('head')
<style>
.complaint-card { border:1px solid rgba(15,23,42,.07); }
.enc-label { display:block; font-size:.78rem; font-weight:700; color:var(--gray-500); margin-bottom:.3rem; }
.enc-input { width:100%; padding:.55rem .85rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.88rem; background:#fff; color:var(--navy); outline:none; font-family:inherit; }
.enc-input:focus { border-color:var(--primary); }
textarea.enc-input { resize:vertical; }
.enc-btn { display:inline-flex; align-items:center; justify-content:center; padding:.6rem 1.25rem; border-radius:999px; font-weight:700; font-size:.87rem; cursor:pointer; border:none; text-decoration:none; }
.enc-btn--primary { background:var(--primary); color:#fff; }
.status-badge { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.74rem; font-weight:700; }
.status-pending      { background:#fef9c3; color:#854d0e; }
.status-under_review { background:#dbeafe; color:#1e40af; }
.status-resolved     { background:#dcfce7; color:#166534; }
.status-dismissed    { background:#f3f4f6; color:#6b7280; }
.enc-alert--success { background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:.85rem 1rem; font-size:.87rem; color:#166534; }
</style>
@endpush
