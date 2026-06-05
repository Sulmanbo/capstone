@extends('layouts.app')
@section('title', 'My Assignments')
@section('breadcrumb', 'My Assignments')

@push('head')
<style>
.sa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px,1fr)); gap: 18px; }
.sa-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: box-shadow .15s; }
.sa-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.sa-card-header { padding: 16px 18px 12px; border-bottom: 1px solid #f1f5f9; }
.sa-card-title { font-size: .96rem; font-weight: 800; color: #1e293b; margin: 0 0 4px; }
.sa-card-subject { font-size: .78rem; color: #64748b; }
.sa-card-body { padding: 14px 18px; }
.sa-meta { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 12px; }
.sa-meta-item { font-size: .78rem; color: #475569; }
.sa-meta-item strong { color: #1e293b; }

.sa-due-row { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; padding: 8px 12px; border-radius: 9px; }
.sa-due-row--ok      { background: #f0fdf4; }
.sa-due-row--warning { background: #fffbeb; }
.sa-due-row--overdue { background: #fef2f2; }
.sa-due-text { font-size: .82rem; font-weight: 700; }
.sa-due-row--ok .sa-due-text      { color: #059669; }
.sa-due-row--warning .sa-due-text { color: #d97706; }
.sa-due-row--overdue .sa-due-text { color: #dc2626; }

.sa-submit-form { border-top: 1px solid #f1f5f9; padding: 14px 18px; }
.sa-submit-form label { display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: 5px; margin-top: 8px; }
.sa-submit-form textarea { width: 100%; padding: 8px 10px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: .83rem; box-sizing: border-box; resize: vertical; }
.sa-submit-form input[type=file] { width: 100%; font-size: .82rem; }
.sa-submit-btn { background: #0f172a; color: #fff; border: none; border-radius: 8px; padding: 8px 22px; font-weight: 700; font-size: .85rem; cursor: pointer; margin-top: 10px; }
.sa-submit-btn:hover { background: #1e293b; }

.sa-grade-badge { display: inline-flex; align-items: center; gap: 6px; background: #d1fae5; color: #065f46; padding: 5px 14px; border-radius: 99px; font-weight: 700; font-size: .84rem; }
.sa-submitted-badge { display: inline-flex; align-items: center; gap: 6px; background: #dbeafe; color: #1e40af; padding: 5px 14px; border-radius: 99px; font-weight: 700; font-size: .84rem; }
.sa-type { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: .7rem; font-weight: 700; margin-bottom: 6px; }
.sa-type--assignment { background: #dbeafe; color: #1e40af; }
.sa-type--quiz       { background: #fef3c7; color: #92400e; }
.sa-type--project    { background: #d1fae5; color: #065f46; }
.sa-type--activity   { background: #f3e8ff; color: #6d28d9; }
</style>
@endpush

@section('content')

<div style="margin-bottom:22px;">
  <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0;">My Assignments</h1>
  <p style="color:#64748b;font-size:.85rem;margin:4px 0 0;">View and submit your assignments, quizzes, and projects.</p>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

@if($assignments->isEmpty())
  <div style="text-align:center;padding:60px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;color:#94a3b8;">
    <div style="font-size:3rem;margin-bottom:10px;">📚</div>
    <p style="font-weight:700;margin:0 0 4px;font-size:1rem;">No assignments yet</p>
    <p style="font-size:.85rem;margin:0;">Check back later or contact your teacher.</p>
  </div>
@else
  <div class="sa-grid">
    @foreach($assignments as $asgn)
      @php
        $submission = $asgn->submissions->first();
        $dueDate = $asgn->due_date;
        $now = now();
        $daysLeft = $now->diffInDays($dueDate, false);
        $isOverdue = $asgn->isOverdue();
      @endphp
      <div class="sa-card">
        <div class="sa-card-header">
          <span class="sa-type sa-type--{{ $asgn->type }}">{{ ucfirst($asgn->type) }}</span>
          <h3 class="sa-card-title">{{ $asgn->title }}</h3>
          <div class="sa-card-subject">{{ $asgn->sectionSubject?->subject?->subject_name }}</div>
        </div>
        <div class="sa-card-body">
          <div class="sa-meta">
            <div class="sa-meta-item"><strong>Max Score:</strong> {{ $asgn->max_score }}</div>
            <div class="sa-meta-item"><strong>Late:</strong> {{ $asgn->allow_late ? 'Allowed' : 'Not allowed' }}</div>
          </div>

          @if($isOverdue && !$asgn->allow_late)
            <div class="sa-due-row sa-due-row--overdue">
              <span>⚠️</span>
              <span class="sa-due-text">Overdue — {{ $dueDate->format('M d, Y g:i A') }}</span>
            </div>
          @elseif($isOverdue && $asgn->allow_late)
            <div class="sa-due-row sa-due-row--warning">
              <span>⏰</span>
              <span class="sa-due-text">Past due (late submissions accepted) — {{ $dueDate->format('M d, Y g:i A') }}</span>
            </div>
          @elseif($daysLeft <= 1)
            <div class="sa-due-row sa-due-row--warning">
              <span>⏰</span>
              <span class="sa-due-text">Due soon — {{ $dueDate->format('M d, Y g:i A') }}</span>
            </div>
          @else
            <div class="sa-due-row sa-due-row--ok">
              <span>✅</span>
              <span class="sa-due-text">Due {{ $dueDate->format('M d, Y') }} ({{ $daysLeft }} days left)</span>
            </div>
          @endif

          @if($asgn->instructions)
            <div style="font-size:.82rem;color:#475569;background:#f8fafc;padding:10px 12px;border-radius:9px;border:1px solid #e2e8f0;margin-bottom:4px;">
              {{ Str::limit($asgn->instructions, 120) }}
            </div>
          @endif

          @if($submission)
            @if($submission->isGraded())
              <div class="sa-grade-badge">
                ✓ Graded: {{ $submission->score }}/{{ $asgn->max_score }} ({{ $submission->percentage }}%)
              </div>
              @if($submission->feedback)
                <div style="margin-top:8px;font-size:.8rem;color:#475569;font-style:italic;">Feedback: {{ $submission->feedback }}</div>
              @endif
            @else
              <div class="sa-submitted-badge">📤 Submitted {{ $submission->submitted_at?->format('M d, Y') }}</div>
            @endif
          @endif
        </div>

        @if(!$submission || $submission->status !== 'graded')
          @if(!$isOverdue || $asgn->allow_late)
          <div class="sa-submit-form">
            <form method="POST" action="{{ route('assignments.student.submit', $asgn) }}" enctype="multipart/form-data">
              @csrf
              <label>Your Answer / Response</label>
              <textarea name="content" rows="2" placeholder="Type your response here…">{{ $submission?->content }}</textarea>
              <label>Attachment (optional, max 10MB)</label>
              <input type="file" name="file">
              <br>
              <button type="submit" class="sa-submit-btn">
                {{ $submission ? 'Resubmit' : 'Submit Assignment' }}
              </button>
            </form>
          </div>
          @else
            <div style="padding:12px 18px;background:#fef2f2;border-top:1px solid #fee2e2;">
              <p style="margin:0;font-size:.82rem;color:#dc2626;font-weight:600;">Deadline has passed. Submissions are closed.</p>
            </div>
          @endif
        @endif
      </div>
    @endforeach
  </div>
@endif
@endsection
