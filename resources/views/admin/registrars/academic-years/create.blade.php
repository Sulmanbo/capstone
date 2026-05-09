@extends('layouts.admin')

@section('title', 'Add Academic Year')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Add Academic Year</h1>
    <p class="text-gray-600 mt-1">Create a new institutional academic cycle</p>
</div>

<div class="bg-white rounded-lg shadow max-w-2xl">
    <form action="{{ route('admin.academic-years.store') }}" method="POST" class="p-8">
        @csrf

        <!-- Year Label -->
        <div class="mb-6">
            <label for="year_label" class="block text-sm font-semibold text-gray-700 mb-2">
                Year Label <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="year_label" 
                   name="year_label" 
                   placeholder="e.g., 2025-2026"
                   value="{{ old('year_label') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent {{ $errors->has('year_label') ? 'border-red-500' : '' }}">
            @error('year_label')
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
                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <p class="text-gray-500 text-sm mt-2">
                <strong>Note:</strong> Only one academic year can be active at a time. Activating this year will deactivate all others.
            </p>
            @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Create Academic Year
            </button>
            <a href="{{ route('admin.academic-years.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
