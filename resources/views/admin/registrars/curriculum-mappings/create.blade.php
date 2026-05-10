@extends('layouts.admin')

@section('title', 'Add Curriculum Mapping')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Add Curriculum Mapping</h1>
    <p class="text-gray-600 mt-1">Assign a subject to a grade level</p>
</div>

<div class="bg-white rounded-lg shadow max-w-2xl">
    <form action="{{ route('admin.curriculum-mappings.store') }}" method="POST" class="p-8">
        @csrf

        <!-- Academic Year -->
        <div class="mb-6">
            <label for="academic_year_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Academic Year <span class="text-red-500">*</span>
            </label>
            <select id="academic_year_id" 
                    name="academic_year_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('academic_year_id') ? 'border-red-500' : '' }}">
                <option value="">Select Academic Year</option>
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ old('academic_year_id', $academicYear?->id) == $year->id ? 'selected' : '' }}>
                    {{ $year->year_label }}
                </option>
                @endforeach
            </select>
            @error('academic_year_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Grade Level -->
        <div class="mb-6">
            <label for="grade_level" class="block text-sm font-semibold text-gray-700 mb-2">
                Grade Level <span class="text-red-500">*</span>
            </label>
            <select id="grade_level" 
                    name="grade_level"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('grade_level') ? 'border-red-500' : '' }}">
                <option value="">Select Grade Level</option>
                @foreach($standardGradeLevels as $level)
                <option value="{{ $level }}" {{ old('grade_level', $gradeLevel) === $level ? 'selected' : '' }}>
                    {{ $level }}
                </option>
                @endforeach
            </select>
            @error('grade_level')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject -->
        <div class="mb-6">
            <label for="subject_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Subject <span class="text-red-500">*</span>
            </label>
            <select id="subject_id" 
                    name="subject_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('subject_id') ? 'border-red-500' : '' }}">
                <option value="">Select Subject</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                </option>
                @endforeach
            </select>
            @error('subject_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Prerequisite Subject -->
        <div class="mb-6">
            <label for="prerequisite_subject_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Prerequisite Subject
                <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <select id="prerequisite_subject_id"
                    name="prerequisite_subject_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
                <option value="">— None —</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ old('prerequisite_subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                </option>
                @endforeach
            </select>
            <p class="text-gray-500 text-sm mt-1">Student must have passed this subject in a prior academic year.</p>
            @error('prerequisite_subject_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Prerequisite Min Grade -->
        <div class="mb-6">
            <label for="prerequisite_min_grade" class="block text-sm font-semibold text-gray-700 mb-2">
                Minimum Grade Required
                <span class="text-gray-400 font-normal">(default: 75)</span>
            </label>
            <input type="number"
                   id="prerequisite_min_grade"
                   name="prerequisite_min_grade"
                   value="{{ old('prerequisite_min_grade', 75) }}"
                   min="0" max="100" step="0.01"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            @error('prerequisite_min_grade')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Type (Required/Elective) -->
        <div class="mb-6">
            <label for="is_required" class="block text-sm font-semibold text-gray-700 mb-2">
                Subject Type <span class="text-red-500">*</span>
            </label>
            <select id="is_required" 
                    name="is_required"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('is_required') ? 'border-red-500' : '' }}">
                <option value="">Select Type</option>
                <option value="1" {{ old('is_required') === '1' ? 'selected' : '' }}>Required</option>
                <option value="0" {{ old('is_required') === '0' ? 'selected' : '' }}>Elective</option>
            </select>
            @error('is_required')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Sequence Order -->
        <div class="mb-6">
            <label for="sequence_order" class="block text-sm font-semibold text-gray-700 mb-2">
                Sequence Order
            </label>
            <input type="number" 
                   id="sequence_order" 
                   name="sequence_order"
                   value="{{ old('sequence_order', 0) }}"
                   min="0"
                   placeholder="0 (default)"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('sequence_order') ? 'border-red-500' : '' }}">
            <p class="text-gray-500 text-sm mt-2">Order of subjects within the same grade level</p>
            @error('sequence_order')
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
            @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Create Mapping
            </button>
            <a href="{{ route('admin.curriculum-mappings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
