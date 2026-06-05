@extends('layouts.app')
@section('title', 'Assignments')
@section('breadcrumb', 'Assignments')

@push('head')
<style>
.asgn-toolbar { display: flex; gap: 12px; align-items: center; margin-bottom: 22px; flex-wrap: wrap; }
.asgn-toolbar h1 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0; flex: 1; }
.btn-primary { background: #0f172a; color: #fff; border: none; border-radius: 9px; padding: 9px 20px; font-weight: 700; font-size: .87rem; cursor: pointer; transition: background .15s; }
.btn-primary:hover { background: #1e293b; }

.asgn-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 999; align-items: center; justify-content: center; }
.asgn-modal-overlay.open { display: flex; }
.asgn-modal { background: #fff; border-radius: 18px; padding: 28px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; }
.asgn-modal h3 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0 0 20px; }
.asgn-field label { display: block; font-size: .78rem; font-weight: 600; color: #475569; margin-bottom: 5px; margin-top: 12px; }
.asgn-field select,.asgn-field input,.asgn-field textarea { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: .86rem; background: #f8fafc; box-sizing: border-box; }
.asgn-field select:focus,.asgn-field input:focus,.asgn-field textarea:focus { outline: none; border-color: #0f172a; background: #fff; }
.asgn-modal-btns { display: flex; gap: 10px; margin-top: 20px; }
.btn-cancel { background: #f1f5f9; color: #475569; border: none; border-radius: 9px; padding: 9px 20px; font-weight: 700; font-size: .87rem; cursor: pointer; }
.asgn-check-row { display: flex; align-items: center; gap: 8px; margin-top: 10px; font-size: .86rem; color: #475569; }

.asgn-table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.asgn-table { width: 100%; border-collapse: collapse; }
.asgn-table th { padding: 10px 16px; background: #f8fafc; font-size: .73rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: left; border-bottom: 1px solid #e2e8f0; }
.asgn-table td { padding: 13px 16px; font-size: .85rem; color: #334155; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.asgn-table tr:last-child td { border-bottom: none; }
.asgn-table tr:hover td { background: #f8fafc; }

.asgn-type { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: .71rem; font-weight: 700; }
.asgn-type--assignment { background: #dbeafe; color: #1e40af; }
.asgn-type--quiz       { background: #fef3c7; color: #92400e; }
.asgn-type--project    { background: #d1fae5; color: #065f46; }
.asgn-type--activity   { background: #f3e8ff; color: #6d28d9; }

.asgn-pub { display: inline-flex; gap: 4px; align-items: center; font-size: .75rem; font-weight: 700; padding: 3px 9px; border-radius: 99px; }
.asgn-pub--yes { background: #d1fae5; color: #065f46; }
.asgn-pub--no  { background: #f1f5f9; color: #64748b; }

.asgn-action-btn { background: #0f172a; color: #fff; border: none; border-radius: 7px; padding: 5px 12px; font-size: .78rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
.asgn-pub-btn { background: #059669; color: #fff; border: none; border-radius: 7px; padding: 5px 12px; font-size: .78rem; font-weight: 700; cursor: pointer; }
</style>
@endpush

@section('content')

<div class="asgn-toolbar">
  <h1>Assignments</h1>
  <button class="btn-primary" onclick="document.getElementById('createModal').classList.add('open')">+ New Assignment</button>
</div>

@if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;font-size:.86rem;">
    ✓ {{ session('success') }}
  </div>
@endif

{{-- Create Modal --}}
<div class="asgn-modal-overlay" id="createModal">
  <div class="asgn-modal">
    <h3>Create Assignment</h3>
    <form method="POST" action="{{ route('assignments.faculty.store') }}">
      @csrf
      <div class="asgn-field">
        <label>Class *</label>
        <select name="section_subject_id" required>
          <option value="">— Select class —</option>
          @foreach($sectionSubjects as $ss)
            <option value="{{ $ss->id }}">{{ $ss->subject?->subject_name }} — {{ $ss->section?->section_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="asgn-field">
        <label>Title *</label>
        <input type="text" name="title" placeholder="Assignment title" required maxlength="255">
      </div>
      <div class="asgn-field">
        <label>Type *</label>
        <select name="type" required>
          <option value="assignment">Assignment</option>
          <option value="quiz">Quiz</option>
          <option value="project">Project</option>
          <option value="activity">Activity</option>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="asgn-field">
          <label>Max Score *</label>
          <input type="number" name="max_score" min="1" max="1000" step="0.5" value="100" required>
        </div>
        <div class="asgn-field">
          <label>Due Date *</label>
          <input type="datetime-local" name="due_date" required>
        </div>
      </div>
      <div class="asgn-field">
        <label>Instructions</label>
        <textarea name="instructions" rows="3" placeholder="Optional instructions…" maxlength="5000"></textarea>
      </div>
      <div class="asgn-check-row">
        <input type="checkbox" name="allow_late" id="chk_late" value="1">
        <label for="chk_late">Allow late submissions</label>
      </div>
      <div class="asgn-check-row">
        <input type="checkbox" name="is_published" id="chk_pub" value="1" checked>
        <label for="chk_pub">Publish immediately (notify students)</label>
      </div>
      <div class="asgn-modal-btns">
        <button type="submit" class="btn-primary" style="flex:1;">Create Assignment</button>
        <button type="button" class="btn-cancel" onclick="document.getElementById('createModal').classList.remove('open')">Cancel</button>
      </div>
    </form>
  </div>
</div>

{{-- Table --}}
<div class="asgn-table-card">
  @if($assignments->isEmpty())
    <div style="text-align:center;padding:50px;color:#94a3b8;">
      <div style="font-size:2.5rem;margin-bottom:8px;">📝</div>
      <p style="font-weight:600;margin:0 0 4px;">No assignments yet</p>
      <p style="font-size:.82rem;margin:0;">Create your first assignment using the button above.</p>
    </div>
  @else
    <div style="overflow-x:auto;">
      <table class="asgn-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Class</th>
            <th>Type</th>
            <th>Due Date</th>
            <th>Max Score</th>
            <th>Submissions</th>
            <th>Published</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($assignments as $asgn)
          <tr>
            <td style="font-weight:700;color:#1e293b;">{{ $asgn->title }}</td>
            <td style="font-size:.82rem;">
              <div style="font-weight:600;">{{ $asgn->sectionSubject?->subject?->subject_name }}</div>
              <div style="color:#64748b;">{{ $asgn->sectionSubject?->section?->section_name }}</div>
            </td>
            <td><span class="asgn-type asgn-type--{{ $asgn->type }}">{{ ucfirst($asgn->type) }}</span></td>
            <td style="font-size:.83rem;">
              {{ \Carbon\Carbon::parse($asgn->due_date)->format('M d, Y g:i A') }}
              @if($asgn->isOverdue())
                <span style="display:block;font-size:.7rem;color:#ef4444;font-weight:700;">OVERDUE</span>
              @endif
            </td>
            <td>{{ $asgn->max_score }}</td>
            <td>
              <span style="font-weight:700;color:#0f172a;">{{ $asgn->submissions->count() }}</span>
              <span style="color:#64748b;font-size:.8rem;"> submitted</span>
              <div style="font-size:.75rem;color:#10b981;font-weight:600;">
                {{ $asgn->submissions->where('status','graded')->count() }} graded
              </div>
            </td>
            <td>
              <span class="asgn-pub asgn-pub--{{ $asgn->is_published ? 'yes' : 'no' }}">
                {{ $asgn->is_published ? '✓ Published' : 'Draft' }}
              </span>
            </td>
            <td style="display:flex;gap:6px;flex-wrap:wrap;">
              <a href="{{ route('assignments.faculty.show', $asgn) }}" class="asgn-action-btn">View</a>
              @if(!$asgn->is_published)
                <form method="POST" action="{{ route('assignments.publish', $asgn) }}" style="margin:0;">
                  @csrf @method('PATCH')
                  <button type="submit" class="asgn-pub-btn">Publish</button>
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

<script>
document.getElementById('createModal').addEventListener('click', function(e){
  if(e.target === this) this.classList.remove('open');
});
</script>
@endsection
