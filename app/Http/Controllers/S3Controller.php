<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\StorageConnectionService;
use App\Http\Requests\StorageConnectionRequest;

class S3Controller extends Controller
{
    public function storageList()
    {
        return view('storage.index', [
            'storages' => auth()->user()->storageConnection()->paginate(10),
        ]);
    }
    public function storageCreate()
    {
        return view('storage.create');
    }
    public function storageStore(StorageConnectionRequest $request)
    {
        auth()->user()->storageConnection()->create([
            ...$request->validated(),
            'endpoint' => $request->validated('endpoint'),
            'use_path_style' => $request->boolean('use_path_style'),
        ]);

        return to_route('storage.list')->with('success', 'Storage connection saved!');
    }



    public function storageEdit($id)
    {
        $storage = auth()->user()->storageConnection()->findOrFail($id);
        return view('storage.edit', compact('storage'));
    }

    public function storageUpdate(StorageConnectionRequest $request, $id)
    {
        $storage = auth()->user()->storageConnection()->findOrFail($id);

        $storage->update([
            ...$request->validated(),
            'use_path_style' => $request->boolean('use_path_style'),
        ]);

        return to_route('storage.list')->with('success', 'Storage connection updated!');
    }



    public function storageDelete($id)
    {
        $storage = auth()->user()->storageConnection()->findOrFail($id);

        // Soft delete
        $storage->delete();

        return to_route('storage.list')->with('success', 'Storage connection deleted!');
    }


    public function storageConnect($id, StorageConnectionService $service)
    {
        $connection = auth()->user()->storageConnection()->findOrFail($id);

        $service->registerDisk($connection->toArray(), 'connected_storage');

        $path = request()->query('path', '/');
        $disk = Storage::disk('connected_storage');

        try {
            $rawFolders = $disk->directories($path);
            $files = $disk->files($path);

            $prefix = trim($path, '/') !== '' ? trim($path, '/') . '/' : '';

            $folders = collect($rawFolders)->map(function ($folder) use ($prefix) {
                return trim(Str::after($folder, $prefix), '/');
            })->filter(function ($folder) {
                return !str_contains($folder, '/'); // Only direct folders
            })->unique()->values();

            return view('storage.folders', [
                'folders' => $folders,
                'files' => $files,
                'path' => $path,
                'connection' => $connection,
                'connectionError' => null, // No error
            ]);
        } catch (\Exception $e) {
            Log::error('S3 connection failed: ' . $e->getMessage());

            // Clean message for UI (no line breaks, no layout breaking)
            $connectionError = 'Connection failed: ' . trim(
                preg_replace('/\s+/', ' ', $e->getMessage())
            );

            return view('storage.folders', [
                'folders' => collect(),
                'files' => collect(),
                'path' => $path,
                'connection' => $connection,
                'connectionError' => $connectionError, // Pass cleaned error
            ]);
        }
    }

    public function uploadFilesToFolder(Request $request, $connectionId, StorageConnectionService $service)
    {
        // Validate max 20 files
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|max:20',
            'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff,ico,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,odt,zip,rar,7z,tar,gz,mp3,wav,ogg,m4a,flac,aac,mp4,webm,mov,avi,mkv,flv,json,xml,yaml,yml,md,log,html,css',
            'current_path' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($request->file('files', []) as $index => $file) {
                $key = "files.$index";
                if ($validator->errors()->has($key)) {
                    $errors[$file->getClientOriginalName()] = $validator->errors()->first($key);
                }
            }

            return response()->json([
                'success' => false,
                'errors' => $errors,
            ], 422);
        }

        $connection = auth()->user()->storageConnection()->findOrFail($connectionId);

        // Register disk dynamically
        $service->registerDisk($connection->toArray(), 'connected_storage');
        $disk = Storage::disk('connected_storage');

        $currentPath = $request->input('current_path', '/');

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $filename = $file->getClientOriginalName();

                // Debug log
                Log::info('Uploading file', [
                    'file' => $filename,
                    'current_path' => $currentPath
                ]);

                // Upload to disk
                $disk->putFileAs($currentPath, $file, $filename, 'public');

                $uploadedFiles[] = $filename;
            } catch (\Exception $e) {
                Log::error('Upload failed', [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
                $errors[] = $file->getClientOriginalName();
            }
        }

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'uploaded' => $uploadedFiles,
                'failed' => $errors
            ], 500);
        }

        return response()->json([
            'success' => true,
            'uploaded' => $uploadedFiles
        ]);
    }


    public function deleteMultipleFolders($id, Request $request, StorageConnectionService $service)
    {
        $folders = $request->input('folders', []);

        if (empty($folders)) {
            return back()->with('error', 'No folders selected for deletion.');
        }

        $connection = auth()->user()->storageConnection()->findOrFail($id);

        $service->registerDisk($connection->toArray(), 'connected_storage');

        $disk = Storage::disk('connected_storage');

        try {
            foreach ($folders as $folderPath) {
                $files = $disk->allFiles($folderPath);
                $disk->delete($files);

                $directories = $disk->allDirectories($folderPath);
                foreach ($directories as $dir) {
                    $disk->deleteDirectory($dir);
                }

                $disk->deleteDirectory($folderPath);
            }

            return back()->with('success', 'Selected folder(s) deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete folders: ' . $e->getMessage());
        }
    }


    public function createFolder(Request $request, $connectionId, StorageConnectionService $service)
    {
        $request->validate([
            'folder_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9-_]+$/'],
            'current_path' => ['nullable', 'string'],
        ]);

        $connection = auth()->user()->storageConnection()->findOrFail($connectionId);

        $service->registerDisk($connection->toArray(), 'connected_storage');

        $disk = Storage::disk('connected_storage');

        $folderName = $request->folder_name;
        $currentPath = trim($request->current_path ?? '', '/');

        $folderPath = ($currentPath === '') ? $folderName : $currentPath . '/' . $folderName;

        try {
            $disk->makeDirectory($folderPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Folder creation failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Folder '$folderName' created successfully.");
    }


    public function deleteMultipleFiles($id, Request $request, StorageConnectionService $service)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'string',
        ]);
        $files = $request->input('files', []); // Expect full paths for each file

        if (empty($files)) {
            return back()->with('error', 'No files selected for deletion.');
        }

        $connection = auth()->user()->storageConnection()->findOrFail($id);

        // Register the disk dynamically
        $service->registerDisk($connection->toArray(), 'connected_storage');
        $disk = Storage::disk('connected_storage');

        try {
            foreach ($files as $filePath) {
                if ($disk->exists($filePath)) {
                    $disk->delete($filePath);
                }
            }

            return back()->with('success', count($files) . ' file(s) deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete files: ' . $e->getMessage());
        }
    }
}
