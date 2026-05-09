@extends('layouts.app')
@section('title', 'Grade Lock Management')
@section('breadcrumb', 'Grade Lock')

@section('content')
<div style="max-width:1100px;">

  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:28px;">
    <div>
      <h1 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Grade Lock Management</h1>
      <p style="font-size:.875rem;color:#94a3b8;margin:0;">
        Lock finalized grades to prevent further edits, and review faculty unlock requests.
        @if($activeQuarter)
          &nbsp;·&nbsp; <span style="font-weight:600;color:#6366f1;">{{ $activeQuarter->quarter_name }}</span>
        @endif
      </p>
    </div>

    @if($activeQuarter)
    <form method="POST" action="{{ route('registrar.grade-lock.lock-all') }}"
          onsubmit="return confirm('Lock ALL finalized grades for the current quarter? This affects every section.')">
      @csrf
      <button type="submit"
              style="padding:.5rem 1.25rem;background:#dc2626;color:#fff;border:none;border-radius:9px;font-size:.84rem;font-weight:700;cursor:pointer;">
        Global Lock All
      </button>
    </form>
    @endif
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#166534;">
    {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#991b1b;">
    {{ session('error') }}
  </div>
  @endif

  @if(!$activeYear)
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:40px;text-align:center;">
    <div style="font-weight:700;color:#374151;margin-bottom:8px;">No Active Academic Year</div>
    <div style="font-size:.85rem;color:#94a3b8;">Activate an academic year to manage grade locks.</div>
  </div>

  @else

  {{-- ── Pending Unlock Requests ────────────────────────────────────────── --}}
  @if($pendingRequests->isNotEmpty())
  <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:20px 24px;margin-bottom:24px;">
    <div style="font-size:.88rem;font-weight:700;color:#92400e;margin-bottom:14px;">
      Pending Unlock Requests ({{ $pendingRequests->count() }})
    </div>
    <div style="display:flex;flex-direction:column;gap:12px;">
      @foreach($pendingRequests as $req)
      <div style="background:#fff;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
          <div style="font-weight:700;font-size:.88rem;color:#0f172a;">
            {{ $req->sectionSubject?->subject?->subject_name ?? '—' }}
            &nbsp;·&nbsp; {{ $req->sectionSubject?->section?->section_name ?? '—' }}
          </div>
          <div style="font-size:.78rem;color:#64748b;margin-top:2px;">
            Requested by <strong>{{ $req->requestedBy?->full_name ?? '—' }}</strong>
            · {{ $req->created_at->diffForHumans() }}
          </div>
          <div style="font-size:.82rem;color:#374151;margin-top:6px;font-style:italic;">
            "{{ $req->reason }}"
          </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
          <form method="POST" action="{{ route('registrar.grade-lock.approve', $req) }}">
            @csrf
            <button type="submit"
                    style="padding:.4rem .9rem;background:#059669;color:#fff;border:none;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;"
                    onclick="return confirm('Approve this unlock request?')">
              Approve
            </button>
          </form>
          <form method="POST" action="{{ route('registrar.grade-lock.deny', $req) }}">
            @csrf
            <input type="hidden" name="review_notes" value="">
            <button type="submit"
                    style="padding:.4rem .9rem;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;"
                    onclick="return confirm('Deny this unlock request?')">
              Deny
            </button>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- ── Section Subjects Table ─────────────────────────────────────────── --}}
  @if($sectionSubjects->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:40px;text-align:center;">
    <div style="font-weight:700;color:#374151;margin-bottom:8px;">No Classes Found</div>
    <div style="font-size:.85rem;color:#94a3b8;">No section-subjects exist for the active academic year.</div>
  </div>
  @else
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.84rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;">Subject</th>
            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;">Section</th>
            <th style="padding:12px 16px;text-align:left;font-weight:700;color:#374151;">Faculty</th>
            <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">Draft</th>
            <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">Submitted</th>
            <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">Finalized</th>
            <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">Locked</th>
            <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sectionSubjects as $ss)
          @php $gc = $ss->grade_counts; @endphp
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:10px 16px;font-weight:600;color:#0f172a;">
              {{ $ss->subject?->subject_name ?? '—' }}
              @if($ss->pending_unlock)
                <span style="font-size:.68rem;background:#fef9c3;color:#713f12;border-radius:4px;padding:1px 5px;margin-left:4px;font-weight:700;">UNLOCK REQ</span>
              @endif
            </td>
            <td style="padding:10px 16px;color:#374151;">{{ $ss->section?->section_name ?? '—' }}</td>
            <td style="padding:10px 16px;color:#64748b;">{{ $ss->faculty?->full_name ?? '—' }}</td>
            <td style="padding:10px 16px;text-align:center;color:#94a3b8;">{{ $gc['draft'] ?: '—' }}</td>
            <td style="padding:10px 16px;text-align:center;color:#d97706;font-weight:{{ $gc['submitted'] ? '700' : '400' }};">{{ $gc['submitted'] ?: '—' }}</td>
            <td style="padding:10px 16px;text-align:center;color:#059669;font-weight:{{ $gc['finalized'] ? '700' : '400' }};">{{ $gc['finalized'] ?: '—' }}</td>
            <td style="padding:10px 16px;text-align:center;color:#dc2626;font-weight:{{ $gc['locked'] ? '700' : '400' }};">{{ $gc['locked'] ?: '—' }}</td>
            <td style="padding:10px 16px;text-align:center;">
              @if($gc['finalized'] > 0)
              <form method="POST" action="{{ route('registrar.grade-lock.lock-section', $ss) }}"
                    onsubmit="return confirm('Lock {{ $gc['finalized'] }} finalized grade(s) for this class?')">
                @csrf
                <button type="submit"
                        style="padding:.35rem .8rem;background:#1e3a5f;color:#fff;border:none;border-radius:7px;font-size:.76rem;font-weight:700;cursor:pointer;">
                  Lock
                </button>
              </form>
              @elseif($gc['locked'] > 0)
              <span style="font-size:.76rem;color:#dc2626;font-weight:700;">Locked</span>
              @else
              <span style="font-size:.76rem;color:#cbd5e1;">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  @endif

</div>
@endsection
