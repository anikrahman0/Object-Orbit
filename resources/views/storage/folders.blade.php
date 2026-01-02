<x-layouts.app :title="__('Storage Folders')">
<x-messages.toaster message="success" />
<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $connection->bucket }}</h1>
    <div>
        @if(!empty($connectionError))
            <div class="flex flex-col items-center justify-center min-h-[200px]  rounded-lg p-6 text-center space-y-4 bg-zinc-50 dark:bg-zinc-80">
                
                <div class="flex justify-center items-center space-x-2">
                    <flux:icon name="wifi-off" class="w-7 h-7 text-red-300 dark:text-red-300"></flux:icon>
                    <h2 class="font-semibold text-zinc-400 dark:text-zinc-400">Connection Failed</h2>
                </div>
                <flux-heading class="break-words break-all overflow-hidden text-start text-zinc-500 dark:text-zinc-500 text-sm">{{ $connectionError }}</flux-heading>

                <flux:button size="sm" href="{{ route('storage.list') }}" variant="filled">
                    <flux:icon name="arrow-left" class="w-5 h-5 mr-2"></flux:icon>
                    Back to Storage List
                </flux:button>
            </div>
        @else
            <div class="flex justify-between items-center mb-6 bg-white rounded-lg">
                <form method="POST" action="{{ route('storage.create.folder', ['connectionId' => $connection->id]) }}" class="flex w-full space-x-2">
                    @csrf
                    <input type="hidden" name="current_path" value="/"> 
            
                    <!-- Folder Name Input -->
                    <flux:input 
                        type="text"
                        placeholder="New Folder Name" 
                        name="folder_name" 
                        required
                        max="255"
                     />
                    <flux:button type="submit" :loading="false" color="zinc" class="flex items-center space-x-1">
                        <flux:icon name="plus" class="w-5 h-5" />
                        <span>Create Folder</span>
                    </flux:button>
                </form>
            </div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Folders</h2>
                <flux:modal.trigger name="upload">
                    <flux:button variant="primary" class="flex items-center space-x-1">
                        <flux:icon name="upload" class="w-5 h-5" />
                        <span>Upload Files</span>
                    </flux:button>
                </flux:modal.trigger>
            </div>
            
            <div class="mb-4 flex items-center space-x-2 text-sm text-gray-700">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item href="{{ route('storage.connect', ['id' => $connection->id, 'path' => '/']) }}" separator="slash">root</flux:breadcrumbs.item>
                
                    @php
                        $segments = explode('/', trim($path, '/'));
                        $breadcrumbPath = '';
                    @endphp
                
                    @foreach($segments as $index => $segment)
                        @php
                            $breadcrumbPath .= '/' . $segment;
                        @endphp
                        <flux:breadcrumbs.item href="{{ route('storage.connect', ['id' => $connection->id, 'path' => ltrim($breadcrumbPath, '/')]) }}" separator="slash">{{ $segment }}</flux:breadcrumbs.item>
                    
                    @endforeach
                </flux:breadcrumbs>
            </div>

            <ul class="mb-6 {{ !empty($folders) && $folders->count() > 0 ?  'bg-zinc-100' : '' }} dark:bg-zinc-800 text-accent-content rounded-lg p-4">
                <form method="POST" action="{{ route('storage.folder.deleteMultiple', $connection->id) }}" id="deleteForm">
                    @csrf
                
                    @foreach ($folders as $folder)
                        <div class="flex items-center mb-2">
                            <flux:checkbox name="folders[]" value="{{ trim($path, '/') . '/' . $folder }}" class="mr-2 folder-checkbox"> </flux:checkbox>
                            <a 
                                href="{{ route('storage.connect', ['id' => $connection->id, 'path' => trim($path, '/') . '/' . $folder]) }}"
                                class="flex items-center gap-2">
                                <flux:icon name="folder" class="w-5 h-5"></flux:icon> {{ $folder }}
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
            @if(!empty($files) && count($files) > 0)
                <h3 class="font-semibold mb-3">Files</h3>
            @endif

            <div 
                x-data="fileSelector(@js($files))"
                @files-deleted.window="clearAll()"
                class="space-y-3"
            >
                <!-- Actions -->
                @if(!empty($files) && count($files) > 0)
                    <div class="flex items-center gap-3">

                        <!-- Select / Deselect All -->
                        <button type="button" @click="toggleSelectAll()" class="inline-flex items-center cursor-pointer">
                            <flux:badge 
                                variant="pill" 
                                icon="user"
                                x-text="allSelected ? 'Deselect All' : 'Select All'"
                            ></flux:badge>
                        </button>

                        <!-- Delete Selected Modal Trigger -->
                        <flux:modal.trigger 
                            name="delete-selected" 
                            x-show="selected.length > 0"
                            x-cloak
                        >
                            <flux:badge
                                variant="pill" 
                                color="red"
                                class="text-sm text-red-600 inline-flex items-center"
                            >
                                <flux:icon name="trash" class="w-4 h-4 mr-2"></flux:icon>
                                Delete Selected (<span x-text="selected.length"></span>)
                            </flux:badge>
                        </flux:modal.trigger>

                    </div>
                @endif

                <!-- File list -->
                <ul class="divide-y divide-gray-200">
                    @forelse($files as $file)
                        @php
                            $filePath = rtrim($path, '/') . '/' . basename($file);
                            $fileUrl  = Storage::disk('connected_storage')->url($filePath);
                        @endphp

                        <li class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-3">

                                <!-- Flux Checkbox -->
                                <flux:checkbox
                                    x-bind:checked="isSelected('{{ $filePath }}')"
                                    x-on:click="toggle('{{ $filePath }}')"
                                ></flux:checkbox>

                                <flux:icon name="file" class="w-5 h-5 text-gray-500"/>

                                <a href="{{ $fileUrl }}"
                                    target="_blank"
                                    class="text-gray-700 hover:underline text-sm break-words break-all overflow-hidden"
                                >
                                    {{ basename($file) }}
                                </a>
                            </div>
                        </li>
                    @empty
                        <p class="flex items-center gap-2 text-zinc-500 py-3">
                            <flux:icon name="file-minus" class="w-5 h-5"/>
                            <span>Files in this folder are empty.</span>
                        </p>
                    @endforelse
                </ul>
                <!-- Delete Modal -->
                <flux:modal name="delete-selected" class="min-w-[22rem]">
                    <form method="POST" action="{{ route('storage.delete-files', $connection->id) }}">
                        @csrf

                        <!-- Hidden inputs for selected files -->
                        <template x-for="file in selected" :key="file">
                            <input type="hidden" name="files[]" x-for="file in selected" :key="file" :value="file">
                        </template>

                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Delete Selected Files?</flux:heading>

                                <flux:text class="mt-2">
                                    You're about to delete <strong x-text="selected.length"></strong> file(s).<br>
                                    This action cannot be reversed.
                                </flux:text>
                            </div>

                            <div class="flex gap-2">
                                <flux:spacer />

                                <flux:modal.close>
                                    <flux:button variant="ghost" type="button">Cancel</flux:button>
                                </flux:modal.close>

                                <flux:button 
                                    variant="danger" 
                                    type="submit"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </div>
                    </form>
                </flux:modal>
            </div>
        @endif
    </div>

    <!--Upload Modal -->
    <div x-data="uploadModal()">
        <flux:modal name="upload" class="[:where(&)]:max-w-3xl [:where(&)]:w-full">
            <div class="p-6 space-y-6">
                <!-- Header -->
                <div>
                    <h2 class="text-lg font-semibold">Upload Files</h2>
                    <p class="text-sm text-gray-500">
                        Drag & drop files or click to select. Max 20 files, 10MB each.
                    </p>
                    <p class="text-sm text-red-600 mt-1" x-text="errorMessage"></p>
                </div>

                <!-- Drag & Drop Zone -->
                <div
                    class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 p-10 text-center cursor-pointer"
                    :class="dragging ? 'border-blue-500' : ''"
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="dropFiles($event)"
                    x-on:click="$refs.fileInput.click()">
                    <flux:icon name="upload" class="w-12 h-12 text-zinc-400 mb-3"/>
                    <p class="font-medium text-zinc-700 dark:text-zinc-200">Drop files here</p>
                    <p class="text-sm text-zinc-500 mt-1">or click to browse</p>

                    <input
                        type="file"
                        multiple
                        class="hidden"
                        x-ref="fileInput"
                        x-on:change="selectFiles($event)"
                        accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,.ico,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.odt,.zip,.rar,.7z,.tar,.gz,.mp3,.wav,.ogg,.m4a,.flac,.aac,.mp4,.webm,.mov,.avi,.mkv,.flv,.json,.xml,.yml,.yaml,.md,.log,.html,.css"
                    />
                </div>

            <!-- Selected Files -->
                <template x-if="files.length">
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        <template x-for="(file, index) in files" :key="file.name">
                            <div class="flex items-center justify-between rounded-lg bg-zinc-100 dark:bg-zinc-700 p-3">
                                <div class="flex items-center gap-3">
                                    <!-- Thumbnail if image -->
                                    <template x-if="file.type.startsWith('image/')">
                                        <flux:modal.trigger name="imagePreview">
                                            <flux:tooltip content="Preview">
                                                <img :src="URL.createObjectURL(file)" class="w-10 h-10 object-cover rounded" alt=""  x-on:click="preview(file)" />
                                            </flux:tooltip>
                                        </flux:modal.trigger>
                                    </template>

                                    <!-- File icon if not image -->
                                    <template x-if="!file.type.startsWith('image/')">
                                        <flux:icon name="file" class="w-10 h-10 text-zinc-400"/>
                                    </template>

                                    <div class="truncate">
                                        <p class="text-sm font-medium" x-text="file.name"></p>
                                        <p class="text-xs text-zinc-500" x-text="formatSize(file.size)"></p>
                                    </div>
                                </div>

                                <!-- Delete button -->
                                <flux:tooltip content="Remove file">
                                    <flux:icon name="trash" x-on:click="files.splice(index, 1)" class="w-4 h-4"/>
                                </flux:tooltip>
                            </div>
                        </template>
                    </div>
                </template>


                <!-- Footer -->
                <div class="flex justify-end gap-2 pt-4 border-t dark:border-zinc-700">
                    <flux:modal.close>
                        <flux:button variant="ghost" data-flux-modal-close>Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" @click="startUpload()">Start Upload</flux:button>
                </div>
            </div>
            
            <!-- Image Preview Modal -->
            <flux:modal name="imagePreview" class="[:where(&)]:max-w-5xl [:where(&)]:w-full">
                <div class="p-6 space-y-4 flex flex-col items-center">
                    <img 
                        x-bind:src="previewImage" 
                        alt="Preview" 
                        class="w-full max-h-[70vh] object-contain rounded-lg" 
                    />
                    <div class="flex justify-end w-full">
                        <flux:modal.close>
                            <flux:button variant="ghost" data-flux-modal-close>Close</flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            </flux:modal>
        </flux:modal>
    </div>
