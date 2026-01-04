<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class StorageConnectionService
{
    /**
     * Dynamically register a filesystem disk.
     *
     * @param array $connection  ['key', 'secret', 'region', 'bucket', 'endpoint', 'use_path_style']
     * @param string $diskName
     * @return string
     */
    public function registerDisk(array $connection, string $diskName = 'connected_storage'): string
    {
        // dd($connection['endpoint']);
        $config = [
            'driver' => 's3',
            'key' => $connection['key'],
            'secret' => $connection['secret'],
            'region' => $connection['region'],
            'bucket' => $connection['bucket'],
            'endpoint' => $connection['endpoint'] ?? null,
            'use_path_style_endpoint' => true,
            'throw' => false,
            'visibility' => 'public',
        ];
        
        Config::set("filesystems.disks.{$diskName}", $config);

        return $diskName;
    }
}
