<?php

namespace App\Services;

use ImageKit\ImageKit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageKitService
{
    protected ImageKit $imageKit;

    public function __construct(ImageKit $imageKit)
    {
        $this->imageKit = $imageKit;
    }

    /**
     * Upload file to ImageKit
     *
     * @param UploadedFile|string $file
     * @param string $fileName
     * @param array $options
     * @return array|null
     */
    public function uploadFile($file, string $fileName, array $options = []): ?array
    {
        try {
            // Get file for ImageKit (try binary method as in official sample)
            if ($file instanceof UploadedFile) {
                // Use file resource as in ImageKit sample: fopen($file, 'r')
                $fileResource = fopen($file->getPathname(), 'r');
                
                Log::info('ImageKit upload file info', [
                    'fileName' => $fileName,
                    'originalName' => $file->getClientOriginalName(),
                    'mimeType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'filePath' => $file->getPathname()
                ]);
                
                $fileForUpload = $fileResource;
            } else {
                // If it's raw content, encode as base64
                if (is_string($file)) {
                    $fileForUpload = base64_encode($file);
                } else {
                    $fileForUpload = $file;
                }
            }
            
            $uploadData = [
                'file' => $fileForUpload,
                'fileName' => $fileName,
            ];

            // Merge additional options
            $uploadData = array_merge($uploadData, $options);
            
            Log::info('ImageKit upload data prepared', [
                'fileName' => $fileName,
                'options' => $options,
                'dataKeys' => array_keys($uploadData)
            ]);

            $result = $this->imageKit->uploadFile($uploadData);

            // Close file resource if it was opened
            if ($file instanceof UploadedFile && is_resource($fileForUpload)) {
                fclose($fileForUpload);
            }

            // Handle ImageKit Response object
            if (isset($result->error) && $result->error) {
                Log::error('ImageKit upload error', [
                    'error' => (array) $result->error,
                    'fileName' => $fileName
                ]);
                return null;
            }

            // Convert result to array
            $resultArray = $result->result ? (array) $result->result : null;
            
            if ($resultArray) {
                Log::info('ImageKit upload success', [
                    'fileId' => $resultArray['fileId'] ?? 'unknown',
                    'url' => $resultArray['url'] ?? 'unknown',
                    'name' => $resultArray['name'] ?? 'unknown',
                    'size' => $resultArray['size'] ?? 'unknown',
                    'fileType' => $resultArray['fileType'] ?? 'unknown',
                    'filePath' => $resultArray['filePath'] ?? 'unknown'
                ]);
                
                // Test URL accessibility immediately after upload
                $this->testUrlAccessibility($resultArray['url'] ?? '');
            }
            
            return $resultArray;
        } catch (\Exception $e) {
            Log::error('ImageKit upload exception: ' . $e->getMessage(), [
                'fileName' => $fileName,
                'exception' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Generate URL for an image
     *
     * @param string $path
     * @param array $transformations
     * @return string
     */
    public function url(string $path, array $transformations = []): string
    {
        try {
            $urlOptions = ['path' => $path];
            
            if (!empty($transformations)) {
                $urlOptions['transformation'] = $transformations;
            }

            return $this->imageKit->url($urlOptions);
        } catch (\Exception $e) {
            Log::error('ImageKit URL generation error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Generate a signed URL
     *
     * @param string $path
     * @param array $transformations
     * @param int $expireSeconds
     * @return string
     */
    public function signedUrl(string $path, array $transformations = [], int $expireSeconds = 300): string
    {
        try {
            $urlOptions = [
                'path' => $path,
                'signed' => true,
                'expireSeconds' => $expireSeconds,
            ];
            
            if (!empty($transformations)) {
                $urlOptions['transformation'] = $transformations;
            }

            return $this->imageKit->url($urlOptions);
        } catch (\Exception $e) {
            Log::error('ImageKit signed URL generation error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Generate authentication parameters for client-side upload
     *
     * @param string $token
     * @param int $expire
     * @return array
     */
    public function getAuthenticationParameters(string $token = '', int $expire = 0): array
    {
        try {
            $result = $this->imageKit->getAuthenticationParameters($token, $expire);
            return (array) $result;
        } catch (\Exception $e) {
            Log::error('ImageKit authentication parameters error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate distance between two pHash values
     *
     * @param string $firstHash
     * @param string $secondHash
     * @return int
     */
    public function pHashDistance(string $firstHash, string $secondHash): int
    {
        try {
            return $this->imageKit->pHashDistance($firstHash, $secondHash);
        } catch (\Exception $e) {
            Log::error('ImageKit pHash distance calculation error: ' . $e->getMessage());
            return -1;
        }
    }

    /**
     * Delete file from ImageKit
     *
     * @param string $fileId
     * @return bool
     */
    public function deleteFile(string $fileId): bool
    {
        try {
            $result = $this->imageKit->deleteFile($fileId);
            return !isset($result->error) || !$result->error;
        } catch (\Exception $e) {
            Log::error('ImageKit delete file error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file details
     *
     * @param string $fileId
     * @return array|null
     */
    public function getFileDetails(string $fileId): ?array
    {
        try {
            $result = $this->imageKit->getFileDetails($fileId);
            
            if (isset($result->error) && $result->error) {
                Log::error('ImageKit get file details error', (array) $result->error);
                return null;
            }

            return $result->result ? (array) $result->result : null;
        } catch (\Exception $e) {
            Log::error('ImageKit get file details exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update file details
     *
     * @param string $fileId
     * @param array $updateData
     * @return array|null
     */
    public function updateFileDetails(string $fileId, array $updateData): ?array
    {
        try {
            $result = $this->imageKit->updateFileDetails($fileId, $updateData);
            
            if (isset($result->error) && $result->error) {
                Log::error('ImageKit update file details error', (array) $result->error);
                return null;
            }

            return $result->result ? (array) $result->result : null;
        } catch (\Exception $e) {
            Log::error('ImageKit update file details exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * List files from ImageKit
     *
     * @param array $options
     * @return array|null
     */
    public function listFiles(array $options = []): ?array
    {
        try {
            $result = $this->imageKit->listFiles($options);
            
            if (isset($result->error) && $result->error) {
                Log::error('ImageKit list files error', (array) $result->error);
                return null;
            }

            return $result->result ? (array) $result->result : null;
        } catch (\Exception $e) {
            Log::error('ImageKit list files exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Test if URL is accessible
     *
     * @param string $url
     * @return void
     */
    private function testUrlAccessibility(string $url): void
    {
        if (empty($url)) {
            Log::warning('Empty URL provided for accessibility test');
            return;
        }

        try {
            Log::info('Testing URL accessibility', ['url' => $url]);
            
            // Use cURL for better control
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true); // Only get headers
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            if ($error) {
                Log::warning('URL accessibility test failed with cURL error', [
                    'url' => $url,
                    'error' => $error
                ]);
            } elseif ($httpCode === 200) {
                Log::info('URL is accessible', [
                    'url' => $url,
                    'httpCode' => $httpCode,
                    'contentType' => $contentType
                ]);
            } else {
                Log::warning('URL returned non-200 status', [
                    'url' => $url,
                    'httpCode' => $httpCode,
                    'contentType' => $contentType
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Exception during URL accessibility test', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
        }
    }
}
