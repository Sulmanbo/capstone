@extends('layouts.admin')

@section('title', 'Grading Quarters')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Grading Quarters</h1>
        <p class="text-gray-600 mt-1">Manage academic quarters within academic years</p>
    </div>
    <a href="{{ route('admin.grading-quarters.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
        + Add Quarter
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <form method="GET" action="{{ route('admin.grading-quarters.index') }}" class="flex gap-4 flex-wrap">
        <select name="academic_year_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-transparent">
            <option value="">All Academic Years</option>
            @foreach($academicYears as $year)
            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                {{ $year->year_label }}
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

<!-- Quarters Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($quarters->count() > 0)
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Academic Year</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Quarter</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Period</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($quarters as $quarter)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-semibold text-gray-900">{{ $quarter->academicYear->year_label }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="font-medium text-gray-900">{{ $quarter->quarter_name }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $quarter->start_date->format('M d') }} - {{ $quarter->end_date->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $quarter->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        @if($quarter->status === 'active')
                        <span class="animate-pulse mr-2">●</span>
                        @endif
                        {{ ucfirst($quarter->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.grading-quarters.edit', $quarter) }}" 
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                    <form method="POST" action="{{ route('admin.grading-quarters.destroy', $quarter) }}" 
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p>No grading quarters found</p>
    </div>
    @endif
</div>

<!-- Pagination -->
@if($quarters->hasPages())
<div class="mt-6">
    {{ $quarters->links('pagination::tailwind') }}
</div>
@endif
@endsection
