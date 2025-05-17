<x-layouts.app :title="__('Storage Folders')">
    
<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $connection->bucket }}</h1>

    {{-- Display success message --}}
    {{-- <h2 class="text-lg font-semibold mb-4">Browsing: {{ $path === '/' ? 'Root' : $path }}</h2> --}}


    {{-- <h2 class="text-lg font-semibold mb-4">Browsing: {{ $path }}</h2>

    <div class="mb-4">
        @if($path !== '/')
            @php
                // Calculate parent path by removing last segment
                $segments = explode('/', trim($path, '/'));
                array_pop($segments);
                $parentPath = '/' . implode('/', $segments);
                if ($parentPath === '') $parentPath = '/';
            @endphp
            <a href="{{ route('storage.connect', ['id' => $connection->id, 'path' => $parentPath]) }}"
               class="text-blue-600 hover:underline">&larr; Parent Folder</a>
        @endif
    </div> --}}

    <div>
        <div class="flex justify-between mb-2 bg-white rounded-lg shadow-sm">
            <form method="POST" action="{{ route('storage.create.folder', ['connectionId' => $connection->id]) }}" class="mb-6 flex space-x-2">
                @csrf
                <input type="hidden" name="current_path" value="{{ $path }}"> {{-- this is the current folder path --}}
                <input type="text" name="folder_name" required placeholder="New Folder Name" class="border p-2 rounded flex-grow" pattern="[a-zA-Z0-9-_]+" title="Alphanumeric, dash, underscore only">
                <button type="submit" class="bg-blue-600 text-white px-4 rounded hover:bg-blue-700">Create Folder</button>
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