</div>
<script>
function uploadModal() {
    return {
        files: [],
        previewImage: null,
        dragging: false,
        maxFiles: 20,
        maxSize: 10 * 1024 * 1024, // 10MB
        allowedTypes: [
            'image/jpeg','image/png','image/webp','image/gif','image/bmp','image/tiff','image/svg+xml',
            'image/x-icon','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain','text/csv','application/rtf','application/vnd.oasis.opendocument.text',
            'application/zip','application/x-rar-compressed','application/x-7z-compressed',
            'application/x-tar','application/gzip',
            'audio/mpeg','audio/wav','audio/ogg','audio/mp4','audio/flac','audio/aac',
            'video/mp4','video/webm','video/quicktime','video/x-msvideo','video/x-matroska','video/x-flv',
            'application/json','application/xml','text/yaml','text/markdown','text/html','text/css','text/log'
        ],
        errorMessage: '',

        handleFiles(selected) {
            this.errorMessage = '';
            const newFiles = [...selected];

            if (this.files.length + newFiles.length > this.maxFiles) {
                this.errorMessage = `You can upload maximum ${this.maxFiles} files.`;
                return;
            }

            newFiles.forEach(file => {
                if (!this.allowedTypes.includes(file.type)) {
                    this.errorMessage = `File type not allowed: ${file.name}`;
                    return;
                }
                if (file.size > this.maxSize) {
                    this.errorMessage = `File too large (max 10MB): ${file.name}`;
                    return;
                }
                this.files.push(file);
            });
        },

        dropFiles(event) {
            this.dragging = false;
            this.handleFiles(event.dataTransfer.files);
        },

        selectFiles(event) {
            this.handleFiles(event.target.files);
        },

        formatSize(size) {
            if (size < 1024) return size + ' B'; // Bytes
            else if (size < 1024 * 1024) return (size / 1024).toFixed(2) + ' KB'; // KB
            else if (size < 1024 * 1024 * 1024) return (size / (1024 * 1024)).toFixed(2) + ' MB'; // MB
            else return (size / (1024 * 1024 * 1024)).toFixed(2) + ' GB'; // GB
        },

        startUpload() {
            if (!this.files.length) {
                this.errorMessage = 'No files selected.';
                return;
            }

            console.log('Ready to upload files:', this.files);
            // Here call your Livewire method or API for S3 upload
        },

        preview(file) {
            if (file.type.startsWith('image/')) {
                this.previewImage = URL.createObjectURL(file);

                // Dispatch a custom event Flux listens to
                window.dispatchEvent(new CustomEvent('modal-show', {
                    detail: { name: 'imagePreview' }
                }));
            }
        }


    }
}
function fileSelector(files = []) {
    return {
        files: files,       // all files loaded from backend
        selected: [],       // selected files
        allSelected: false, // for toggle select all

        // Toggle single file
        toggle(file) {
            if (this.selected.includes(file)) {
                this.selected = this.selected.filter(f => f !== file);
            } else {
                this.selected.push(file);
            }
            this.updateAllSelected();
        },

        // Select / Deselect all files
        toggleSelectAll() {
            if (this.allSelected) {
                this.selected = [];
                this.allSelected = false;
            } else {
                this.selected = [...this.files];
                this.allSelected = true;
            }
        },

        // Clear all selections
        clearAll() {
            this.selected = [];
            this.allSelected = false;
        },

        // Check if file is selected
        isSelected(file) {
            return this.selected.includes(file);
        },

        // Update the allSelected flag
        updateAllSelected() {
            this.allSelected = (this.selected.length === this.files.length && this.files.length > 0);
        },

        // Optional: live deletion via Alpine (if you want JS delete)
        deleteSelected() {
            if (this.selected.length === 0) return;

            if (!confirm('Delete selected files?')) return;

            // Call Livewire if exists
            if (window.$wire) {
                $wire.deleteFiles(this.selected);
            }

            // Remove locally
            this.files = this.files.filter(f => !this.selected.includes(f));
            this.clearAll();

            // Dispatch event
            window.dispatchEvent(new CustomEvent('files-deleted'));
        }
    }
}
</script>
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


