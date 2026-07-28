@extends('admin.layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="min-h-full p-4 sm:p-6" style="background-color: var(--theme-bg);">
    <div class="mb-5 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-700">Add New User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium hover:underline" style="color: var(--theme-primary);">
            Back to Users
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 border border-green-200 text-sm px-4 py-3 rounded-none mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-4 py-3 rounded-none mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 text-red-700 border border-red-200 text-sm px-4 py-3 rounded-none mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-none shadow-sm p-5 sm:p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Basic Information --}}
                <div class="sm:col-span-2">
                    <h2 class="text-sm font-semibold text-gray-800 border-b pb-2 mb-4">Basic Information</h2>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>

                {{-- Information --}}
                <div class="sm:col-span-2 mt-4">
                    <h2 class="text-sm font-semibold text-gray-800 border-b pb-2 mb-4">Information</h2>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Account Type <span class="text-red-500">*</span></label>
                    <select name="account_type" id="account_type" required
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors bg-white">
                        <option value="Normal User" {{ old('account_type') == 'Normal User' ? 'selected' : '' }}>Under Existing Sponsor</option>
                        <option value="Root Distributor" {{ old('account_type') == 'Root Distributor' ? 'selected' : '' }}>Root Distributor</option>
                    </select>
                </div>
                
                <div id="sponsor_field_container">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Sponsor (Username or Referral Code) <span class="text-red-500">*</span></label>
                    <input type="text" name="sponsor" id="sponsor" value="{{ old('sponsor') }}"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors"
                        placeholder="Enter Sponsor Username or Code">
                    <p id="sponsor_name_display" class="text-sm mt-1"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90 rounded-none" style="background-color: var(--theme-primary);">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accountTypeSelect = document.getElementById('account_type');
        const sponsorFieldContainer = document.getElementById('sponsor_field_container');
        const sponsorInput = document.getElementById('sponsor');

        function toggleSponsorField() {
            if (accountTypeSelect.value === 'Root Distributor') {
                sponsorFieldContainer.style.display = 'none';
                sponsorInput.removeAttribute('required');
                sponsorInput.value = '';
            } else {
                sponsorFieldContainer.style.display = 'block';
                sponsorInput.setAttribute('required', 'required');
            }
        }

        accountTypeSelect.addEventListener('change', toggleSponsorField);
        toggleSponsorField(); // Initial check

        // Sponsor Lookup
        const sponsorDisplay = document.getElementById('sponsor_name_display');
        let timeoutId;

        function searchSponsor() {
            clearTimeout(timeoutId);
            const val = sponsorInput.value.trim();
            if(val.length === 0) {
                sponsorDisplay.textContent = '';
                return;
            }
            sponsorDisplay.textContent = 'Searching...';
            sponsorDisplay.className = 'text-sm mt-1 text-gray-500 font-medium';
            
            timeoutId = setTimeout(() => {
                fetch(`{{ route('admin.api.users.search') }}?search=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(res => {
                    if(res.success && res.data.length > 0) {
                        const user = res.data.find(u => u.username === val || u.referral_code === val) || res.data[0];
                        sponsorDisplay.textContent = 'Found: ' + user.name + ' (@' + user.username + ')';
                        sponsorDisplay.className = 'text-sm mt-1 text-green-600 font-medium';
                    } else {
                        sponsorDisplay.textContent = 'User not found';
                        sponsorDisplay.className = 'text-sm mt-1 text-red-500 font-medium';
                    }
                }).catch(() => {
                    sponsorDisplay.textContent = 'Error fetching user';
                    sponsorDisplay.className = 'text-sm mt-1 text-red-500 font-medium';
                });
            }, 500);
        }

        sponsorInput.addEventListener('input', searchSponsor);
        if(sponsorInput.value.trim() !== '') {
            searchSponsor();
        }
    });
</script>
@endsection
