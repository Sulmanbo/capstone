@extends('layouts.admin')
@section('title', 'Add Subject')
@section('breadcrumb', 'Subjects / Add')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Add Subject</h1>
      <p class="enc-page__subtitle">Add a new subject to the master registry.</p>
    </div>
    <a href="{{ route('admin.subjects.index') }}" class="enc-btn enc-btn--secondary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      Back to Subjects
    </a>
  </div>
</div>

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:10px;color:#991b1b;font-size:.875rem;">
  @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

<div class="enc-card" style="max-width:680px;">
  <div class="enc-card__body" style="padding:32px;">
    <form action="{{ route('admin.subjects.store') }}" method="POST">
      @csrf

      {{-- Subject Code --}}
      <div style="margin-bottom:22px;">
        <label class="enc-label" for="subject_code">Subject Code <span style="color:var(--danger)">*</span></label>
        <input type="text"
               id="subject_code" name="subject_code"
               placeholder="e.g., MTH101, ENG101"
               value="{{ old('subject_code') }}"
               class="enc-input {{ $errors->has('subject_code') ? 'enc-input--error' : '' }}">
        <div class="enc-field-hint">Must be unique — cannot be changed after creation.</div>
        @error('subject_code')<div class="enc-field-error">{{ $message }}</div>@enderror
      </div>

      {{-- Subject Name --}}
      <div style="margin-bottom:22px;">
        <label class="enc-label" for="subject_name">Subject Name <span style="color:var(--danger)">*</span></label>
        <input type="text"
               id="subject_name" name="subject_name"
               placeholder="e.g., Mathematics, English Language"
               value="{{ old('subject_name') }}"
               class="enc-input {{ $errors->has('subject_name') ? 'enc-input--error' : '' }}">
        @error('subject_name')<div class="enc-field-error">{{ $message }}</div>@enderror
      </div>

      {{-- Year Level --}}
      <div style="margin-bottom:22px;">
        <label class="enc-label" for="year_level">Year Level</label>
        <select id="year_level" name="year_level"
                class="enc-input {{ $errors->has('year_level') ? 'enc-input--error' : '' }}">
          <option value="">— Any / Not Specified —</option>
          @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $lvl)
            <option value="{{ $lvl }}" {{ old('year_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
          @endforeach
        </select>
        <div class="enc-field-hint">Controls which sections this subject appears in for schedules.</div>
        @error('year_level')<div class="enc-field-error">{{ $message }}</div>@enderror
      </div>

      {{-- Status --}}
      <div style="margin-bottom:22px;">
        <label class="enc-label" for="status">Status <span style="color:var(--danger)">*</span></label>
        <select id="status" name="status"
                class="enc-input {{ $errors->has('status') ? 'enc-input--error' : '' }}">
          <option value="">Select Status</option>
          <option value="active"   {{ old('status') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <div class="enc-field-hint">Only active subjects can be assigned to curricula.</div>
        @error('status')<div class="enc-field-error">{{ $message }}</div>@enderror
      </div>

      {{-- Description --}}
      <div style="margin-bottom:22px;">
        <label class="enc-label" for="description">Description</label>
        <textarea id="description" name="description" rows="3"
                  placeholder="Optional detailed description of the subject"
                  class="enc-input {{ $errors->has('description') ? 'enc-input--error' : '' }}"
                  style="resize:vertical;">{{ old('description') }}</textarea>
        @error('description')<div class="enc-field-error">{{ $message }}</div>@enderror
      </div>

      {{-- Credit Hours --}}
      <div style="margin-bottom:22px;">
        <label class="enc-label" for="credits">Credit Hours</label>
        <input type="number"
               id="credits" name="credits"
               value="{{ old('credits') }}"
               min="1" max="20"
               class="enc-input {{ $errors->has('credits') ? 'enc-input--error' : '' }}"
               style="max-width:160px;">
        @error('credits')<div class="enc-field-error">{{ $message }}</div>@enderror
      </div>

      {{-- Custom Grade Weights --}}
      <div style="margin-bottom:28px;border:1.5px solid var(--gray-200);border-radius:12px;padding:20px 22px;">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:0;">
          <input type="checkbox" name="use_custom_weights" id="use_custom_weights" value="1"
                 {{ old('use_custom_weights') ? 'checked' : '' }}
                 onchange="toggleWeights(this.checked)"
                 style="width:16px;height:16px;accent-color:var(--navy);cursor:pointer;">
          <span style="font-size:.875rem;font-weight:700;color:var(--navy);">Use Custom Grade Weights</span>
          <span style="font-size:.72rem;color:var(--gray-400);">(overrides global DepEd 40-40-20 formula)</span>
        </label>

        <div id="weight-inputs" style="{{ old('use_custom_weights') ? '' : 'display:none;' }} margin-top:18px;">
          <div style="font-size:.78rem;color:var(--gray-400);margin-bottom:14px;">Percentages must sum to exactly 100%.</div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
            <div>
              <label class="enc-label">Written Works %</label>
              <input type="number" name="ww_weight" id="ww_weight"
                     value="{{ old('ww_weight', 30) }}" min="1" max="98" step="0.01"
                     oninput="updateWeightSum()" class="enc-input">
            </div>
            <div>
              <label class="enc-label">Performance Tasks %</label>
              <input type="number" name="pt_weight" id="pt_weight"
                     value="{{ old('pt_weight', 50) }}" min="1" max="98" step="0.01"
                     oninput="updateWeightSum()" class="enc-input">
            </div>
            <div>
              <label class="enc-label">Quarterly Assessment %</label>
              <input type="number" name="qa_weight" id="qa_weight"
                     value="{{ old('qa_weight', 20) }}" min="1" max="98" step="0.01"
                     oninput="updateWeightSum()" class="enc-input">
            </div>
          </div>
          <div id="weight-sum-display" style="margin-top:10px;font-size:.85rem;font-weight:700;"></div>
        </div>
        @error('weights')<div class="enc-field-error" style="margin-top:8px;">{{ $message }}</div>@enderror
      </div>

      {{-- Actions --}}
      <div style="display:flex;gap:12px;padding-top:4px;">
        <button type="submit" class="enc-btn enc-btn--primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
          </svg>
          Create Subject
        </button>
        <a href="{{ route('admin.subjects.index') }}" class="enc-btn enc-btn--secondary">Cancel</a>
      </div>

    </form>
  </div>
</div>

@push('scripts')
<script>
function toggleWeights(enabled) {
  document.getElementById('weight-inputs').style.display = enabled ? '' : 'none';
  if (enabled) updateWeightSum();
}
function updateWeightSum() {
  const ww  = parseFloat(document.getElementById('ww_weight').value) || 0;
  const pt  = parseFloat(document.getElementById('pt_weight').value) || 0;
  const qa  = parseFloat(document.getElementById('qa_weight').value) || 0;
  const sum = Math.round((ww + pt + qa) * 100) / 100;
  const el  = document.getElementById('weight-sum-display');
  el.textContent = 'Current sum: ' + sum + '%';
  el.style.color = Math.abs(sum - 100) < 0.01 ? '#16a34a' : '#dc2626';
}
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('use_custom_weights').checked) updateWeightSum();
});
</script>
@endpush
@endsection
