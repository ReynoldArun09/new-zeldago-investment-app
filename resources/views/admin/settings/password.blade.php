@extends('admin.layouts.app')

@section('title', 'Password Setting')

@section('content')
<div class="w-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Password Setting</h1>
            <p class="text-xs text-gray-500 mt-0.5">Change your admin account secure password details.</p>
        </div>
        <a href="{{ route('admin.profile') }}"
            style="background-color: var(--theme-primary);"
            class="flex items-center gap-2 px-3 py-1.5 rounded-none text-xs font-semibold text-white transition-all shadow-sm hover:opacity-90">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profile Setting
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        @csrf
        {{-- Profile Sidebar --}}
        <div class="lg:col-span-4 bg-white border-none rounded-none overflow-hidden shadow-sm flex flex-col h-fit">
            <div class="p-4 flex flex-col items-center justify-center text-center text-white" style="background-color: var(--theme-primary);">
                <div class="w-16 h-16 rounded-full border-2 border-white/30 overflow-hidden flex items-center justify-center bg-white/10 mb-3">
                    @if($admin->avatar_url)
                        <img src="{{ $admin->avatar_url }}" alt="Admin" class="w-full h-full object-cover">
                    @else
                        <span class="text-xl font-bold text-white uppercase">{{ $admin->name[0] }}</span>
                    @endif
                </div>
                <h3 class="font-semibold text-sm">{{ $admin->name }}</h3>
                <span class="text-[10px] text-white/70 font-medium px-2 py-0.5 rounded-full bg-white/15 mt-1">
                    Super Admin
                </span>
            </div>

            <div class="p-4 space-y-3">
                <div class="flex justify-between items-center py-1.5 border-b border-gray-100 text-xs">
                    <span class="text-gray-500 font-medium">Name</span>
                    <span class="text-gray-800 font-semibold">{{ $admin->name }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-gray-100 text-xs">
                    <span class="text-gray-500 font-medium">Username</span>
                    <span class="text-gray-800 font-semibold">admin</span>
                </div>
                <div class="flex justify-between items-center py-1.5 text-xs">
                    <span class="text-gray-500 font-medium">Email</span>
                    <span class="text-gray-800 font-semibold truncate max-w-[150px]">{{ $admin->email }}</span>
                </div>
            </div>
        </div>

        {{-- Change Password Form --}}
        <div class="lg:col-span-8 bg-white border-none rounded-none p-4 shadow-sm space-y-4">
            <h2 class="text-sm font-semibold text-gray-800 mb-2">Change Password</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="oldPassword" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="newPassword" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="confirmPassword" required oninput="validatePasswords()"
                        class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                    <span id="password-match-error" class="text-[10px] text-rose-500 mt-1 hidden">Passwords do not match</span>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" id="submit-btn"
                    style="background-color: var(--theme-primary);"
                    class="w-full py-2 text-xs font-semibold text-white rounded-none transition-all shadow-sm hover:opacity-90">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function validatePasswords() {
        const newPass = document.getElementsByName('newPassword')[0].value;
        const confirmPass = document.getElementById('confirmPassword').value;
        const errorEl = document.getElementById('password-match-error');
        const submitBtn = document.getElementById('submit-btn');

        if (newPass && confirmPass && newPass !== confirmPass) {
            errorEl.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.style.opacity = 0.5;
        } else {
            errorEl.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.style.opacity = 1;
        }
    }
</script>
@endsection
