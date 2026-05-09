@extends('layouts.admin')

@section('title', 'Subjects Registry')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Subjects Registry</h1>
        <p class="text-gray-600 mt-1">Master database of all subjects offered</p>
    </div>
    <a href="{{ route('admin.subjects.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
        + Add Subject
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex gap-4 flex-wrap">
        <div class="flex-1 min-w-64">
            <input type="text" name="search" placeholder="Search by code or name..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
        </div>
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

<!-- Subjects Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($subjects->count() > 0)
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subject Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subject ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Credits</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($subjects as $subject)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-semibold text-gray-900">{{ $subject->subject_code }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <code class="text-xs bg-gray-100 px-3 py-1 rounded text-gray-600">{{ $subject->subject_id }}</code>
                </td>
                <td class="px-6 py-4">
                    <span class="text-gray-900">{{ $subject->subject_name }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $subject->credits ?? '—' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subject->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($subject->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.subjects.show', $subject) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                    <a href="{{ route('admin.subjects.edit', $subject) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium ml-3">Edit</a>
                    @if(!$subject->isUsedInCurriculum())
                    <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" 
                          class="inline-block ml-3"
                          onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                    </form>
                    @endif
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
        <p>No subjects found</p>
    </div>
    @endif
</div>

<!-- Pagination -->
@if($subjects->hasPages())
<div class="mt-6">
    {{ $subjects->links('pagination::tailwind') }}
</div>
@endif
@endsection
