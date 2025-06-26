<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageKitService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImageUploadController extends Controller
{
    protected ImageKitService $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

    /**
     * Upload image and return ImageKit URL
     */
    public function uploadDestinationImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $fileName = 'destination_' . time() . '_' . $file->getClientOriginalName();

        $uploadResult = $this->imageKitService->uploadFile($file, $fileName, [
            'folder' => '/destinations',
            'useUniqueFileName' => true,
            'tags' => ['destination', 'thumbnail'],
            'isPrivateFile' => false,
        ]);

        if (!$uploadResult) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $uploadResult['url'],
                'fileId' => $uploadResult['fileId'],
                'fileName' => $uploadResult['name'],
                'thumbnailUrl' => $uploadResult['thumbnailUrl'] ?? null,
            ]
        ]);
    }

    /**
     * Delete image from ImageKit
     */
    public function deleteImage(Request $request): JsonResponse
    {
        $request->validate([
            'file_id' => 'required|string',
        ]);

        $fileId = $request->input('file_id');
        $success = $this->imageKitService->deleteFile($fileId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Image deleted successfully' : 'Failed to delete image'
        ]);
    }

    /**
     * Generate optimized URL
     */
    public function getOptimizedUrl(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'width' => 'sometimes|integer|min:1|max:2000',
            'height' => 'sometimes|integer|min:1|max:2000',
            'quality' => 'sometimes|integer|min:1|max:100',
        ]);

        $path = $request->input('path');
        $transformations = [];

        if ($request->has('width') || $request->has('height') || $request->has('quality')) {
            $transformation = [];
            
            if ($request->has('width')) {
                $transformation['width'] = $request->integer('width');
            }
            
            if ($request->has('height')) {
                $transformation['height'] = $request->integer('height');
            }
            
            if ($request->has('quality')) {
                $transformation['quality'] = $request->integer('quality');
            }
            
            $transformations[] = $transformation;
        }

        $url = $this->imageKitService->url($path, $transformations);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
