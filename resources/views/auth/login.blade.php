@php
    $theme = get_setting('theme_appearance');
    // Set fallbacks for robustness if settings are incomplete
    $primaryColor = $theme['primaryColor'] ?? '#4f46e5'; // default indigo-600
    $sidebarColor = $theme['sidebarColor'] ?? '#1e1e2f';
    $bgColor = $theme['bgColor'] ?? '#f8fafc'; // light slate
    $fontFamily = $theme['fontFamily'] ?? 'Inter, sans-serif';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $theme['appName'] ?? 'One Planet' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --sidebar-bg: {{ $sidebarColor }};
            --body-bg: {{ $bgColor }};
            --font-main: {{ $fontFamily }};
            /* Generate some secondary colors based on primary for gradients */
            --primary-light: color-mix(in srgb, var(--primary) 80%, white);
            --primary-dark: color-mix(in srgb, var(--primary) 80%, black);
        }
        
        body {
            font-family: var(--font-main);
            background-color: var(--body-bg);
            /* Premium animated gradient background */
            background: linear-gradient(-45deg, #f8fafc, #e2e8f0, var(--primary-light), #f1f5f9);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .premium-card {
            background-color: #ffffff;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 10px 15px -3px rgba(0, 0, 0, 0.05),
                0 25px 50px -12px rgba(0, 0, 0, 0.1);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .premium-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f8fafc;
            border: 1.5px solid transparent;
        }
        
        .premium-input:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary) 15%, transparent);
        }
        
        .premium-input:focus + .input-icon i {
            color: var(--primary);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px color-mix(in srgb, var(--primary) 30%, transparent);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }

        .text-primary {
            color: var(--primary);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative">
    
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full opacity-20 blur-3xl mix-blend-multiply" style="background-color: var(--primary);"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full opacity-20 blur-3xl mix-blend-multiply" style="background-color: var(--primary-dark);"></div>
    </div>

    <div class="w-full max-w-[420px] relative z-10 my-auto">
        
        <div class="premium-card p-8 sm:p-10 relative overflow-hidden">
            <!-- Subtle top border accent -->
            <div class="absolute top-0 left-0 w-full h-1" style="background: linear-gradient(90deg, var(--primary-light), var(--primary), var(--primary-dark));"></div>
            
            <div class="text-center mb-8 pt-2">
                @php $branding = get_setting('logo_favicon') ?? []; @endphp
                @if(!empty($branding['logo']))
                    <img src="{{ $branding['logo'] }}" alt="{{ $theme['appName'] ?? 'App Logo' }}" class="h-16 mx-auto mb-6 object-contain drop-shadow-sm">
                @else
                    <div class="w-16 h-16 rounded-2xl text-white flex items-center justify-center font-bold text-3xl mx-auto mb-6 shadow-md" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark));">
                        {{ substr($theme['appName'] ?? 'O', 0, 1) }}
                    </div>
                @endif
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight mb-2">Welcome Back</h2>
                <p class="text-sm font-medium text-slate-500">Please sign in to your account</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <div class="space-y-1.5">
                    <label for="login_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider ml-1">Email or Username</label>
                    <div class="relative group">
                        <input type="text" id="login_id" name="login_id" value="{{ old('login_id') }}" 
                            class="premium-input block w-full pl-11 pr-4 py-3.5 rounded-xl text-slate-800 placeholder-slate-400 font-medium outline-none" 
                            placeholder="Enter your credentials" required>
                        <div class="input-icon absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-300">
                            <i class="ph ph-user text-slate-400 text-lg transition-colors duration-300"></i>
                        </div>
                    </div>
                    @error('login_id')
                        <p class="text-red-500 text-xs mt-1 ml-1 font-medium flex items-center gap-1"><i class="ph ph-warning-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between ml-1">
                        <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Password</label>
                        <a href="#" class="text-xs font-bold text-primary hover:opacity-80 transition-opacity">Forgot?</a>
                    </div>
                    <div class="relative group">
                        <input type="password" id="password" name="password" 
                            class="premium-input block w-full pl-11 pr-11 py-3.5 rounded-xl text-slate-800 placeholder-slate-400 font-medium outline-none" 
                            placeholder="••••••••" required>
                        <div class="input-icon absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-300">
                            <i class="ph ph-lock-key text-slate-400 text-lg transition-colors duration-300"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-400 hover:text-slate-700 transition-colors" onclick="togglePassword()">
                            <i class="ph ph-eye text-lg" id="togglePasswordIcon"></i>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 ml-1 font-medium flex items-center gap-1"><i class="ph ph-warning-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl text-sm font-bold text-white btn-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign In <i class="ph ph-arrow-right font-bold text-lg"></i>
                    </button>
                </div>
            </form>
            
        </div>
        
        <!-- Optional footer text -->
        <p class="text-center text-xs font-medium text-slate-500 mt-8">
            &copy; {{ date('Y') }} {{ $theme['appName'] ?? 'One Planet' }}. All rights reserved.
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }
    </script>
</body>
</html>
