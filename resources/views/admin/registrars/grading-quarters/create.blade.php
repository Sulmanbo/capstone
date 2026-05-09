@extends('layouts.admin')

@section('title', 'Add Grading Quarter')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Add Grading Quarter</h1>
    <p class="text-gray-600 mt-1">Create a new quarter within an academic year</p>
</div>

<div class="bg-white rounded-lg shadow max-w-2xl">
    <form action="{{ route('admin.grading-quarters.store') }}" method="POST" class="p-8">
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

        <!-- Quarter Number -->
        <div class="mb-6">
            <label for="quarter_number" class="block text-sm font-semibold text-gray-700 mb-2">
                Quarter Number <span class="text-red-500">*</span>
            </label>
            <select id="quarter_number" 
                    name="quarter_number"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('quarter_number') ? 'border-red-500' : '' }}">
                <option value="">Select Quarter</option>
                <option value="1" {{ old('quarter_number') === '1' ? 'selected' : '' }}>1st Quarter</option>
                <option value="2" {{ old('quarter_number') === '2' ? 'selected' : '' }}>2nd Quarter</option>
                <option value="3" {{ old('quarter_number') === '3' ? 'selected' : '' }}>3rd Quarter</option>
                <option value="4" {{ old('quarter_number') === '4' ? 'selected' : '' }}>4th Quarter</option>
            </select>
            @error('quarter_number')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Quarter Name -->
        <div class="mb-6">
            <label for="quarter_name" class="block text-sm font-semibold text-gray-700 mb-2">
                Quarter Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="quarter_name" 
                   name="quarter_name"
                   placeholder="e.g., 1st Quarter"
                   value="{{ old('quarter_name') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('quarter_name') ? 'border-red-500' : '' }}">
            @error('quarter_name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Start Date -->
        <div class="mb-6">
            <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                Start Date <span class="text-red-500">*</span>
            </label>
            <input type="date" 
                   id="start_date" 
                   name="start_date"
                   value="{{ old('start_date') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('start_date') ? 'border-red-500' : '' }}">
            @error('start_date')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- End Date -->
        <div class="mb-6">
            <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                End Date <span class="text-red-500">*</span>
            </label>
            <input type="date" 
                   id="end_date" 
                   name="end_date"
                   value="{{ old('end_date') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('end_date') ? 'border-red-500' : '' }}">
            @error('end_date')
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
            <p class="text-gray-500 text-sm mt-2">
                <strong>Note:</strong> Only one quarter per academic year can be active.
            </p>
            @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Create Quarter
            </button>
            <a href="{{ route('admin.grading-quarters.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
