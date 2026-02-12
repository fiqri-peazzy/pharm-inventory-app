<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Pharm Inventory</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Apply dark mode based on localStorage -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body class="h-full bg-white transition-colors duration-300 dark:bg-gray-900">
    <div class="flex min-h-full items-center justify-center p-6 lg:p-12">
        <div class="relative w-full max-w-lg text-center">
            <!-- Decorative Elements -->
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 opacity-20 dark:opacity-30">
                <div class="h-64 w-64 rounded-full bg-blue-500 blur-3xl"></div>
            </div>
            <div class="absolute -bottom-24 left-1/2 -translate-x-1/2 opacity-10 dark:opacity-20">
                <div class="h-64 w-64 rounded-full bg-purple-500 blur-3xl"></div>
            </div>

            <main class="relative z-10">
                <div class="mb-6 flex justify-center">
                    <div class="relative">
                        <span class="text-9xl font-black tracking-tighter text-gray-100 dark:text-gray-800/50">
                            @yield('code')
                        </span>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-6xl font-extrabold text-transparent">
                                @yield('code')
                            </span>
                        </div>
                    </div>
                </div>

                <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    @yield('title')
                </h1>
                
                <p class="mt-6 text-base leading-7 text-gray-600 dark:text-gray-400">
                    @yield('message')
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ Auth::check() ? Auth::user()->getHomeRoute() : route('login') }}" 
                       class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-xl transition-all duration-200 hover:bg-blue-500 hover:scale-105 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-blue-600 dark:hover:bg-blue-500">
                        Kembali ke Sistem
                    </a>
                    
                    @if(Auth::check())
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-gray-900 transition-colors hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                Keluar <span aria-hidden="true">&rarr;</span>
                            </button>
                        </form>
                    @endif
                </div>
            </main>

            <footer class="mt-12 text-sm text-gray-500 dark:text-gray-500">
                &copy; {{ date('Y') }} Pharm Inventory App. All rights reserved.
            </footer>
        </div>
    </div>
</body>

</html>
