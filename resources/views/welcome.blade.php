<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head', ['title' => 'ObjectOrbit - Simplify Your Storage'])
</head>

<body class="min-h-screen bg-white dark:bg-zinc-950 antialiased font-sans">
    <flux:header container class="py-2 border-b border-zinc-700 bg-zinc-950/80 backdrop-blur-md sticky top-0 z-50">
        <a href="/" class="flex items-center">
            <x-app-logo-icon class="h-16 w-auto" />
        </a>

        <flux:spacer />

        <flux:navbar class="gap-4">
            @if (Route::has('login'))
                @auth
                    <flux:button variant="filled" class="!bg-white !text-zinc-950 hover:!bg-zinc-100" :href="route('dashboard')"
                        wire:navigate>
                        {{ __('Go to Dashboard') }}
                    </flux:button>
                @else
                    <flux:navbar.item class="!text-zinc-400 hover:!text-white" :href="route('login')" wire:navigate>
                        {{ __('Log in') }}
                    </flux:navbar.item>

                    @if (Route::has('register'))
                        <flux:button variant="filled" class="bg-white text-zinc-950 hover:bg-zinc-200" :href="route('register')"
                            wire:navigate>
                            {{ __('Get Started') }}
                        </flux:button>
                    @endif
                @endauth
            @endif
        </flux:navbar>
    </flux:header>

    <main>
        <!-- Hero Section -->
        <section class="relative py-20 lg:py-32 overflow-hidden">
            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-3xl mx-auto text-center">
                    <div
                        class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 mb-6 border border-zinc-200 dark:border-zinc-700">
                        <span class="flex h-2 w-2 rounded-full bg-blue-500 me-2"></span>
                        Now Connecting to S3 & More
                    </div>
                    <h1
                        class="text-5xl lg:text-7xl font-extrabold tracking-tight text-zinc-900 dark:text-white mb-8 leading-[1.1]">
                        Visualize and Manage Your <span
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 bg-clip-text text-transparent">Object
                            Storage</span> with Ease
                    </h1>
                    <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-10 leading-relaxed">
                        ObjectOrbit provides a seamless, unified interface to manage all your cloud storage connections.
                        Simple, fast, and secure.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <flux:button variant="filled" class="!bg-white !text-zinc-950 hover:!bg-zinc-100"
                                icon="layout-grid" :href="route('dashboard')" wire:navigate>
                                Explore Dashboard
                            </flux:button>
                        @else
                            <flux:button variant="primary" icon="rocket-launch" :href="route('register')" wire:navigate>
                                Get Started for Free
                            </flux:button>
                            <flux:button variant="ghost" :href="route('login')" wire:navigate>
                                Already have an account?
                            </flux:button>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Background Decoration -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-0 pointer-events-none opacity-50 dark:opacity-20">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/20 blur-[120px] rounded-full">
                </div>
                <div
                    class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/20 blur-[120px] rounded-full">
                </div>
            </div>
        </section>

        <!-- Features Preview -->
        <section class="py-24 bg-zinc-50 dark:bg-zinc-900/50 border-y border-zinc-200 dark:border-zinc-800">
            <div class="container mx-auto px-6 text-center">
                <h2
                    class="text-3xl font-bold text-zinc-900 dark:text-white mb-16 underline decoration-blue-500 decoration-4 underline-offset-8">
                    Built for Modern Cloud Workflows</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div
                        class="p-8 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 text-left">
                        <div
                            class="size-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-6">
                            <flux:icon name="database" class="size-6" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Multi-Cloud Support</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">Connect to S3, R2, and other compatible object
                            storage providers in seconds.</p>
                    </div>
                    <div
                        class="p-8 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 text-left">
                        <div
                            class="size-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center mb-6">
                            <flux:icon name="folder" class="size-6" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">File Management</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">Upload, delete, and organize your files with a
                            clean, intuitive file explorer interface.</p>
                    </div>
                    <div
                        class="p-8 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 text-left">
                        <div
                            class="size-12 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center mb-6">
                            <flux:icon name="shield-check" class="size-6" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Secure Access</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">Your credentials stay secure. We only bridge the
                            connection when you're managing your data.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-4 bg-zinc-950/80 backdrop-blur-md border-t border-zinc-700">
        <div class="container mx-auto px-6">
            <div class="flex flex-col items-center text-center">
                <x-app-logo-icon class="h-16 w-auto mb-2" />

                <nav class="flex flex-wrap justify-center gap-x-6 gap-y-1 mb-2 text-xs font-medium text-zinc-400">
                    <a href="/" class="hover:text-white transition-colors duration-200">Home</a>
                    <a href="{{ route('dashboard') }}"
                        class="hover:text-white transition-colors duration-200">Dashboard</a>
                </nav>

                <div class="w-12 h-px bg-zinc-700 mb-2"></div>

                <p class="text-zinc-500 text-[10px] tracking-widest uppercase">
                    &copy; {{ date('Y') }} Anik Rahman. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    @fluxScripts
</body>

</html>