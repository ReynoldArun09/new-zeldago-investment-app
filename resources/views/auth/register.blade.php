@php
    $theme = get_setting('theme_appearance');
    // Set fallbacks for robustness if settings are incomplete
    $primaryColor = $theme['primaryColor'] ?? '#8b5cf6';
    $sidebarColor = $theme['sidebarColor'] ?? '#1e1e2f';
    $bgColor = $theme['bgColor'] ?? '#f3f4f6';
    $fontFamily = $theme['fontFamily'] ?? 'Inter, sans-serif';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ $theme['appName'] ?? 'One Planet' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --sidebar-bg: {{ $sidebarColor }};
            --body-bg: {{ $bgColor }};
            --font-main: {{ $fontFamily }};
        }
        body {
            font-family: var(--font-main);
            background-color: var(--body-bg);
        }
        .bg-primary {
            background-color: var(--primary);
        }
        .text-primary {
            color: var(--primary);
        }
        .bg-sidebar {
            background-color: var(--sidebar-bg);
        }
        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .focus-ring-primary:focus {
            border-color: {{ $primaryColor }};
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden p-4 sm:p-8">
    <div class="w-full max-w-md relative z-10 my-auto py-8">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 rounded-2xl text-white flex items-center justify-center font-bold text-3xl mx-auto mb-6 shadow-lg" style="background-color: var(--primary);">
                {{ substr($theme['appName'] ?? 'O', 0, 1) }}
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
            <p class="text-gray-500">Join us today</p>
        </div>

        <div class="bg-white rounded-none shadow-xl shadow-gray-200/50 p-8 sm:p-10 border border-gray-100">
            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="sponsor_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sponsor Referral Code (Optional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-users text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" id="sponsor_id" name="sponsor_id" value="{{ old('sponsor_id') }}" class="block w-full pl-10 pr-3 py-3 border @error('sponsor_id') border-red-500 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="e.g. SPON-1234">
                    </div>
                    @error('sponsor_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-user text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="block w-full pl-10 pr-3 py-3 border @error('name') border-red-500 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="John Doe" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-envelope text-gray-400 text-lg"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="block w-full pl-10 pr-3 py-3 border @error('email') border-red-500 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="name@example.com" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-lock-key text-gray-400 text-lg"></i>
                        </div>
                        <input type="password" id="password" name="password" class="block w-full pl-10 pr-10 py-3 border @error('password') border-red-500 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="••••••••" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <i class="ph ph-eye text-gray-400 hover:text-gray-600 text-lg"></i>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm shadow-primary/30 text-sm font-bold text-white btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all mt-4">
                    Sign Up
                </button>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
