@extends('layouts.app')
@section('title', $assignment->title)
@section('breadcrumb', 'Assignments / ' . $assignment->title)

@push('head')
<style>
.sub-header { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px 24px; margin-bottom: 20px; }
.sub-meta { display: flex; gap: 24px; flex-wrap: wrap; margin-top: 12px; }
.sub-meta-item { font-size: .83rem; color: #475569; }
.sub-meta-item strong { color: #1e293b; }

.sub-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
@media(max-width:640px){ .sub-stats { grid-template-columns: repeat(2,1fr); } }
.sub-stat { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; text-align: center; }
.sub-stat-num { font-size: 1.8rem; font-weight: 800; color: #1e293b; }
.sub-stat-label { font-size: .72rem; color: #64748b; font-weight: 600; text-transform: uppercase; margin-top: 2px; }

.sub-table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.sub-table { width: 100%; border-collapse: collapse; }
.sub-table th { padding: 10px 16px; background: #f8fafc; font-size: .73rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: left; border-bottom: 1px solid #e2e8f0; }
.sub-table td { padding: 12px 16px; font-size: .85rem; color: #334155; border-bottom: 1px solid #f8fafc; vertical-align: top; }
.sub-table tr:last-child td { border-bottom: none; }
.sub-table tr:hover td { background: #f8fafc; }
.sub-badge { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: .71rem; font-weight: 700; }
.sub-badge--submitted { background: #dbeafe; color: #1e40af; }
.sub-badge--graded    { background: #d1fae5; color: #065f46; }
.grade-form { display: flex; gap: 6px; align-items: center; }
.grade-form input { width: 70px; padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .82rem; }
.grade-form textarea { width: 130px; padding: 5px 8px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: .78rem; resize: none; height: 34px; }
.grade-form button { background: #059669; color: #fff; border: none; border-radius: 7px; padding: 5px 12px; font-size: .78rem; font-weight: 700; cursor: pointer; white-space: nowrap; }
</style>
@endpush

@section('content')

<div style="margin-bottom:16px;">
  <a href="{{ route('assignments.faculty.index') }}" style="color:#64748b;font-size:.83rem;text-decoration:none;">← Back to Assignments</a>
</div>

<div class="sub-header">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.3rem;font-weight:800;color:#1e293b;margin:0;">{{ $assignment->title }}</h1>
      <div style="font-size:.84rem;color:#64748b;margin-top:4px;">
        {{ $assignment->sectionSubject?->subject?->subject_name }} · {{ $assignment->sectionSubject?->section?->section_name }}
      </div>
    </div>
    <span style="background:{{ $assignment->is_published ? '#d1fae5' : '#f1f5f9' }};color:{{ $assignment->is_published ? '#065f46' : '#64748b' }};padding:5px 14px;border-radius:99px;font-size:.78rem;font-weight:700;">
      {{ $assignment->is_published ? 'Published' : 'Draft' }}
    </span>
  </div>
  <div class="sub-meta">
    <div class="sub-meta-item"><strong>Type:</strong> {{ ucfirst($assignment->type) }}</div>
    <div class="sub-meta-item"><strong>Max Score:</strong> {{ $assignment->max_score }}</div>
    <div class="sub-meta-item"><strong>Due:</strong> {{ $assignment->due_date->format('M d, Y g:i A') }}</div>
    <div class="sub-meta-item"><strong>Late:</strong> {{ $assignment->allow_late ? 'Allowed' : 'Not allowed' }}</div>
  </div>
  @if($assignment->instructions)
    <div style="margin-top:14px;background:#f8fafc;border-radius:10px;padding:12px 14px;font-size:.85rem;color:#334155;border:1px solid #e2e8f0;">
      {{ $assignment->instructions }}
    </div>
  @endif
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

<div class="sub-stats">
  <div class="sub-stat">
    <div class="sub-stat-num">{{ $assignment->submissions->count() }}</div>
    <div class="sub-stat-label">Submitted</div>
  </div>
  <div class="sub-stat">
    <div class="sub-stat-num">{{ $assignment->submissions->where('status','graded')->count() }}</div>
    <div class="sub-stat-label">Graded</div>
  </div>
  <div class="sub-stat">
    <div class="sub-stat-num">{{ $assignment->submissions->whereNotNull('score')->avg('score') ? number_format($assignment->submissions->whereNotNull('score')->avg('score'),1) : '—' }}</div>
    <div class="sub-stat-label">Avg Score</div>
  </div>
  <div class="sub-stat">
    <div class="sub-stat-num">{{ $assignment->submissions->whereNotNull('score')->max('score') ?? '—' }}</div>
    <div class="sub-stat-label">Highest</div>
  </div>
</div>

<div class="sub-table-card">
  @if($assignment->submissions->isEmpty())
    <div style="text-align:center;padding:40px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📬</div>
      <p style="font-weight:600;margin:0 0 4px;">No submissions yet</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="sub-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Submitted At</th>
            <th>Attachment</th>
            <th>Content</th>
            <th>Status</th>
            <th>Score / {{ $assignment->max_score }}</th>
            <th>Grade</th>
          </tr>
        </thead>
        <tbody>
          @foreach($assignment->submissions as $sub)
          <tr>
            <td>
              <div style="font-weight:700;color:#1e293b;">{{ $sub->student?->first_name }} {{ $sub->student?->last_name }}</div>
              <div style="font-size:.74rem;color:#64748b;">LRN: {{ $sub->student?->lrn ?? 'N/A' }}</div>
            </td>
            <td style="font-size:.82rem;color:#475569;">
              {{ $sub->submitted_at?->format('M d, Y g:i A') ?? '—' }}
              @if($sub->submitted_at && $assignment->due_date && $sub->submitted_at->isAfter($assignment->due_date))
                <span style="display:block;font-size:.7rem;color:#f59e0b;font-weight:700;">Late</span>
              @endif
            </td>
            <td>
              @if($sub->file_path)
                <a href="{{ Storage::url($sub->file_path) }}" target="_blank" style="font-size:.8rem;color:#3b82f6;font-weight:600;">
                  📎 {{ Str::limit($sub->file_name, 20) }}
                </a>
              @else
                <span style="color:#94a3b8;font-size:.8rem;">None</span>
              @endif
            </td>
            <td style="max-width:180px;font-size:.8rem;color:#475569;" title="{{ $sub->content }}">
              {{ $sub->content ? Str::limit($sub->content, 60) : '—' }}
            </td>
            <td><span class="sub-badge sub-badge--{{ $sub->status }}">{{ ucfirst($sub->status) }}</span></td>
            <td>
              @if($sub->isGraded())
                <span style="font-weight:700;color:#059669;">{{ $sub->score }} / {{ $assignment->max_score }}</span>
                <span style="font-size:.75rem;color:#64748b;"> ({{ $sub->percentage }}%)</span>
              @else
                <span style="color:#94a3b8;font-size:.82rem;">Not graded</span>
              @endif
            </td>
            <td>
              <form method="POST" action="{{ route('assignments.grade', $sub) }}" class="grade-form">
                @csrf @method('PATCH')
                <input type="number" name="score" placeholder="Score" min="0" max="{{ $assignment->max_score }}" step="0.5" value="{{ $sub->score }}">
                <textarea name="feedback" placeholder="Feedback…">{{ $sub->feedback }}</textarea>
                <button type="submit">{{ $sub->isGraded() ? 'Update' : 'Grade' }}</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
