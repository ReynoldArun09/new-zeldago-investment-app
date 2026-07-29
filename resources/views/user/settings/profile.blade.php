@extends('user.layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
        <p class="text-gray-600 mt-1">Update your personal information and contact details.</p>
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
        <form action="{{ route('user.settings.profile.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-6">
                <!-- First & Last Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-600 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-600 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>

                <!-- Email & Mobile Number -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-600 mb-1">Mobile Number <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-sm border border-r-0 border-gray-200 bg-gray-50 text-gray-500 sm:text-sm">
                                +
                            </span>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                                class="flex-1 block w-full px-3 py-2 bg-white border border-gray-200 rounded-none rounded-r-sm focus:ring-primary focus:border-primary sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                        class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                </div>

                <!-- City & State -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-600 mb-1">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}"
                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-600 mb-1">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $user->state) }}"
                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>

                <!-- Zip & Country -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-600 mb-1">Zip / Postal</label>
                        <input type="text" name="zip" id="zip" value="{{ old('zip', $user->zip) }}"
                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-600 mb-1">Country <span class="text-red-500">*</span></label>
                        <select name="country" id="country" required class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-sm focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="">Select country</option>
                            <option value="India" {{ old('country', $user->country) == 'India' ? 'selected' : '' }}>India</option>
                            <option value="United States" {{ old('country', $user->country) == 'United States' ? 'selected' : '' }}>United States</option>
                            <option value="United Kingdom" {{ old('country', $user->country) == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="Australia" {{ old('country', $user->country) == 'Australia' ? 'selected' : '' }}>Australia</option>
                            <option value="Canada" {{ old('country', $user->country) == 'Canada' ? 'selected' : '' }}>Canada</option>
                            <!-- Add more countries if needed -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                    <i class="ph ph-floppy-disk"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
