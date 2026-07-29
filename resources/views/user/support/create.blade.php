@extends('user.layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
<div class="max-w-3xl mx-auto w-full">
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Create Support Ticket</h1>
        <p class="text-gray-600 mt-2">Submit a new request to our support team.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('user.support.store') }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" name="subject" id="subject" class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors" required placeholder="Brief description of the issue">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select id="priority" name="priority" class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                        <option value="LOW">Low</option>
                        <option value="MEDIUM" selected>Medium</option>
                        <option value="HIGH">High</option>
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="6" class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors" required placeholder="Describe your issue in detail..."></textarea>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('user.support.index') }}" class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center rounded-xl border border-transparent bg-[var(--primary)] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none transition">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
