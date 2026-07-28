@extends('admin.layouts.app')

@section('title', 'Logo & Favicon')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <form method="POST" action="{{ route('admin.settings.logo.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Logo & Favicon</h1>
                <p class="text-xs text-gray-500 mt-0.5">Upload your application branding logo and browser favicon icon.</p>
            </div>
            <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-[var(--theme-primary)] hover:opacity-90 rounded-lg transition-all shadow-sm font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Save Changes
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Logo Card --}}
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex flex-col">
                <h2 class="text-sm font-semibold text-gray-800 mb-1">System Logo</h2>
                <p class="text-xs text-gray-400 mb-4">Recommended dimensions: 250x80px. Allowed formats: PNG, JPG, SVG.</p>

                <div class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-6 bg-gray-50/50 min-h-[160px]">
                    @php
                        $settings = get_setting('logo_favicon');
                        $logo = $settings['logo'] ?? null;
                        $favicon = $settings['favicon'] ?? null;
                    @endphp
                    <label for="logo-input" class="flex flex-col items-center justify-center cursor-pointer text-center group w-full h-full">
                        <div id="logo-preview-container" class="relative {{ $logo ? '' : 'hidden' }}">
                            <img id="logo-preview-img" src="{{ $logo ?? '' }}" alt="App Logo Preview" class="max-h-16 object-contain rounded-lg">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-lg transition-opacity duration-200">
                                <span class="text-white text-xs font-semibold">Change Logo</span>
                            </div>
                        </div>
                        <div id="logo-upload-placeholder" class="flex flex-col items-center justify-center {{ $logo ? 'hidden' : '' }}">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--theme-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Upload Logo</span>
                            <span class="text-[10px] text-gray-400 mt-1">Drag and drop file here</span>
                        </div>
                    </label>
                    <input type="file" id="logo-input" name="logo" accept="image/*" class="hidden" onchange="previewFile(this, 'logo')">
                </div>
            </div>

            {{-- Favicon Card --}}
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex flex-col">
                <h2 class="text-sm font-semibold text-gray-800 mb-1">System Favicon</h2>
                <p class="text-xs text-gray-400 mb-4">Recommended dimensions: 32x32px. Allowed formats: ICO, PNG.</p>

                <div class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-6 bg-gray-50/50 min-h-[160px]">
                    <label for="favicon-input" class="flex flex-col items-center justify-center cursor-pointer text-center group w-full h-full">
                        <div id="favicon-preview-container" class="relative {{ $favicon ? '' : 'hidden' }}">
                            <img id="favicon-preview-img" src="{{ $favicon ?? '' }}" alt="App Favicon Preview" class="w-10 h-10 object-contain rounded">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded transition-opacity duration-200">
                                <span class="text-white text-[8px] font-semibold">Change</span>
                            </div>
                        </div>
                        <div id="favicon-upload-placeholder" class="flex flex-col items-center justify-center {{ $favicon ? 'hidden' : '' }}">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--theme-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Upload Favicon</span>
                            <span class="text-[10px] text-gray-400 mt-1">Drag and drop file here</span>
                        </div>
                    </label>
                    <input type="file" id="favicon-input" name="favicon" accept="image/*" class="hidden" onchange="previewFile(this, 'favicon')">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewFile(input, type) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(type + '-preview-img').src = e.target.result;
                document.getElementById(type + '-preview-container').classList.remove('hidden');
                document.getElementById(type + '-upload-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
