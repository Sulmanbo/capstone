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
@endsection
