<x-layouts.app :title="__('Storage Folders')">
<x-messages.toaster message="success" />
<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $connection->bucket }}</h1>
    <div>
        @if(!empty($connectionError))
            <div class="flex flex-col items-center justify-center min-h-[200px] bg-red-50 dark:bg-red-900 rounded-lg p-6 text-center space-y-4 border border-red-200 dark:border-red-700">
                <!-- Warning Icon -->
                <svg class="w-12 h-12 text-red-600 dark:text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3C7 3 3 7 3 12s4 9 9 9 9-4 9-9-4-9-9-9z" />
                </svg>
        
                <h2 class="text-xl font-semibold text-red-700 dark:text-red-300">Connection Failed</h2>
                <p class="text-red-600 dark:text-red-400">{{ $connectionError }}</p>
        
                <a href="{{ route('storage.list') }}" 
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                    <!-- Back Icon -->
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Storage List
                </a>
            </div>
        @else
            <div class="flex justify-start mb-6 bg-white rounded-lg">
                <form method="POST" action="{{ route('storage.create.folder', ['connectionId' => $connection->id]) }}" class="flex w-full space-x-2">
                    <input type="hidden" name="_token" value="gA3Rr36G4T07DECIeODRg0ueTp8nFtcGfiynTxpg" autocomplete="off">
                    <input type="hidden" name="current_path" value="/"> 
            
                    <!-- Folder Name Input -->
                    <input type="text" 
                           name="folder_name" 
                           required 
                           placeholder="New Folder Name" 
                           class="flex-grow border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                           pattern="[a-zA-Z0-9-_]+" 
                           title="Alphanumeric, dash, underscore only">
            
                    <!-- Create Button -->
                    <button type="submit" 
                            class="flex items-center border border-blue-600 text-blue-600 px-5 py-2 rounded-lg hover:bg-blue-50 focus:ring-2 focus:ring-blue-300 font-medium transition">
                        
                        <!-- Plus Icon -->
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
            
                        Create Folder
                    </button>
                </form>
            </div>
            
            <h3 class="font-semibold mb-2">Folders</h3>
            <div class="mb-4 flex items-center space-x-2 text-sm text-gray-700">
                <a href="{{ route('storage.connect', ['id' => $connection->id, 'path' => '/']) }}"
                class="text-blue-600 hover:underline">Root</a>
            
                @php
                    $segments = explode('/', trim($path, '/'));
                    $breadcrumbPath = '';
                @endphp
            
                @foreach($segments as $index => $segment)
                    @php
                        $breadcrumbPath .= '/' . $segment;
                    @endphp
                    <span>/</span>
                    <a href="{{ route('storage.connect', ['id' => $connection->id, 'path' => ltrim($breadcrumbPath, '/')]) }}"
                    class="text-blue-600 hover:underline">{{ $segment }}</a>
                @endforeach
            </div>
            <ul class="mb-6 bg-blue-100 text-accent-content rounded-lg p-4">
                {{-- @dd($folders) --}}
                {{-- @foreach($folders as $folder)
                    @php
                        $folderPath = rtrim($path, '/') . '/' . basename($folder);
                    @endphp
                    <li>
                        <a href="{{ route('storage.connect', ['id' => $connection->id, 'path' => $folderPath]) }}"
                        class="text-blue-600 hover:underline flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6z"/></svg>
                            {{ basename($folder) }}
                        </a>
                    </li>
                @endforeach --}}
                <form method="POST" action="{{ route('storage.folder.deleteMultiple', $connection->id) }}" id="deleteForm">
                    @csrf
                
                    @foreach ($folders as $folder)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="folders[]" value="{{ trim($path, '/') . '/' . $folder }}" class="mr-2 folder-checkbox">
                            <a href="{{ route('storage.connect', ['id' => $connection->id, 'path' => trim($path, '/') . '/' . $folder]) }}"
                            class="text-blue-600 hover:underline flex-1">
                            📁 {{ $folder }}
                            </a>
                        </div>
                    @endforeach
                
                    <button type="submit" 
                        onclick="return confirm('Are you sure you want to delete the selected folder(s)? This action cannot be undone.')"
                        class="mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-opacity duration-300"
                        id="deleteSelectedBtn" style="display: none;">
                        Delete Selected
                    </button>
                </form>
            </ul>

            <h3 class="font-semibold mb-2">Files</h3>
            <ul>
                @foreach($files as $file)
                    @php
                        $filePath = rtrim($path, '/') . '/' . basename($file);
                        // Generate URL to view or download file - adjust as needed
                        $fileUrl = Storage::disk('connected_storage')->url($filePath);
                    @endphp
                    <li class="flex items-center gap-2 border-b-1 border-gray-200 py-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6"/>
                        </svg>
                        <a href="{{ $fileUrl }}" target="_blank" class="text-gray-700 hover:underline">{{ basename($file) }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
<script>
    const checkboxes = document.querySelectorAll('.folder-checkbox');
    const deleteBtn = document.getElementById('deleteSelectedBtn');

    function toggleDeleteBtn() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (anyChecked) {
            deleteBtn.style.display = 'inline-block';
            deleteBtn.classList.remove('opacity-0');
            deleteBtn.classList.add('opacity-100');
        } else {
            deleteBtn.classList.remove('opacity-100');
            deleteBtn.classList.add('opacity-0');
            setTimeout(() => {
                deleteBtn.style.display = 'none';
            }, 300); // match transition duration
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleDeleteBtn);
    });

    // Initialize button state on page load
    toggleDeleteBtn();
</script>
</x-app-layout>


