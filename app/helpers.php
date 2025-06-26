<?php

if (!function_exists('imagekit_url')) {
    /**
     * Generate ImageKit URL
     *
     * @param string $path
     * @param array $transformations
     * @return string
     */
    function imagekit_url(string $path, array $transformations = []): string
    {
        return app(\App\Services\ImageKitService::class)->url($path, $transformations);
    }
}

if (!function_exists('imagekit_signed_url')) {
    /**
     * Generate ImageKit signed URL
     *
     * @param string $path
     * @param array $transformations
     * @param int $expireSeconds
     * @return string
     */
    function imagekit_signed_url(string $path, array $transformations = [], int $expireSeconds = 300): string
    {
        return app(\App\Services\ImageKitService::class)->signedUrl($path, $transformations, $expireSeconds);
    }
}

if (!function_exists('imagekit_upload')) {
    /**
     * Upload file to ImageKit
     *
     * @param mixed $file
     * @param string $fileName
     * @param array $options
     * @return array|null
     */
    function imagekit_upload($file, string $fileName, array $options = []): ?array
    {
        return app(\App\Services\ImageKitService::class)->uploadFile($file, $fileName, $options);
    }
}

if (!function_exists('destination_image_url')) {
    /**
     * Generate optimized destination image URL
     *
     * @param string $imageUrl
     * @param string $size
     * @return string
     */
    function destination_image_url(string $imageUrl, string $size = 'medium'): string
    {
        if (!$imageUrl || !str_contains($imageUrl, 'ik.imagekit.io')) {
            return $imageUrl;
        }

        $transformations = match($size) {
            'thumbnail' => [['width' => 400, 'height' => 300, 'quality' => 100, 'crop' => 'maintain_ratio']],
            'medium' => [['width' => 800, 'height' => 600, 'quality' => 100, 'crop' => 'maintain_ratio']],
            'large' => [['width' => 1200, 'height' => 800, 'quality' => 100, 'crop' => 'maintain_ratio']],
            'hero' => [['width' => 1200, 'height' => 600, 'quality' => 100, 'crop' => 'maintain_ratio']],
            'original' => [], // No transformations at all
            default => []
        };

        // Extract just the file path from the full URL
        $urlParts = parse_url($imageUrl);
        if (!$urlParts || !isset($urlParts['path'])) {
            return $imageUrl;
        }
        
        // Remove the leading slash and endpoint path to get just the file path
        $path = ltrim($urlParts['path'], '/');
        
        // Remove the ImageKit endpoint identifier if present
        if (str_starts_with($path, 'mu06faxxn/')) {
            $path = substr($path, strlen('mu06faxxn/'));
        }
        
        // Ensure path starts with a slash for ImageKit URL generation
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return imagekit_url($path, $transformations);
    }
}

if (!function_exists('destination_image_srcset')) {
    /**
     * Generate responsive srcset for destination images
     *
     * @param string $imageUrl
     * @return string
     */
    function destination_image_srcset(string $imageUrl): string
    {
        if (!$imageUrl || !str_contains($imageUrl, 'ik.imagekit.io')) {
            return $imageUrl;
        }

        $urlParts = parse_url($imageUrl);
        $path = $urlParts['path'] ?? '';

        $sizes = [
            '400w' => [['width' => 400, 'quality' => 100]],
            '800w' => [['width' => 800, 'quality' => 100]],
            '1200w' => [['width' => 1200, 'quality' => 100]],
        ];

        $srcset = [];
        foreach ($sizes as $size => $transformations) {
            $srcset[] = imagekit_url($path, $transformations) . ' ' . $size;
        }

        return implode(', ', $srcset);
    }
}

if (!function_exists('destination_original_url')) {
    /**
     * Get original destination image URL without any transformations
     *
     * @param string $imageUrl
     * @return string
     */
    function destination_original_url(string $imageUrl): string
    {
        // Return the original URL as-is without any transformations
        return $imageUrl;
    }
}
