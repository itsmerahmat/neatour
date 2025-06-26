<?php

namespace App\Http\Controllers;

use App\Services\ImageKitService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Facades\ImageKit;

class ImageKitController extends Controller
{
    protected ImageKitService $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    /**
     * Upload a file to ImageKit
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|max:10240', // Max 10MB
            'filename' => 'sometimes|string|max:255',
        ]);

        $file = $request->file('file');
        $fileName = $request->input('filename', $file->getClientOriginalName());

        // Additional upload options
        $options = [
            'useUniqueFileName' => true,
            'folder' => '/uploads',
            'isPrivateFile' => false,
        ];

        // Add tags if provided
        if ($request->has('tags')) {
            $options['tags'] = implode(',', $request->input('tags', []));
        }

        $result = $this->imageKitService->uploadFile($file, $fileName, $options);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Generate URL with transformations
     */
    public function generateUrl(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'transformations' => 'sometimes|array',
        ]);

        $path = $request->input('path');
        $transformations = $request->input('transformations', []);

        // Using the service directly
        $url = $this->imageKitService->url($path, $transformations);

        // Or using the facade
        // $url = ImageKit::url($path, $transformations);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }

    /**
     * Generate signed URL
     */
    public function generateSignedUrl(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'transformations' => 'sometimes|array',
            'expire_seconds' => 'sometimes|integer|min:1|max:86400', // Max 24 hours
        ]);

        $path = $request->input('path');
        $transformations = $request->input('transformations', []);
        $expireSeconds = $request->input('expire_seconds', 300);

        $url = $this->imageKitService->signedUrl($path, $transformations, $expireSeconds);

        return response()->json([
            'success' => true,
            'signed_url' => $url,
            'expires_in' => $expireSeconds
        ]);
    }

    /**
     * Get authentication parameters for client-side upload
     */
    public function getAuthParams(): JsonResponse
    {
        $authParams = $this->imageKitService->getAuthenticationParameters();

        return response()->json([
            'success' => true,
            'auth_params' => $authParams
        ]);
    }

    /**
     * Delete a file
     */
    public function deleteFile(Request $request): JsonResponse
    {
        $request->validate([
            'file_id' => 'required|string',
        ]);

        $fileId = $request->input('file_id');
        $success = $this->imageKitService->deleteFile($fileId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'File deleted successfully' : 'Failed to delete file'
        ]);
    }

    /**
     * Get file details
     */
    public function getFileDetails(Request $request): JsonResponse
    {
        $request->validate([
            'file_id' => 'required|string',
        ]);

        $fileId = $request->input('file_id');
        $details = $this->imageKitService->getFileDetails($fileId);

        if (!$details) {
            return response()->json([
                'success' => false,
                'message' => 'File not found or error occurred'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $details
        ]);
    }

    /**
     * List files
     */
    public function listFiles(Request $request): JsonResponse
    {
        $options = [];

        if ($request->has('limit')) {
            $options['limit'] = $request->integer('limit', 10);
        }

        if ($request->has('skip')) {
            $options['skip'] = $request->integer('skip', 0);
        }

        if ($request->has('folder')) {
            $options['path'] = $request->string('folder');
        }

        $files = $this->imageKitService->listFiles($options);

        if (!$files) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve files'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * Calculate pHash distance between two images
     */
    public function pHashDistance(Request $request): JsonResponse
    {
        $request->validate([
            'hash1' => 'required|string',
            'hash2' => 'required|string',
        ]);

        $hash1 = $request->input('hash1');
        $hash2 = $request->input('hash2');

        $distance = $this->imageKitService->pHashDistance($hash1, $hash2);

        return response()->json([
            'success' => true,
            'distance' => $distance,
            'similarity' => $this->getSimilarityLevel($distance)
        ]);
    }

    /**
     * Get similarity level based on pHash distance
     */
    private function getSimilarityLevel(int $distance): string
    {
        if ($distance == 0) {
            return 'identical';
        } elseif ($distance <= 10) {
            return 'very_similar';
        } elseif ($distance <= 20) {
            return 'similar';
        } else {
            return 'different';
        }
    }
}
