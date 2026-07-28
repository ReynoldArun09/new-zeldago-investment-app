@extends('admin.layouts.app')

@section('title', 'ROI Settings')

@section('content')
<div class="w-full">
    {{-- Header --}}
    @php
        $roi = \App\Models\Setting::where('key', 'roi_settings')->first();
        $settings = $roi ? $roi->value : [];
    @endphp
    <form method="POST" action="{{ route('admin.settings.roi.update') }}">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">ROI Settings</h1>
                <p class="text-xs text-gray-500 mt-0.5">Configure Return on Investment rules for user investments.</p>
            </div>
            <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-[var(--theme-primary)] hover:opacity-90 rounded-lg transition-all shadow-sm font-semibold">
                <i class="ph ph-floppy-disk text-lg"></i>
                Save Changes
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm font-medium">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-3">ROI Type</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="roi_type[]" value="manual" class="text-[var(--theme-primary)] focus:ring-[var(--theme-primary)] w-4 h-4 rounded border-gray-300" {{ in_array('manual', old('roi_type', $settings['roi_type'] ?? ['manual'])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Manual</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="roi_type[]" value="auto" class="text-[var(--theme-primary)] focus:ring-[var(--theme-primary)] w-4 h-4 rounded border-gray-300" {{ in_array('auto', old('roi_type', $settings['roi_type'] ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Auto</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">Determine if ROI is distributed automatically by the system or manually by an admin.</p>
            </div>
            
            <div class="mb-4">
                <label for="cycle_days" class="block text-sm font-semibold text-gray-700 mb-2">Cycle Days</label>
                <input type="number" id="cycle_days" name="cycle_days" value="{{ old('cycle_days', $settings['cycle_days'] ?? 1) }}" class="block w-full max-w-sm px-4 py-2.5 text-sm border @error('cycle_days') border-red-500 @else border-gray-200 @enderror rounded-lg focus:outline-none focus:ring-1 focus:ring-[var(--theme-primary)] focus:border-[var(--theme-primary)] transition-colors" required min="1">
                <p class="text-xs text-gray-500 mt-1">Days between ROI credits.</p>
            </div>
        </div>
    </form>
</div>
@endsection
