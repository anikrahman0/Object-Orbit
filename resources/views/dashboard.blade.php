<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-between bg-blue-50 dark:bg-neutral-900 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    
                    <span class="flex h-20 w-20 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow">
                        <!-- Storage Connection Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                            stroke="currentColor" class="h-10 w-10">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3c4.418 0 8 1.79 8 4v10c0 2.21-3.582 4-8 4s-8-1.79-8-4V7c0-2.21 3.582-4 8-4z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 12c0 2.21 3.582 4 8 4s8-1.79 8-4" />
                        </svg>
                    </span>
            
                    <div>
                        <h3 class="font-semibold text-lg">Storage Connections</h3>
                        <p class="text-neutral-500 dark:text-neutral-400 text-sm">{{ $storage_count }} {{ Str::plural('Connection', $storage_count) }}</p>
                    </div>
            
                </div>
            </div>
        </div>
        {{-- <div class="relative h-full flex items-center justify-center overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-app-logo-icon class="size-32 stroke-gray-900/10 dark:stroke-neutral-100/10" />
        </div>         --}}
    </div>
</x-layouts.app>
