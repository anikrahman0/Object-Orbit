<?php

namespace App\Http\Controllers;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageConnectionService;

class S3Controller extends Controller
{
    public function storageList()
    {
        // $r = Storage::disk('do_spaces')->files('media/bangla/imgAll/2025October');
        // Storage::disk('do_spaces')->makeDirectory('somoytv/media/bangla');
        // Storage::disk('do_spaces')->makeDirectory('somoytv/media/english');

        // $directories = Storage::disk('do_spaces')->allDirectories();
        // dd($r, $directories);
        return view('storage.index', [
            'storages' => auth()->user()->storageConnection()->paginate(10),
        ]);
    }

    public function storageCreate()
    {
        return view('storage.create');
    }
    public function storageStore(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255|unique:storage_connections,key',
            'secret' => 'required|string|max:60000|unique:storage_connections,secret',
            'region' => 'required|string|max:20',
            'bucket' => 'required|string|max:100',
            'endpoint' => 'nullable|url|max:255',
            'use_path_style' => 'nullable',
        ]);

        auth()->user()->storageConnection()->create([
            'key' => $data['key'],
            'secret' => $data['secret'],
            'region' => $data['region'],
            'bucket' => $data['bucket'],
            'endpoint' => $data['endpoint'] ?? null,
            'use_path_style' => $request->has('use_path_style'),
        ]);

        return to_route('storage.list')->with('success', 'Storage connection saved!');
    }



    public function storageConnect($id, StorageConnectionService $service)
    {
        $connection = auth()->user()->storageConnection()->findOrFail($id);

        $service->registerDisk($connection->toArray(), 'connected_storage');

        // Get path from query param or default to root
        $path = request()->query('path', '/');
        $disk = Storage::disk('connected_storage');

        try {
            // Get raw directories and files
            $rawFolders = $disk->directories($path);
            $files = $disk->files($path);

            // Normalize path prefix
            $prefix = trim($path, '/') !== '' ? trim($path, '/') . '/' : '';

            // Extract only immediate child folders
            $folders = collect($rawFolders)->map(function ($folder) use ($prefix) {
                return trim(Str::after($folder, $prefix), '/');
            })->filter(function ($folder) {
                return !str_contains($folder, '/'); // Only direct folders
            })->unique()->values();

            return view('storage.folders', compact('folders', 'files', 'path', 'connection'));
        } catch (\Exception $e) {
            Log::error('S3 connection failed: ' . $e->getMessage());

            return view('storage.folders', [
                'folders' => collect(),
                'files' => collect(),
                'path' => $path,
                'connection' => $connection,
            ])->withErrors('Connection failed: ' . $e->getMessage());
        }
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

            return back()->with('success', 'Selected folders deleted successfully.');
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
}
