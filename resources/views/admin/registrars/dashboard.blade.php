@extends('layouts.admin')

@section('title', 'Registrar Dashboard')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white mb-8 rounded-lg shadow-lg overflow-hidden">
    <!-- Welcome Header Banner -->
    <div class="p-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold mb-2">Welcome, {{ $registrar->first_name }}!</h1>
                <p class="text-blue-100 text-lg">Registrar Module - Academic & Records Management</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-semibold">{{ $systemInfo['current_timestamp']->format('M d, Y') }}</div>
                <div class="text-blue-100">{{ $systemInfo['current_timestamp']->format('H:i:s A') }}</div>
            </div>
        </div>
        
        <!-- Academic Year Display -->
        <div class="mt-6 pt-6 border-t border-blue-500 border-opacity-50 flex justify-between items-center">
            <div>
                <span class="text-blue-200 text-sm">Current Academic Year:</span>
                <p class="text-2xl font-bold">
                    @if($systemInfo['academic_year'])
                        {{ $systemInfo['academic_year']->year_label }}
                        <span class="text-sm bg-blue-500 px-3 py-1 rounded-full ml-2">Active</span>
                    @else
                        <span class="text-yellow-300">No active academic year</span>
                    @endif
                </p>
            </div>
            <div>
                <span class="text-blue-200 text-sm">Current Quarter:</span>
                <p class="text-2xl font-bold">
                    @if($systemInfo['quarter'])
                        {{ $systemInfo['quarter']->quarter_name }}
                    @else
                        <span class="text-yellow-300">Not assigned</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Toolbar -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach($quickActions as $action)
    <a href="{{ route($action['route']) }}" 
       class="group bg-white rounded-lg shadow hover:shadow-lg transition duration-300 p-6 border-l-4 hover:border-l-{{$action['color']}}-600"
       style="border-left-color: {{ match($action['color']) {
            'blue' => '#2563eb',
            'green' => '#16a34a',
            'red' => '#dc2626',
            'purple' => '#9333ea',
            default => '#6b7280'
        } }}">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-{{ $action['color'] }}-600">
                    {{ $action['title'] }}
                </h3>
                <p class="text-gray-600 text-sm">{{ $action['description'] }}</p>
            </div>
            <div class="text-{{ $action['color'] }}-600 group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7m0 0l-7 7m7-7H5" />
                </svg>
            </div>
        </div>
    </a>
    @endforeach
</div>

<!-- Statistical Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Grade Submission Rate -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-700 font-semibold">Grade Submission Rate</h3>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-900">{{ $stats['grade_submission_rate'] }}%</span>
            <span class="ml-2 text-green-600 text-sm font-semibold">↑ On Track</span>
        </div>
        <p class="text-gray-500 text-xs mt-2">Sections with finalized grades</p>
    </div>

    <!-- Pending Unlock Requests -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-700 font-semibold">Pending Unlocks</h3>
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1C6.48 1 2 5.48 2 11s4.48 10 10 10 10-4.48 10-10S17.52 1 12 1zm1 15h-2v-2h2v2zm0-4h-2V7h2v5z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-900">{{ $stats['pending_unlocks'] }}</span>
            <span class="ml-2 text-gray-500 text-sm">Request(s)</span>
        </div>
        <p class="text-gray-500 text-xs mt-2">Awaiting approval</p>
    </div>

    <!-- Total Active Students -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-700 font-semibold">Active Students</h3>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-900">{{ $stats['total_active_students'] }}</span>
            <span class="ml-2 text-gray-500 text-sm">Enrolled</span>
        </div>
        <p class="text-gray-500 text-xs mt-2">Currently active in system</p>
    </div>

    <!-- Active Curriculum Entries -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-700 font-semibold">Curriculum</h3>
            <div class="bg-purple-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 2c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H6zm6 6l6 4-6 4V8z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline">
            <span class="text-3xl font-bold text-gray-900">{{ $stats['total_curriculum_entries'] }}</span>
            <span class="ml-2 text-gray-500 text-sm">Mappings</span>
        </div>
        <p class="text-gray-500 text-xs mt-2">Subject-to-grade assignments</p>
    </div>
</div>

<!-- Academic Feed -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content (Feed) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Academic Feed</h2>
                <p class="text-gray-600 text-sm">Recent updates on sections with finalized grades</p>
            </div>
            
            <div class="divide-y max-h-96 overflow-y-auto">
                @forelse($academicFeed as $item)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($item['type'] === 'curriculum_update')
                            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            @else
                            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-medium text-gray-900">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $item['message'] }}</p>
                            <div class="flex items-center mt-2 text-xs text-gray-500">
                                <time>{{ $item['timestamp']->diffForHumans() }}</time>
                                <span class="mx-2">•</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($item['status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-2">No recent academic updates</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- System Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-900 mb-4">System Information</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-600">Total Academic Years:</span>
                    <p class="font-semibold text-gray-900">{{ $systemInfo['total_academic_years'] }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Active Subjects:</span>
                    <p class="font-semibold text-gray-900">{{ $systemInfo['total_subjects'] }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Grade Levels:</span>
                    <p class="font-semibold text-gray-900">{{ $stats['grade_levels']->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Grade Levels Overview -->
        @if($stats['grade_levels']->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-900 mb-4">Grade Levels Configured</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach($stats['grade_levels'] as $level)
                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full">
                    {{ $level }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-900 mb-4">Quick Links</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.academic-years.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        → Manage Academic Years
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.grading-quarters.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        → Manage Grading Quarters
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.subjects.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        → Manage Subjects
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.curriculum-mappings.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        → Curriculum Mapping
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
