<x-layouts.app :title="__('Storage Folders')">
    <x-messages.toaster message="success" />
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">{{ $connection->bucket }}</h1>
        <div>
            @if(!empty($connectionError))
                <div
                    class="flex flex-col items-center justify-center min-h-[200px]  rounded-lg p-6 text-center space-y-4 bg-zinc-50 dark:bg-zinc-80">

                    <div class="flex justify-center items-center space-x-2">
                        <flux:icon name="wifi-off" class="w-7 h-7 text-red-300 dark:text-red-300"></flux:icon>
                        <h2 class="font-semibold text-zinc-400 dark:text-zinc-400">Connection Failed</h2>
                    </div>
                    <flux-heading
                        class="break-words break-all overflow-hidden text-start text-zinc-500 dark:text-zinc-500 text-sm">{{ $connectionError }}</flux-heading>

                    <flux:button size="sm" href="{{ route('storage.list') }}" variant="filled" wire:navigate>
                        <flux:icon name="arrow-left" class="w-5 h-5 mr-2"></flux:icon>
                        Back to Storage List
                    </flux:button>
                </div>
            @else
                <div class="mb-4 flex items-center space-x-2 text-sm text-gray-700 border-b">
                    <flux:breadcrumbs class="mb-3">
                        <flux:breadcrumbs.item
                            href="{{ route('storage.connect', ['id' => $connection->id, 'path' => '/']) }}"
                            separator="slash">root</flux:breadcrumbs.item>

                        @php
                            $segments = explode('/', trim($path, '/'));
                            $breadcrumbPath = '';
                        @endphp

                        @foreach($segments as $index => $segment)
                            @php
                                $breadcrumbPath .= '/' . $segment;
                            @endphp
                            <flux:breadcrumbs.item
                                href="{{ route('storage.connect', ['id' => $connection->id, 'path' => ltrim($breadcrumbPath, '/')]) }}"
                                separator="slash">{{ $segment }}</flux:breadcrumbs.item>
                        @endforeach
                        </flux:breadcrumb>
                </div>
                {{-- <div class="flex justify-between items-center mb-6 bg-white rounded-lg">
                    <form method="POST" action="{{ route('storage.create.folder', ['connectionId' => $connection->id]) }}"
                        class="flex w-full space-x-2">
                        @csrf
                        <input type="hidden" name="current_path" value="{{ $path }}">

                        <!-- Folder Name Input -->
                        <flux:input type="text" placeholder="New Folder Name" name="folder_name" required max="255" />
                        <flux:button type="submit" :loading="false" color="zinc" class="flex items-center space-x-1">
                            <flux:icon name="plus" class="w-5 h-5" />
                            <span>Create Folder</span>
                        </flux:button>
                    </form>
                </div> --}}
                <div x-data="storageManager(@js($folders), @js($files), @js($path))" class="space-y-6">

                    <!-- Toolbar -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <flux:modal.trigger name="folder-create">
                            <flux:button size="sm" variant="filled">
                                <flux:icon name="folder" class="w-5 h-5" />
                                <span>Create Folder</span>
                            </flux:button>
                        </flux:modal.trigger>
                        <flux:modal.trigger name="upload">
                            <flux:button size="sm" variant="filled">
                                <flux:icon name="upload" class="w-5 h-5" />
                                <span>Upload Files</span>
                            </flux:button>
                        </flux:modal.trigger>

                        <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

                        <!-- Select All -->
                        <flux:button size="sm" variant="filled" x-on:click="toggleSelectAll()">
                            <flux:icon name="layout-list" class="w-5 h-5" />
                            <span x-text="allSelected ? 'Deselect All' : 'Select All'"></span>
                        </flux:button>

                        <!-- Delete Button (Unified) -->
                        <template x-if="totalSelected > 0">
                            <flux:modal.trigger name="delete-items">
                                <flux:button size="sm" variant="danger">
                                    <flux:icon name="trash" class="w-5 h-5" />
                                    <span>Delete (<span x-text="totalSelected"></span>)</span>
                                </flux:button>
                            </flux:modal.trigger>
                        </template>
                    </div>

                    <!-- Folders and Files List -->
                    <div class="bg-white dark:bg-zinc-800 rounded-lg p-2">
                        <ul class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            <!-- Folders -->
                            @foreach ($folders as $folder)
                                                    @php
                                                        $fullPath = trim($path, '/') . '/' . $folder;
                                                        $fullPath = ltrim($fullPath, '/');
                                                    @endphp
                                 <li
                                                        class="flex items-center gap-3 p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 rounded-md transition-colors group">
                                                        <flux:checkbox x-bind:checked="isFolderSelected('{{ $fullPath }}')"
                                                            x-on:click="toggleFolder('{{ $fullPath }}')" />
                                                        <a href="{{ route('storage.connect', ['id' => $connection->id, 'path' => $fullPath]) }}"
                                                            class="flex-1 flex items-center gap-3 text-zinc-700 dark:text-zinc-300">
                                                            <flux:icon name="folder" class="w-5 h-5" />
                                                            <span class="truncate font-medium">{{ $folder }}</span>
                                                        </a>
                                                    </li>
                            @endforeach

                            <!-- Files -->
                            @foreach ($files as $file)
                                                    @php
                                                        $filePath = ltrim(rtrim($path, '/') . '/' . basename($file), '/');
                                                        $fileUrl = Storage::disk('connected_storage')->url($filePath);
                                                    @endphp
                                    <li class="flex items-center gap-3 p-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 rounded-md transition-colors">
                                        <flux:checkbox x-bind:checked="isFileSelected('{{ $filePath }}')"
                                            x-on:click="toggleFile('{{ $filePath }}')" />
                                        <div class="flex-1 flex items-center gap-3 min-w-0">
                                            <flux:icon name="file" class="w-5 h-5 text-zinc-500" />
                                            <a href="{{ $fileUrl }}" target="_blank"
                                                class="truncate hover:underline text-sm text-zinc-700 dark:text-zinc-300">
                                                {{ basename($file) }}
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                        </ul>
                    </div>

                    @if((empty($folders) || count($folders) === 0) && (empty($files) || count($files) === 0))
                        <div class="flex flex-col items-center justify-center py-12 text-zinc-400">
                            <flux:icon name="folder-open" class="w-12 h-12 mb-2 opacity-50" />
                            <p>This folder is empty</p>
                        </div>
                    @endif


                    <!-- Delete Confirmation Modal -->
                    <flux:modal name="delete-items" class="min-w-[22rem]">
                        <form method="POST" action="{{ route('storage.delete-items', $connection->id) }}">
                            @csrf

                            <!-- Hidden Inputs -->
                            <template x-for="folder in selectedFolders" :key="'folder-'+folder">
                                <input type="hidden" name="folders[]" :value="folder">
                            </template>
                            <template x-for="file in selectedFiles" :key="'file-'+file">
                                <input type="hidden" name="files[]" :value="file">
                            </template>

                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg" class="text-red-600">Delete Items?</flux:heading>
                                    <flux:text class="mt-2">
                                        You are about to delete <strong x-text="totalSelected"></strong> item(s).
                                        <br>This action cannot be undone.
                                    </flux:text>
                                </div>

                                <div class="flex gap-2 justify-end">
                                    <flux:modal.close>
                                        <flux:button variant="ghost">Cancel</flux:button>
                                    </flux:modal.close>
                                    <flux:button variant="danger" type="submit">Confirm Delete</flux:button>
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
                    <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-zinc-300 dark:border-zinc-600 p-10 text-center cursor-pointer"
                        :class="dragging ? 'border-blue-500' : ''" x-on:dragover.prevent="dragging = true"
                        x-on:dragleave.prevent="dragging = false" x-on:drop.prevent="dropFiles($event)"
                        x-on:click="$refs.fileInput.click()">
                        <flux:icon name="upload" class="w-12 h-12 text-zinc-400 mb-3" />
                        <p class="font-medium text-zinc-700 dark:text-zinc-200">Drop files here</p>
                        <p class="text-sm text-zinc-500 mt-1">or click to browse</p>

                        <input type="file" multiple class="hidden" x-ref="fileInput" x-on:change="selectFiles($event)"
                            accept=".jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,.ico,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.odt,.zip,.rar,.7z,.tar,.gz,.mp3,.wav,.ogg,.m4a,.flac,.aac,.mp4,.webm,.mov,.avi,.mkv,.flv,.json,.xml,.yml,.yaml,.md,.log,.html,.css" />
                    </div>

                    <!-- Selected Files -->
                    <template x-if="files.length">
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <template x-for="(file, index) in files" :key="file.name">
                                <div
                                    class="flex items-center justify-between rounded-lg bg-zinc-100 dark:bg-zinc-700 p-3">
                                    <div class="flex items-center gap-3">
                                        <!-- Thumbnail if image -->
                                        <template x-if="file.type.startsWith('image/')">
                                            <flux:modal.trigger name="imagePreview">
                                                <flux:tooltip content="Preview">
                                                    <img :src="URL.createObjectURL(file)"
                                                        class="w-10 h-10 object-cover rounded" alt=""
                                                        x-on:click="preview(file)" />
                                                </flux:tooltip>
                                            </flux:modal.trigger>
                                        </template>

                                        <!-- File icon if not image -->
                                        <template x-if="!file.type.startsWith('image/')">
                                            <flux:icon name="file" class="w-10 h-10 text-zinc-400" />
                                        </template>

                                        <div class="truncate">
                                            <p class="text-sm font-medium" x-text="file.name"></p>
                                            <p class="text-xs text-zinc-500" x-text="formatSize(file.size)"></p>
                                        </div>
                                    </div>

                                    <!-- Delete button -->
                                    <flux:tooltip content="Remove file">
                                        <flux:icon name="trash" x-on:click="files.splice(index, 1)" class="w-4 h-4" />
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
                        <img x-bind:src="previewImage" alt="Preview"
                            class="w-full max-h-[70vh] object-contain rounded-lg" />
                        <div class="flex justify-end w-full">
                            <flux:modal.close>
                                <flux:button variant="ghost" data-flux-modal-close>Close</flux:button>
                            </flux:modal.close>
                        </div>
                    </div>
                </flux:modal>
            </flux:modal>
        </div>

        <flux:modal name="folder-create" class="[:where(&)]:max-w-md [:where(&)]:w-full">
            <div class="p-6 space-y-5">
                <!-- Header -->
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        Create New Folder
                    </h2>
                    <p class="text-sm text-zinc-500">
                        Folder will be created inside the current directory
                    </p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('storage.create.folder', ['connectionId' => $connection->id]) }}"
                    class="space-y-4">
                    @csrf

                    <!-- Current Path -->
                    <input type="hidden" name="current_path" value="{{ $path }}">

                    <!-- Folder Name -->
                    <flux:input type="text" name="folder_name" placeholder="New folder name" required maxlength="255"
                        autofocus />

                    <!-- Footer -->
                    <div class="flex justify-end gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:modal.close>
                            <flux:button variant="ghost" type="reset">
                                Cancel
                            </flux:button>
                        </flux:modal.close>

                        <flux:button icon="plus" type="submit" variant="primary">
                            <span>Create</span>
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        window.axios = axios; // optional, ensures global scope
    </script>
    <script>
        function uploadModal() {
            return {
                files: [],
                previewImage: null,
                dragging: false,
                maxFiles: 20,
                maxSize: 10 * 1024 * 1024, // 10MB
                allowedTypes: [
                    'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp', 'image/tiff', 'image/svg+xml',
                    'image/x-icon', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain', 'text/csv', 'application/rtf', 'application/vnd.oasis.opendocument.text',
                    'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
                    'application/x-tar', 'application/gzip',
                    'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/flac', 'audio/aac',
                    'video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/x-flv',
                    'application/json', 'application/xml', 'text/yaml', 'text/markdown', 'text/html', 'text/css', 'text/log'
                ],
                errorMessage: '',

                reset() {
                    this.files = []
                    this.previewImage = null
                    this.errorMessage = ''
                    if (this.$refs.fileInput) this.$refs.fileInput.value = ''
                },

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
                    if (this.files.length === 0) {
                        this.errorMessage = 'No files selected.';
                        return;
                    }

                    // Push files to queue **and get their IDs**
                    const filesWithIds = this.files.map(file => {
                        const id = Date.now() + Math.random();
                        Alpine.store('uploadQueue').queue.push({
                            id,
                            name: file.name,
                            progress: 0,
                            status: 'Pending',
                            file: file,
                        });
                        return { file, id }; // save id to use in upload
                    });

                    Flux.modal('upload').close();
                    this.files = [];
                    this.errorMessage = '';

                    filesWithIds.forEach(({ file, id }) => {
                        const formData = new FormData();
                        formData.append('files[]', file);
                        formData.append('current_path', @json($path));

                        axios.post(`/storage/${@json($connection->id)}/upload`, formData, {
                            headers: { 'Content-Type': 'multipart/form-data' },
                            onUploadProgress: (event) => {
                                const percent = Math.round((event.loaded * 100) / event.total);
                                Alpine.store('uploadQueue').updateProgress(id, percent);
                            }
                        }).then(res => {
                            Alpine.store('uploadQueue').updateStatus(id, res.data.success ? 'Success' : 'Failed');
                        }).catch(error => {
                            let message = 'Failed';
                            if (error.response && error.response.status === 422) {
                                if (error && error['files.0']) {
                                    message = error['files.0'][0];
                                }
                            }
                            Alpine.store('uploadQueue').updateStatus(id, message);
                        });
                    });
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

        function storageManager(folders = [], files = [], currentPath = '') {
            return {
                selectedFolders: [],
                selectedFiles: [],
                folders: folders,
                files: files.map(f => {
                    // Need to match the path generation logic in blade loop for consistency
                    // In blade: $filePath = ltrim(rtrim($path, '/') . '/' . basename($file), '/');
                    let path = currentPath.replace(/\/$/, '') + '/' + f.split('/').pop();
                    return path.replace(/^\//, '');
                }),
                path: currentPath,

                // Computed: All Selected
                get allSelected() {
                    return (this.folders.length > 0 && this.selectedFolders.length === this.folders.length) &&
                        (this.files.length > 0 && this.selectedFiles.length === this.files.length);
                },

                get totalSelected() {
                    return this.selectedFolders.length + this.selectedFiles.length;
                },

                // Folder Logic
                toggleFolder(folderPath) {
                    if (this.selectedFolders.includes(folderPath)) {
                        this.selectedFolders = this.selectedFolders.filter(f => f !== folderPath);
                    } else {
                        this.selectedFolders.push(folderPath);
                    }
                },
                isFolderSelected(folderPath) {
                    return this.selectedFolders.includes(folderPath);
                },

                // File Logic
                toggleFile(filePath) {
                    if (this.selectedFiles.includes(filePath)) {
                        this.selectedFiles = this.selectedFiles.filter(f => f !== filePath);
                    } else {
                        this.selectedFiles.push(filePath);
                    }
                },
                isFileSelected(filePath) {
                    return this.selectedFiles.includes(filePath);
                },

                // Bulk Logic
                toggleSelectAll() {
                    if (this.totalSelected === (this.folders.length + this.files.length)) {
                        // Deselect All
                        this.selectedFolders = [];
                        this.selectedFiles = [];
                    } else {
                        // Select All
                        // Reconstruct full paths for folders as done in blade
                        this.selectedFolders = this.folders.map(f => {
                            let p = this.path.replace(/\/$/, '') + '/' + f;
                            return p.replace(/^\//, '');
                        });
                        this.selectedFiles = [...this.files];
                    }
                }
            }
        }
    </script>
    </x-app-layout>