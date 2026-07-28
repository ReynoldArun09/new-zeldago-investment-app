@extends('admin.layouts.app')

@section('title', 'All Nominee Submissions')

@section('content')
<div class="w-full">
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Main Box --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-800">All Nominee Submissions</h2>
            <form action="{{ route('admin.verification.nominee') }}" method="GET" class="flex">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="User or Nominee Name" class="w-full md:w-64 px-4 py-2 text-sm border border-gray-200 rounded-l-lg focus:outline-none focus:border-[var(--theme-primary)]">
                <button type="submit" class="px-4 py-2 bg-[var(--theme-primary)] text-white rounded-r-lg hover:opacity-90 transition-opacity">
                    <i class="ph ph-magnifying-glass"></i>
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-white">
                <thead class="bg-[var(--theme-primary)] font-medium">
                    <tr>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Nominee Name</th>
                        <th class="px-6 py-3.5">Relation</th>
                        <th class="px-6 py-3.5">Submitted</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-100">
                    @forelse($nominees as $nominee)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $nominee->user->name }}</div>
                                <a href="{{ route('admin.users.details', $nominee->user->username) }}" class="text-xs text-[var(--theme-primary)] hover:underline">{{ '@' . $nominee->user->username }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-700">{{ $nominee->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border border-blue-200 text-blue-600 bg-white">
                                    {{ $nominee->relation }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $nominee->created_at->format('m/d/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($nominee->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-amber-500 border border-amber-300">
                                        Pending
                                    </span>
                                @elseif($nominee->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-emerald-500 border border-emerald-300">
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-red-500 border border-red-300">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.verification.nominee.review', $nominee->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-indigo-200 rounded-md text-xs font-medium text-[var(--theme-primary)] bg-white hover:bg-indigo-50 focus:outline-none transition-colors">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                No Nominee documents found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($nominees->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $nominees->firstItem() ?? 0 }} to {{ $nominees->lastItem() ?? 0 }} of {{ $nominees->total() }} results
                </span>
                <div>
                    {{ $nominees->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $nominees->count() }} to {{ $nominees->count() }} of {{ $nominees->count() }} results
                </span>
                <div class="flex gap-1">
                    <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&lt;</button>
                    <button class="px-3 py-1 border border-[var(--theme-primary)] rounded-md bg-[var(--theme-primary)] text-white text-sm">1</button>
                    <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&gt;</button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
