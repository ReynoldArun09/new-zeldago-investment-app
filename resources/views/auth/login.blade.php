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
<body class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden p-4 sm:p-8">
    <div class="w-full max-w-md relative z-10 my-auto py-8">
        <div class="mb-8 text-center">
            <div class="w-16 h-16 rounded-2xl text-white flex items-center justify-center font-bold text-3xl mx-auto mb-6 shadow-lg" style="background-color: var(--primary);">
                {{ substr($theme['appName'] ?? 'O', 0, 1) }}
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
            <p class="text-gray-500">Sign in to continue</p>
        </div>

        <div class="bg-white rounded-none shadow-xl shadow-gray-200/50 p-8 sm:p-10 border border-gray-100">
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
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
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Password</label>
                        <a href="#" class="text-xs font-medium text-primary hover:underline">Forgot password?</a>
                    </div>
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
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
