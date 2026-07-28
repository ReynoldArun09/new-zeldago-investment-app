@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="w-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Profile</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manage your account profile information.</p>
        </div>
        <a href="{{ route('admin.password') }}"
            style="background-color: var(--theme-primary);"
            class="flex items-center gap-2 px-3 py-1.5 rounded-none text-xs font-semibold text-white transition-all shadow-sm hover:opacity-90">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Password Setting
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        @csrf
        {{-- Profile Sidebar --}}
        <div class="lg:col-span-4 bg-white border-none rounded-none overflow-hidden shadow-sm flex flex-col h-fit">
            <div class="p-4 flex flex-col items-center justify-center text-center text-white" style="background-color: var(--theme-primary);">
                <div class="w-16 h-16 rounded-full border-2 border-white/30 overflow-hidden flex items-center justify-center bg-white/10 mb-3">
                    @if($admin->avatar_url)
                        <img id="avatar-sidebar-img" src="{{ $admin->avatar_url }}" alt="Admin" class="w-full h-full object-cover">
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

        {{-- Edit Info Form --}}
        <div class="lg:col-span-8 bg-white border-none rounded-none p-4 shadow-sm space-y-4">
            <h2 class="text-sm font-semibold text-gray-800 mb-2">Profile Information</h2>

            <div class="flex flex-col sm:flex-row gap-6">
                {{-- Avatar uploader --}}
                <div class="flex flex-col items-center justify-center shrink-0">
                    <span class="block text-xs font-semibold text-gray-600 self-start mb-2">Image</span>
                    <label for="avatar-input" class="relative w-36 h-36 border-2 border-dashed border-gray-200 rounded-none bg-gray-50/50 flex flex-col items-center justify-center cursor-pointer overflow-hidden group">
                        <div id="avatar-preview-container" class="{{ $admin->avatar_url ? '' : 'hidden' }} w-full h-full">
                            <img id="avatar-preview-img" src="{{ $admin->avatar_url ?? '' }}" alt="Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                                <span class="text-white text-[10px] font-semibold">Change</span>
                            </div>
                        </div>
                        <div id="avatar-upload-placeholder" class="{{ $admin->avatar_url ? 'hidden' : '' }} text-center p-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-2 text-[#3b5fc0]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <span class="text-[10px] text-gray-400 font-medium">Upload Image</span>
                        </div>
                    </label>
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    <span class="text-[9px] text-gray-400 mt-2 max-w-[150px] text-center">
                        Supported Files: <b>.png, .jpg, .jpeg</b>. Image will be resized into <b>400x400px</b>.
                    </span>
                </div>

                {{-- Inputs --}}
                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ $admin->name }}" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ $admin->email }}" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    style="background-color: var(--theme-primary);"
                    class="w-full py-2 text-xs font-semibold text-white rounded-none transition-all shadow-sm hover:opacity-90">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewAvatar(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview-img').src = e.target.result;
                document.getElementById('avatar-preview-container').classList.remove('hidden');
                document.getElementById('avatar-upload-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
