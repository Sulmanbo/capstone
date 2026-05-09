@extends('layouts.admin')

@section('title', 'Curriculum Mapping')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Curriculum Mapping</h1>
        <p class="text-gray-600 mt-1">Define subjects required for each grade level</p>
    </div>
    <a href="{{ route('admin.curriculum-mappings.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
        + Add Mapping
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <form method="GET" action="{{ route('admin.curriculum-mappings.index') }}" class="flex gap-4 flex-wrap">
        <select name="academic_year_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Academic Years</option>
            @foreach($academicYears as $year)
            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                {{ $year->year_label }}
            </option>
            @endforeach
        </select>
        <select name="grade_level" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Grade Levels</option>
            @foreach($gradeLevels as $level)
            <option value="{{ $level }}" {{ request('grade_level') === $level ? 'selected' : '' }}>
                {{ $level }}
            </option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-lg transition">
            Filter
        </button>
    </form>
</div>

<!-- Curriculum Mappings Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($mappings->count() > 0)
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Academic Year</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Grade Level</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($mappings as $mapping)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="font-semibold text-gray-900">{{ $mapping->academicYear->year_label }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $mapping->grade_level }}
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm">
                        <span class="font-semibold text-gray-900">{{ $mapping->subject->subject_code }}</span>
                        <p class="text-gray-600">{{ $mapping->subject->subject_name }}</p>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $mapping->is_required ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $mapping->is_required ? 'Required' : 'Elective' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $mapping->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($mapping->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.curriculum-mappings.edit', $mapping) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form method="POST" action="{{ route('admin.curriculum-mappings.destroy', $mapping) }}" 
                          class="inline-block ml-3"
                          onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="p-6 text-center text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
        <p>No curriculum mappings found</p>
    </div>
    @endif
</div>

<!-- Pagination -->
@if($mappings->hasPages())
<div class="mt-6">
    {{ $mappings->links('pagination::tailwind') }}
</div>
@endif
@endsection
