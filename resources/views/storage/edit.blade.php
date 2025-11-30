<x-layouts.app :title="__('Connect Storage')">
    <div class="h-full w-full gap-4 rounded-xl">
        {{-- <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div> --}}
        {{-- <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div> --}}
        <!-- resources/views/connect-s3.blade.php -->
        <form method="POST" action="{{ route('storage.update', $storage->id) }}" class="max-w-xl mx-auto mt-8 bg-white dark:bg-neutral-900 p-6 rounded-lg border border-neutral-200 dark:border-neutral-700 shadow-sm">
            @csrf
        
            <!-- Form Header -->
            <h2 class="text-2xl font-bold mb-6 pb-2 border-b border-neutral-300 dark:border-neutral-700">
                Connect S3 Storage
            </h2>
        
            <!-- Access Key ID -->
            <div class="mb-4">
                <label for="key" class="block font-semibold mb-1">Access Key ID  <span class="text-red-600">*</span></label>
                <input type="text" name="key" id="key" 
                       value="{{ old('key') ?? $storage->key }}"
                       class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
                       required>
                @error('key')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Secret Access Key -->
            <div class="mb-4">
                <label for="secret" class="block font-semibold mb-1">Secret Access Key <span class="text-red-600">*</span></label>
                <input type="password" name="secret" id="secret" value="{{ old('secret') ?? $storage->secret }}"
                       class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
                       required>
                @error('secret')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Region -->
            <div class="mb-4">
                <label for="region" class="block font-semibold mb-1">Region  <span class="text-red-600">*</span></label>
                <input type="text" name="region" id="region" 
                       value="{{ old('region') ?? $storage->region }}" 
                       placeholder="e.g., us-east-1"
                       class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
                       required>
                @error('region')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Bucket Name -->
            <div class="mb-4">
                <label for="bucket" class="block font-semibold mb-1">Bucket Name  <span class="text-red-600">*</span></label>
                <input type="text" name="bucket" id="bucket" 
                       value="{{ old('bucket') ?? $storage->bucket }}" 
                       class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
                       required>
                @error('bucket')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Endpoint -->
            <div class="mb-6">
                <label for="endpoint" class="block font-semibold mb-1">Endpoint (Optional)</label>
                <input type="url" name="endpoint" id="endpoint" 
                       value="{{ old('endpoint') ?? $storage->endpoint }}" 
                       placeholder="https://s3.wasabisys.com"
                       class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                @error('endpoint')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        
            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                Update
            </button>
        </form>
        

    </div>
</x-layouts.app>
