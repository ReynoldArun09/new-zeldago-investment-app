@extends('user.layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Create Support Ticket</h1>
    <p class="text-gray-600 mt-1">Submit a new request to our support team.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <form action="{{ route('user.support.store') }}" method="POST">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required placeholder="Brief description of the issue">
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select id="priority" name="priority" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="LOW">Low</option>
                    <option value="MEDIUM" selected>Medium</option>
                    <option value="HIGH">High</option>
                </select>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="message" name="message" rows="6" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required placeholder="Describe your issue in detail..."></textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('user.support.index') }}" class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent bg-[var(--primary)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:opacity-90 focus:outline-none transition">
                Submit Ticket
            </button>
        </div>
    </form>
</div>
@endsection
