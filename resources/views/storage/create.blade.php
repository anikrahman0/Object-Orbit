<x-layouts.app :title="__('Connect Storage')">
    <div class="h-full w-full gap-4 rounded-lg">
        <!-- resources/views/connect-s3.blade.php -->
        <form method="POST" action="{{ route('storage.store') }}" class="max-w-xl mx-auto mt-8 bg-white dark:bg-neutral-900 p-6 rounded-lg border border-neutral-200 dark:border-neutral-700 shadow-sm">
            @csrf
        
            <!-- Form Header -->
            <h2 class="text-xl font-bold mb-6 pb-3 border-b border-neutral-300 dark:border-neutral-700">
                Add New S3 Connection
            </h2>
        
            <!-- Access Key ID -->
            <div class="mb-4">
                <flux:input 
                    label="Access Key ID *"
                    type="text"
                    name="key"
                    id="key"
                    value="{{ old('key') }}"
                    required
                    :invalid="$errors->has('key')"
                />
            
            </div>
        
            <!-- Secret Access Key -->
            <div class="mb-4">
                <flux:input 
                    label="Secret Access Key *"
                    type="password"
                    name="secret"
                    id="secret"
                    required
                    :invalid="$errors->has('secret')"
                />
            </div>
            <!-- Region -->
            <div class="mb-4">
                <flux:input 
                    label="Region *"
                    type="text"
                    name="region"
                    id="region"
                    value="{{ old('region') }}"
                    placeholder="e.g., us-east-1"
                    required
                    :invalid="$errors->has('region')"
                />
            </div>

            <!-- Bucket Name -->
            <div class="mb-4">
                <flux:input 
                    label="Bucket Name *"
                    type="text"
                    name="bucket"
                    id="bucket"
                    value="{{ old('bucket') }}"
                    required
                    :invalid="$errors->has('bucket')"
                />
            </div>
        
            <!-- Endpoint -->
            <div class="mb-6">
                <flux:input 
                    label="Endpoint (Optional)"
                    type="url"
                    name="endpoint"
                    id="endpoint"
                    value="{{ old('endpoint') }}"
                    placeholder="https://s3.wasabisys.com"
                />
            </div>
        
            <!-- Submit Button -->
            <flux:button 
                type="submit" 
                variant="primary" 
                class="w-full mb-3 mt-3">
                Create
            </flux:button>
        </form>
        

    </div>
</x-layouts.app>
