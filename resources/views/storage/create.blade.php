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
        <form method="POST" action="{{ route('storage.store') }}" class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            @csrf

            <h2 class="text-xl font-bold mb-4">Connect S3 Storage</h2>

            <div class="mb-4">
                <label class="block font-semibold mb-1" for="key">Access Key ID</label>
                <input type="text" name="key" id="key" class="w-full border rounded px-3 py-2" required value="{{ old('key') }}">
                @error('key')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1" for="secret">Secret Access Key</label>
                <input type="password" name="secret" id="secret" class="w-full border rounded px-3 py-2" required>
                @error('secret')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1" for="region">Region</label>
                <input type="text" name="region" id="region" placeholder="e.g., us-east-1" class="w-full border rounded px-3 py-2" required value="{{ old('region') }}">
                @error('region')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1" for="bucket">Bucket Name</label>
                <input type="text" name="bucket" id="bucket" class="w-full border rounded px-3 py-2" required value="{{ old('bucket') }}">
                @error('bucket')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1" for="endpoint">Endpoint (Optional)</label>
                <input type="url" name="endpoint" id="endpoint" placeholder="https://s3.wasabisys.com" class="w-full border rounded px-3 py-2" value="{{ old('endpoint') }}">
                @error('endpoint')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Store
            </button>
        </form>

    </div>
</x-layouts.app>
