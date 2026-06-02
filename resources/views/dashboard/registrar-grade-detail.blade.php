@extends('layouts.app')
@section('title', 'Grade Detail')

@push('head')
<style>
.gd-section { margin-bottom:24px; }
.gd-section__title { font-size:.95rem; font-weight:700; color:#0f172a; margin-bottom:12px; }
.gd-row { display:flex; gap:24px; margin-bottom:16px; flex-wrap:wrap; }
.gd-field { flex:1; min-width:200px; }
.gd-label { font-size:.8rem; font-weight:700; text-transform:uppercase; color:#64748b; margin-bottom:6px; display:block; }
.gd-value { font-size:.95rem; color:#0f172a; font-weight:600; }
.component-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-bottom:12px; }
.component-value { font-size:1.3rem; font-weight:800; color:#1d4ed8; }
.status-badge { display:inline-block; padding:.4rem .8rem; border-radius:6px; font-size:.75rem; font-weight:700; text-transform:uppercase; }
.status-badge.submitted { background:#fef3c7; color:#92400e; }
.status-badge.finalized { background:#dbeafe; color:#1e40af; }
.status-badge.locked { background:#dcfce7; color:#166534; }
.action-button { padding:.6rem 1.2rem; border-radius:6px; font-size:.85rem; font-weight:600; border:none; cursor:pointer; transition:all .15s; }
.action-button.primary { background:#1d4ed8; color:#fff; }
.action-button.primary:hover { background:#1e40af; }
.action-button.secondary { background:#e2e8f0; color:#475569; }
.action-button.secondary:hover { background:#cbd5e1; }
</style>
@endpush

@section('content')
<div style="max-width:900px;">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.1rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Grade Details</h1>
      <p style="font-size:.82rem;color:#64748b;margin:0;">Review and verify submitted grade</p>
    </div>
    <span class="status-badge status-badge--{{ $grade->status }}">{{ ucfirst($grade->status) }}</span>
  </div>

  @if(session('success'))
  <div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.85rem;">{{ session('success') }}</div>
  @endif

  @if($errors->any())
  <div style="margin-bottom:16px;padding:12px 16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.85rem;">
    @foreach($errors->all() as $err)
    <div>{{ $err }}</div>
    @endforeach
  </div>
  @endif

  <div class="enc-card">
    <div class="gd-section">
      <div class="gd-section__title">Student & Course Information</div>
      <div class="gd-row">
        <div class="gd-field">
          <span class="gd-label">Student Name</span>
          <span class="gd-value">{{ $grade->enrollment->student?->full_name ?? 'Unknown' }}</span>
        </div>
        <div class="gd-field">
          <span class="gd-label">LRN</span>
          <span class="gd-value">{{ $grade->enrollment->student?->lrn ?? '—' }}</span>
        </div>
      </div>
      <div class="gd-row">
        <div class="gd-field">
          <span class="gd-label">Subject</span>
          <span class="gd-value">{{ $grade->sectionSubject?->subject?->subject_name ?? 'Unknown' }}</span>
        </div>
        <div class="gd-field">
          <span class="gd-label">Section</span>
          <span class="gd-value">{{ $grade->sectionSubject?->section?->section_name ?? 'Unknown' }}</span>
        </div>
      </div>
      <div class="gd-row">
        <div class="gd-field">
          <span class="gd-label">Faculty</span>
          <span class="gd-value">{{ $grade->sectionSubject?->faculty?->full_name ?? 'Unknown' }}</span>
        </div>
        <div class="gd-field">
          <span class="gd-label">Quarter</span>
          <span class="gd-value">Q{{ $grade->gradingQuarter?->quarter_number ?? '—' }}</span>
        </div>
      </div>
    </div>

    <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">

    <div class="gd-section">
      <div class="gd-section__title">Grade Components</div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="component-box">
          <div style="font-size:.8rem;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Written Work (40%)</div>
          <div class="component-value">{{ $grade->written_work ?? '—' }}</div>
        </div>
        <div class="component-box">
          <div style="font-size:.8rem;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Performance Task (40%)</div>
          <div class="component-value">{{ $grade->performance_task ?? '—' }}</div>
        </div>
        <div class="component-box">
          <div style="font-size:.8rem;color:#64748b;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Quarterly Assessment (20%)</div>
          <div class="component-value">{{ $grade->quarterly_assessment ?? '—' }}</div>
        </div>
      </div>
    </div>

    <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">

    <div class="gd-section">
      <div class="gd-section__title">Computed Final Grade</div>
      <div class="component-box" style="background:#f0f9ff;">
        <div style="font-size:.85rem;color:#0c4a6e;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Final Grade</div>
        <div style="font-size:2.2rem;font-weight:900;color:#0369a1;margin-bottom:8px;">
          {{ $grade->final_grade ?? ($grade->computeFinalGrade() ? round($grade->computeFinalGrade(), 2) : '—') }}
        </div>
        <div style="font-size:.85rem;color:#0c4a6e;">
          Descriptor: <strong>{{ $grade->descriptor ?? 'N/A' }}</strong>
          <br>
          Status: <strong>{{ $grade->isPassing() ? 'Passing ✓' : 'Below Passing' }}</strong>
        </div>
      </div>
    </div>

    <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">

    <div class="gd-section">
      <div class="gd-section__title">Submission Details</div>
      <div class="gd-row">
        <div class="gd-field">
          <span class="gd-label">Submitted By</span>
          <span class="gd-value">{{ $grade->submittedBy?->full_name ?? 'Unknown' }}</span>
        </div>
        <div class="gd-field">
          <span class="gd-label">Submitted At</span>
          <span class="gd-value">{{ $grade->submitted_at?->format('M d, Y H:i') ?? '—' }}</span>
        </div>
      </div>
      @if($grade->finalized_at)
      <div class="gd-row">
        <div class="gd-field">
          <span class="gd-label">Finalized By</span>
          <span class="gd-value">{{ $grade->finalizedBy?->full_name ?? 'Unknown' }}</span>
        </div>
        <div class="gd-field">
          <span class="gd-label">Finalized At</span>
          <span class="gd-value">{{ $grade->finalized_at?->format('M d, Y H:i') ?? '—' }}</span>
        </div>
      </div>
      @endif
    </div>

    <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">

    <div class="gd-section">
      <div class="gd-section__title">Actions</div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        @if($grade->status === 'submitted')
        <form method="POST" action="{{ route('registrar.grades.finalize', $grade) }}" style="display:inline;">
          @csrf
          <button type="submit" class="action-button primary">Finalize Grade</button>
        </form>
        @endif

        @if($grade->status === 'finalized')
        <form method="POST" action="{{ route('registrar.grades.lock', $grade) }}" style="display:inline;">
          @csrf
          <button type="submit" class="action-button primary">Lock Grade</button>
        </form>
        @endif

        @if($grade->status === 'locked')
        <form method="POST" action="{{ route('registrar.grades.unlock', $grade) }}" style="display:inline;">
          @csrf
          <button type="submit" class="action-button secondary">Unlock Grade</button>
        </form>
        @endif

        <a href="{{ route('registrar.grades') }}" class="action-button secondary" style="text-decoration:none;display:inline-block;">
          Back to List
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
