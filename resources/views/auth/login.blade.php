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
    <title>Login - {{ $theme['appName'] ?? 'One Planet' }}</title>
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
        .btn-primary {
            background-color: var(--primary);
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .text-primary {
            color: var(--primary);
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
            <h1 class="text-4xl font-bold text-white mb-4">Welcome Back</h1>
            <p class="text-gray-300 text-lg leading-relaxed">
                Manage your investments, track your performance, and grow your portfolio with our advanced platform.
            </p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-primary relative">
        <div class="w-full max-w-md">
            <div class="mb-8 text-white">
                <h2 class="text-3xl font-bold mb-2">Sign In</h2>
                <p class="text-white/80">Enter your credentials to access your account</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
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
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Password</label>
                            <a href="#" class="text-xs font-medium text-primary hover:underline">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" class="block w-full pl-10 pr-10 py-3 border @error('password') border-red-500 @else border-gray-100 @enderror rounded-xl focus:outline-none focus:ring-2 focus-ring-primary bg-gray-50/50 transition-all text-gray-800 placeholder-gray-400" placeholder="••••••••" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all">
                        Sign In
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">Create one</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
