@extends('layouts.admin')

@section('title', 'Edit Academic Year')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Edit Academic Year</h1>
    <p class="text-gray-600 mt-1">{{ $academicYear->year_label }}</p>
</div>

<div class="bg-white rounded-lg shadow max-w-2xl">
    <form action="{{ route('admin.academic-years.update', $academicYear) }}" method="POST" class="p-8">
        @csrf
        @method('PUT')

        <!-- Year Label -->
        <div class="mb-6">
            <label for="year_label" class="block text-sm font-semibold text-gray-700 mb-2">
                Year Label <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="year_label" 
                   name="year_label" 
                   value="{{ old('year_label', $academicYear->year_label) }}"
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
                   value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}"
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
                   value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}"
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
                <option value="active" {{ old('status', $academicYear->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $academicYear->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="archived" {{ old('status', $academicYear->status) === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <p class="text-gray-500 text-sm mt-2">
                <strong>Note:</strong> @if($academicYear->status === 'active')
                    This year is currently active. Changing its status will deactivate it.
                @else
                    If you set this to active, all other active years will be automatically deactivated.
                @endif
            </p>
            @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">
                <strong>Associated Quarters:</strong> {{ $academicYear->quarters()->count() }} quarter(s)
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Update Academic Year
            </button>
            <a href="{{ route('admin.academic-years.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
