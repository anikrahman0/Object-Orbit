<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border  p-6 flex flex-col justify-between bg-zinc-50 dark:bg-zinc-900 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    
                    <span class="flex h-20 w-20 items-center justify-center rounded-xl bg-gradient-to-br from-light-500 to-light-600 bg-accent text-white shadow">
                        <!-- Storage Connection Icon -->
                        <flux:icon name="database" class="size-10" />
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
