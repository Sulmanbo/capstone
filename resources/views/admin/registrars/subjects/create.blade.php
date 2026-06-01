@extends('layouts.admin')

@section('title', 'Add Subject')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Add Subject</h1>
    <p class="text-gray-600 mt-1">Add a new subject to the master registry</p>
</div>

<div class="bg-white rounded-lg shadow max-w-2xl">
    <form action="{{ route('admin.subjects.store') }}" method="POST" class="p-8">
        @csrf

        <!-- Subject Code -->
        <div class="mb-6">
            <label for="subject_code" class="block text-sm font-semibold text-gray-700 mb-2">
                Subject Code <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="subject_code" 
                   name="subject_code"
                   placeholder="e.g., MTH101, ENG101"
                   value="{{ old('subject_code') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('subject_code') ? 'border-red-500' : '' }}">
            <p class="text-gray-500 text-sm mt-2">Must be unique and immutable</p>
            @error('subject_code')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject Name -->
        <div class="mb-6">
            <label for="subject_name" class="block text-sm font-semibold text-gray-700 mb-2">
                Subject Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="subject_name" 
                   name="subject_name"
                   placeholder="e.g., Mathematics, English Language"
                   value="{{ old('subject_name') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('subject_name') ? 'border-red-500' : '' }}">
            @error('subject_name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Year Level (target grade — used by the schedule cascading dropdown) -->
        <div class="mb-6">
            <label for="year_level" class="block text-sm font-semibold text-gray-700 mb-2">
                Year Level
                <span class="text-gray-500 font-normal text-xs">(controls which sections this subject appears for)</span>
            </label>
            <select id="year_level"
                    name="year_level"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('year_level') ? 'border-red-500' : '' }}">
                <option value="">— Any / Not Specified —</option>
                @foreach(['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'] as $lvl)
                    <option value="{{ $lvl }}" {{ old('year_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                @endforeach
            </select>
            @error('year_level')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Description
            </label>
            <textarea id="description" 
                      name="description"
                      rows="4"
                      placeholder="Optional detailed description of the subject"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('description') ? 'border-red-500' : '' }}">{{ old('description') }}</textarea>
            @error('description')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Credits -->
        <div class="mb-6">
            <label for="credits" class="block text-sm font-semibold text-gray-700 mb-2">
                Credit Hours
            </label>
            <input type="number" 
                   id="credits" 
                   name="credits"
                   value="{{ old('credits') }}"
                   min="1"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('credits') ? 'border-red-500' : '' }}">
            @error('credits')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                Status <span class="text-red-500">*</span>
            </label>
            <select id="status" 
                    name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('status') ? 'border-red-500' : '' }}">
                <option value="">Select Status</option>
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <p class="text-gray-500 text-sm mt-2">Only active subjects can be assigned to curricula</p>
            @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Custom Grade Weights -->
        <div class="mb-6 border border-gray-200 rounded-lg p-5" id="weights-section">
            <label class="flex items-center gap-3 cursor-pointer mb-4">
                <input type="checkbox" name="use_custom_weights" id="use_custom_weights" value="1"
                       {{ old('use_custom_weights') ? 'checked' : '' }}
                       onchange="toggleWeights(this.checked)"
                       class="w-4 h-4 text-blue-600 rounded">
                <span class="text-sm font-semibold text-gray-700">Use Custom Grade Weights</span>
                <span class="text-xs text-gray-400">(overrides global DepEd formula)</span>
            </label>
            <div id="weight-inputs" style="{{ old('use_custom_weights') ? '' : 'display:none;' }}">
                <p class="text-xs text-gray-500 mb-3">Percentages must sum to exactly 100%.</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Written Works %</label>
                        <input type="number" name="ww_weight" id="ww_weight"
                               value="{{ old('ww_weight', 30) }}" min="1" max="98" step="0.01"
                               oninput="updateWeightSum()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Performance Tasks %</label>
                        <input type="number" name="pt_weight" id="pt_weight"
                               value="{{ old('pt_weight', 50) }}" min="1" max="98" step="0.01"
                               oninput="updateWeightSum()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Quarterly Assessment %</label>
                        <input type="number" name="qa_weight" id="qa_weight"
                               value="{{ old('qa_weight', 20) }}" min="1" max="98" step="0.01"
                               oninput="updateWeightSum()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                    </div>
                </div>
                <div id="weight-sum-display" class="mt-3 text-sm font-semibold"></div>
            </div>
            @error('weights')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Note -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">
                <strong>Subject ID:</strong> A unique, immutable identifier will be automatically assigned to this subject.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Create Subject
            </button>
            <a href="{{ route('admin.subjects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
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
