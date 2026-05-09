@extends('layouts.app')

@section('title', 'Report Card')
@section('breadcrumb', 'Student Report Card')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Official Report Card</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — {{ $reportCard['term'] }} / {{ $reportCard['year'] }}</p>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Summary</div>
    <span class="enc-card__meta">Finalized grade report</span>
  </div>
  <div class="enc-card__body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;">
      <div class="student-summary-panel">
        <span>Student</span>
        <strong>{{ $studentInfo['full_name'] }}</strong>
      </div>
      <div class="student-summary-panel">
        <span>LRN</span>
        <strong>{{ $studentInfo['lrn'] ?? 'N/A' }}</strong>
      </div>
      <div class="student-summary-panel">
        <span>Grade Level</span>
        <strong>{{ $studentInfo['grade_level'] }}</strong>
      </div>
      <div class="student-summary-panel">
        <span>Section</span>
        <strong>{{ $studentInfo['section'] }}</strong>
      </div>
    </div>
  </div>
</div>

<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Grades Breakdown</div>
    <span class="enc-card__meta">Detailed subject scores</span>
  </div>
  <div class="enc-card__body">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left;padding:12px 14px;color:var(--gray-500);font-weight:700;border-bottom:1px solid rgba(15,23,42,.08);">Subject</th>
            <th style="text-align:left;padding:12px 14px;color:var(--gray-500);font-weight:700;border-bottom:1px solid rgba(15,23,42,.08);">Category</th>
            <th style="text-align:right;padding:12px 14px;color:var(--gray-500);font-weight:700;border-bottom:1px solid rgba(15,23,42,.08);">Grade</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reportCard['subjects'] as $subject)
          <tr>
            <td style="padding:14px 14px;border-bottom:1px solid rgba(15,23,42,.06);">{{ $subject['name'] }}</td>
            <td style="padding:14px 14px;border-bottom:1px solid rgba(15,23,42,.06);">{{ $subject['category'] }}</td>
            <td style="padding:14px 14px;border-bottom:1px solid rgba(15,23,42,.06);text-align:right;font-weight:700;color:var(--navy);">{{ $subject['grade'] }}%</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;margin-top:20px;">
  <div class="enc-card student-glass-card" style="padding:1.25rem;">
    <div class="enc-card__header">
      <div class="enc-card__title">Term Results</div>
    </div>
    <div class="enc-card__body">
      <div style="display:grid;gap:12px;">
        <div style="font-size:1rem;color:var(--gray-500);">Term GPA</div>
        <div style="font-size:2rem;font-weight:700;color:var(--primary);">{{ $reportCard['gpa'] }}</div>
        <div style="font-size:.9rem;color:var(--gray-500);">{{ $reportCard['remarks'] }}</div>
      </div>
    </div>
  </div>
  <div class="enc-card student-glass-card" style="padding:1.25rem;">
    <div class="enc-card__header">
      <div class="enc-card__title">Actions</div>
    </div>
    <div class="enc-card__body" style="display:flex;flex-direction:column;gap:12px;">
      <a href="{{ route('report-card.download', auth()->id()) }}" class="enc-button enc-button--primary" target="_blank">Download PDF</a>
      <a href="{{ route('student.dashboard') }}" class="enc-button enc-button--secondary">Back to Dashboard</a>
    </div>
  </div>
</div>

@endsection

@push('head')
<style>
.student-glass-card {
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(255, 255, 255, 0.65);
  backdrop-filter: blur(18px);
  box-shadow: 0 24px 60px rgba(15, 23, 42, 0.06);
}
.enc-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.1rem;
  border-radius: 999px;
  font-weight: 700;
  text-decoration: none;
}
.enc-button--primary {
  background: var(--primary);
  color: #fff;
}
.enc-button--secondary {
  background: rgba(15, 23, 42, 0.08);
  color: var(--navy);
}
</style>
@endpush
