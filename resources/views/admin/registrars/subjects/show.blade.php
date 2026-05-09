@extends('layouts.admin')

@section('title', 'Subject Details: ' . $subject->subject_name)

@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $subject->subject_name }}</h1>
        <p class="text-gray-600 mt-1">Subject Code: <code class="bg-gray-100 px-2 py-1 rounded">{{ $subject->subject_code }}</code></p>
    </div>
    <a href="{{ route('admin.subjects.edit', $subject) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
        Edit Subject
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Subject Information</h2>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600 text-sm">Subject Code</span>
                        <p class="font-semibold text-gray-900">{{ $subject->subject_code }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Credit Hours</span>
                        <p class="font-semibold text-gray-900">{{ $subject->credits ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <span class="text-gray-600 text-sm block mb-1">Description</span>
                    <p class="text-gray-900">{{ $subject->description ?? 'No description provided' }}</p>
                </div>

                <div>
                    <span class="text-gray-600 text-sm">Status</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $subject->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($subject->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Curriculum Usage -->
        @if($curriculumUsage->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Used in Curriculum</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Academic Year</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Grade Level</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($curriculumUsage as $usage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.academic-years.edit', $usage->academicYear) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $usage->academicYear->year_label }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $usage->grade_level }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $usage->is_required ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $usage->is_required ? 'Required' : 'Elective' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $usage->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($usage->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <p class="text-gray-700">This subject is not currently used in any curriculum mappings.</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Subject ID -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-900 mb-3">Subject ID</h3>
            <code class="block bg-gray-100 text-gray-600 text-sm px-3 py-2 rounded border border-gray-200 break-all">
                {{ $subject->subject_id }}
            </code>
            <p class="text-gray-500 text-xs mt-3">
                This is an immutable, unique identifier assigned at creation.
            </p>
        </div>

        <!-- Metadata -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-900 mb-3">Metadata</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-600">Created:</span>
                    <p class="font-medium text-gray-900">{{ $subject->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Last Updated:</span>
                    <p class="font-medium text-gray-900">{{ $subject->updated_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-900 mb-3">Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.subjects.edit', $subject) }}" 
                   class="block bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-2 px-4 rounded-lg transition">
                    Edit
                </a>
                @if(!$subject->isUsedInCurriculum())
                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" 
                      class="block"
                      onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Delete
                    </button>
                </form>
                @else
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded text-sm text-yellow-800">
                    Cannot delete: Subject is used in curriculum mappings.
                </div>
                @endif
                <a href="{{ route('admin.subjects.index') }}" 
                   class="block bg-gray-300 hover:bg-gray-400 text-gray-800 text-center font-semibold py-2 px-4 rounded-lg transition">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
