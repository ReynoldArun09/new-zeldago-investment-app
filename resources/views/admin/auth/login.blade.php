@php
    $theme = get_setting('theme_appearance') ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ $theme['appName'] ?? 'MLM Application' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#0a1535] flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background geometric shapes -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 left-10 w-40 h-40 border border-[#1e3a6e]/40 rounded-2xl rotate-12 opacity-50"></div>
        <div class="absolute top-32 left-24 w-20 h-20 border border-[#2a4a8e]/30 rounded-xl rotate-45 opacity-40"></div>
        <div class="absolute bottom-16 right-12 w-56 h-56 border border-[#1e3a6e]/30 rounded-3xl -rotate-12 opacity-40"></div>
        <div class="absolute bottom-40 right-32 w-24 h-24 border border-[#3b5fc0]/20 rounded-xl rotate-6 opacity-30"></div>
        <div class="absolute top-1/2 left-6 w-16 h-16 border border-[#2a4a8e]/20 rounded-lg -rotate-12 opacity-30"></div>
        <div class="absolute top-1/4 right-8 w-32 h-32 border border-[#1e3a6e]/25 rounded-2xl rotate-20 opacity-35"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Card -->
        <div class="bg-[#0d1e45] rounded-2xl shadow-2xl overflow-hidden border border-[#1e3a6e]/50">
            <!-- Header band -->
            <div class="bg-gradient-to-r from-[#2a4aad] to-[#3b5fc0] px-8 py-7 text-center relative">
                <!-- Shield icon -->
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/15 mb-3 ring-2 ring-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white tracking-wide">
                    Admin Portal
                </h1>
                <p class="text-white/70 text-sm mt-1">
                    Sign in to your dashboard
                </p>

                <!-- Bottom notch -->
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-6 h-6 bg-[#2a4aad] rotate-45 rounded-sm"></div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login.submit') }}" class="px-8 pt-10 pb-8 space-y-5">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded relative text-sm" role="alert">
                        <span class="block sm:inline">{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-semibold text-white/60 uppercase tracking-widest">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="Enter email"
                            class="w-full bg-[#0a1535] border border-[#1e3a6e]/60 text-white placeholder-white/25
                            rounded-lg pl-10 pr-4 py-2.5 text-sm outline-none
                            focus:border-[#3b5fc0] focus:ring-1 focus:ring-[#3b5fc0]/40 transition-colors"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-semibold text-white/60 uppercase tracking-widest">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                            placeholder="Enter password"
                            class="w-full bg-[#0a1535] border border-[#1e3a6e]/60 text-white placeholder-white/25
                            rounded-lg pl-10 pr-4 py-2.5 text-sm outline-none
                            focus:border-[#3b5fc0] focus:ring-1 focus:ring-[#3b5fc0]/40 transition-colors"
                        >
                    </div>
                </div>

                <!-- Remember me -->
                <label class="flex items-center gap-2.5 cursor-pointer group w-fit">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-[#3b5fc0] cursor-pointer">
                    <span class="text-sm text-white/50 group-hover:text-white/70 transition-colors select-none">
                        Remember me
                    </span>
                </label>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-[#2a4aad] to-[#3b5fc0] hover:from-[#3355c0] hover:to-[#4a6fd0]
                    text-white font-semibold text-sm tracking-widest uppercase rounded-lg py-3
                    transition-all duration-200 shadow-lg shadow-[#2a4aad]/30 mt-1
                    active:scale-[0.98] focus:outline-none">
                    Sign In
                </button>
            </form>
        </div>

        <!-- Footer note -->
        <p class="text-center text-white/25 text-xs mt-5">
            Protected admin area &mdash; unauthorized access is prohibited
        </p>
    </div>
</body>
</html>
