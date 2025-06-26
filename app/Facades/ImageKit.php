<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null uploadFile($file, string $fileName, array $options = [])
 * @method static string url(string $path, array $transformations = [])
 * @method static string signedUrl(string $path, array $transformations = [], int $expireSeconds = 300)
 * @method static array getAuthenticationParameters(string $token = '', int $expire = 0)
 * @method static int pHashDistance(string $firstHash, string $secondHash)
 * @method static bool deleteFile(string $fileId)
 * @method static array|null getFileDetails(string $fileId)
 * @method static array|null updateFileDetails(string $fileId, array $updateData)
 * @method static array|null listFiles(array $options = [])
 *
 * @see \App\Services\ImageKitService
 */
class ImageKit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \App\Services\ImageKitService::class;
    }
}
