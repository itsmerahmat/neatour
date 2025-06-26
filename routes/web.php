<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageKitController;
use App\Http\Controllers\Api\ImageUploadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/katalog', [HomeController::class, 'katalog'])->name('katalog');
Route::get('/katalog/{id}', [HomeController::class, 'show'])->name('katalog.detail');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
    Route::resource('user', UserController::class);
    
    // Category routes dengan pembatasan akses hanya untuk superadmin
    Route::controller(CategoryController::class)->prefix('category')->name('category.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{category}', 'update')->name('update');
        Route::delete('/{category}', 'destroy')->name('destroy');
    });
});

// Destination routes
Route::resource('destination', DestinationController::class)->middleware(['auth']);

// Testimonial routes
Route::resource('testimonial', TestimonialController::class)->middleware(['auth']);

// ImageKit API routes
Route::middleware(['auth'])->prefix('api/imagekit')->name('imagekit.')->group(function () {
    Route::post('/upload', [ImageKitController::class, 'upload'])->name('upload');
    Route::post('/generate-url', [ImageKitController::class, 'generateUrl'])->name('generate.url');
    Route::post('/generate-signed-url', [ImageKitController::class, 'generateSignedUrl'])->name('generate.signed.url');
    Route::get('/auth-params', [ImageKitController::class, 'getAuthParams'])->name('auth.params');
    Route::delete('/delete-file', [ImageKitController::class, 'deleteFile'])->name('delete.file');
    Route::get('/file-details', [ImageKitController::class, 'getFileDetails'])->name('file.details');
    Route::get('/list-files', [ImageKitController::class, 'listFiles'])->name('list.files');
    Route::post('/phash-distance', [ImageKitController::class, 'pHashDistance'])->name('phash.distance');
});

// Image Upload API routes for destinations
Route::middleware(['auth'])->prefix('api/upload')->name('api.upload.')->group(function () {
    Route::post('/destination-image', [ImageUploadController::class, 'uploadDestinationImage'])->name('destination.image');
    Route::delete('/delete-image', [ImageUploadController::class, 'deleteImage'])->name('delete.image');
    Route::post('/optimized-url', [ImageUploadController::class, 'getOptimizedUrl'])->name('optimized.url');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
