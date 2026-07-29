@extends('user.layouts.app')

@section('title', 'Password Reset')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Password Reset</h1>
        <p class="text-gray-600 mt-1">Update your password to keep your account secure.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="ph ph-x-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div>
        <form action="{{ route('user.settings.password.update') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-600 mb-1">Current Password <span class="text-red-500">*</span></label>
                <input type="password" name="current_password" id="current_password" required
                    class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-600 mb-1">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required
                        class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-600 mb-1">Confirm New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                    <i class="ph ph-lock-key"></i>
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
