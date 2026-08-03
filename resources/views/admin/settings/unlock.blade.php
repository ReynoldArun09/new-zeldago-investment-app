@extends('admin.layouts.app')

@section('title', 'Unlock Admin Settings')

@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col items-center">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-3">
                <i class="ph ph-lock-key text-2xl"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Unlock Settings</h2>
            <p class="text-sm text-gray-500 mt-1 text-center">
                For security reasons, please confirm your password to access the system settings.
            </p>
        </div>

        <form action="{{ route('admin.settings.unlock.submit') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            @if($errors->any())
                <div class="p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Your Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="ph ph-password"></i>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autofocus
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-sm"
                        placeholder="Enter your admin password"
                    >
                </div>
            </div>

            <button type="submit" class="w-full bg-[var(--theme-primary)] hover:opacity-90 text-white font-medium py-2.5 rounded-lg text-sm transition-all shadow-sm">
                Unlock Settings
            </button>
            
            <div class="text-center mt-4">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Cancel and return to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
