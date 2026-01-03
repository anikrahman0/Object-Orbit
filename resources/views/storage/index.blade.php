<x-layouts.app :title="__('Storage List')">
    <x-messages.toaster message="success" />
    <div class="h-full w-full gap-4 rounded-xl">
        <!-- resources/views/connect-s3.blade.php -->
        <div class="relative overflow-x-auto border sm:rounded-lg">
            <div class="flex items-center justify-between px-4 py-5 border-b bg-white dark:bg-gray-800 dark:border-gray-700">
                <h4 class="font-bold tracking-tight text-gray-900 dark:text-white">Storage List</h4>
                <flux:button icon="plus" :href="route('storage.create')" variant="primary" wire:navigate>
                    Add Storage Connection
                </flux:button>
            </div>

            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Bucket Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Region
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Endpoint
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($storages as $storage)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $storage->bucket }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $storage->region }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $storage->endpoint }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2"> <!-- Add flex container with gap -->
                                    <!-- Edit Button -->
                                    <flux:button size="sm" variant="filled" icon="pencil" href="{{ route('storage.edit', $storage->id) }}" wire:navigate> 
                                        <span>Edit</span>
                                    </flux:button>
                            
                                    <!-- Delete Button -->
                                    <flux:button size="sm" variant="primary" color="red" icon="trash" href="{{ route('storage.delete', $storage->id) }}" onclick="return confirm('Are you sure you want to delete this storage connection?');">
                                        <span>Delete</span>
                                    </flux:button>

                                    <!-- Connect Button -->
                                    <flux:button size="sm" variant="primary" icon="link" href="{{ route('storage.connect', $storage->id) }}"  wire:navigate>
                                        <span>Connect</span>
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td colspan="4" class="px-6 py-4 text-center">
                                No storage found.
                            </td>
                        </tr>
                    @endforelse
                    @if ($storages->hasPages())
                        <div class="px-6 py-4">
                            {{ $storages->links() }}
                        </div>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
