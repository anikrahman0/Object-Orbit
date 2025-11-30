@props(['message'])

@if(session($message))
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 4000)" 
        class="fixed top-5 right-5 z-50 flex items-center max-w-sm w-full bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg transition duration-500"
        role="alert"
    >
        <!-- Icon -->
        <svg class="w-6 h-6 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>

        <span class="flex-1">{{ session($message) }}</span>

        <!-- Close Button -->
        <button @click="show = false" class="ml-2 text-green-700 hover:text-green-900 focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif
