@extends('admin.layouts.app')

@section('title', 'SEO Configuration')

@section('content')
@php
    $seo = get_setting('seo_configuration') ?? [];
@endphp
<div class="w-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-bold text-gray-800">SEO Configuration</h1>
            <p class="text-xs text-gray-500 mt-0.5">Configure proper meta tags, descriptions, and keywords to make the system search engine friendly.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.seo.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- SEO Image --}}
                <div class="lg:col-span-4 flex flex-col">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">SEO Image</label>
                    <div class="relative border border-gray-200 rounded-lg p-2 bg-gray-50/50 flex flex-col items-center justify-center min-h-[220px]">
                        @if(!empty($seo['seoImage']))
                            <div class="relative w-full h-full min-h-[200px] flex items-center justify-center">
                                <img src="{{ $seo['seoImage'] }}" alt="SEO Preview" class="max-h-48 object-contain rounded-md">
                            </div>
                        @endif
                        <label class="flex flex-col items-center justify-center cursor-pointer text-center group mt-3">
                            <span class="text-xs font-semibold text-gray-700 hover:text-[var(--theme-primary)]">Upload New Image</span>
                            <input type="file" name="seoImage" accept="image/*" class="hidden">
                        </label>
                    </div>
                </div>

                {{-- Fields --}}
                <div class="lg:col-span-8 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Meta Keywords</label>
                        <input type="text" name="keywords" value="{{ implode(', ', $seo['keywords'] ?? []) }}" placeholder="Separate by comma"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Meta Robots</label>
                        <input type="text" name="metaRobots" value="{{ $seo['metaRobots'] ?? '' }}" placeholder="e.g. noindex, follow"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Meta Description</label>
                        <textarea name="metaDescription" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">{{ $seo['metaDescription'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Social Title</label>
                        <input type="text" name="socialTitle" value="{{ $seo['socialTitle'] ?? '' }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Social Description</label>
                        <textarea name="socialDescription" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-800 outline-none focus:border-[var(--theme-primary)] transition-colors bg-[#fafafa]">{{ $seo['socialDescription'] ?? '' }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm text-white bg-[var(--theme-primary)] hover:opacity-90 rounded-lg transition-all shadow-sm font-semibold">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
