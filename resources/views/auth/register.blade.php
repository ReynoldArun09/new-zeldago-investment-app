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
<body class="min-h-screen flex bg-gray-50">
    <!-- Left Side: Graphic / Branding -->
    <div class="hidden lg:flex w-1/2 bg-[#11131e] relative overflow-hidden items-center justify-center p-12">
        <!-- Decorative glowing orbs to mimic reference image -->
        <div class="absolute top-0 left-0 w-full h-full opacity-40 pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-red-900 mix-blend-screen filter blur-[120px]"></div>
            <div class="absolute -bottom-[20%] -right-[10%] w-[60%] h-[60%] rounded-full bg-green-900 mix-blend-screen filter blur-[100px]"></div>
        </div>

        <div class="relative z-10 text-center max-w-lg">
            <h1 class="text-4xl font-bold text-white mb-4">Join Us Today</h1>
            <p class="text-gray-300 text-lg leading-relaxed">
                Create an account to start managing your investments and exploring our powerful platform features.
            </p>
        </div>
    </div>

    <!-- Right Side: Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-primary relative">
        <div class="w-full max-w-md">
            <div class="mb-8 text-white">
                <h2 class="text-3xl font-bold mb-2">Create Account</h2>
                <p class="text-white/80">Fill in the details to get started</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form action="{{ route('register') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="sponsor_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sponsor Referral Code (Optional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input type="text" id="sponsor_id" name="sponsor_id" value="{{ old('sponsor_id') }}" class="block w-full pl-10 pr-3 py-3 border @error('sponsor_id') border-red-500 @else border-gray-100 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="e.g. SPON-1234">
                        </div>
                        @error('sponsor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="block w-full pl-10 pr-3 py-3 border @error('name') border-red-500 @else border-gray-100 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="John Doe" required>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="block w-full pl-10 pr-3 py-3 border @error('email') border-red-500 @else border-gray-100 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="name@example.com" required>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" class="block w-full pl-10 pr-10 py-3 border @error('password') border-red-500 @else border-gray-100 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="••••••••" required>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all">
                        Sign Up
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
