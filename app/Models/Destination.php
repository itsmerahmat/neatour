<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'pic_id',
        'name',
        'thumb_image',
        'imagekit_file_id',
        'content',
        'facility',
        'lat',
        'lon',
        'address',
        'operating_hours',
        'published'
    ];

    protected $casts = [
        'published' => 'boolean',
        'lat' => 'float',
        'lon' => 'float',
    ];

    protected $appends = ['avg_rating', 'total_reviews'];

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'destination_category');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }
    
    /**
     * Get the average rating for this destination.
     *
     * @return float
     */
    public function getAvgRatingAttribute(): float
    {
        $testimonials = $this->testimonials;
        
        if ($testimonials->isEmpty()) {
            return 0;
        }
        
        return round($testimonials->avg('rating'), 1);
    }
    
    /**
     * Get the total number of reviews/testimonials for this destination.
     *
     * @return int
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->testimonials->count();
    }

    /**
     * Get optimized image URL with ImageKit transformations
     *
     * @param array $transformations
     * @return string
     */
    public function getOptimizedImageUrl(array $transformations = []): string
    {
        if (!$this->thumb_image) {
            return '';
        }

        // If it's already an ImageKit URL, generate optimized version
        if (str_contains($this->thumb_image, 'ik.imagekit.io')) {
            // Extract path from ImageKit URL
            $urlParts = parse_url($this->thumb_image);
            $path = $urlParts['path'] ?? '';
            
            return imagekit_url($path, $transformations);
        }

        // For legacy images, return as is
        return $this->thumb_image;
    }

    /**
     * Get thumbnail image for cards (NO compression)
     *
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->getOptimizedImageUrl([
            [
                'width' => 400,
                'height' => 300,
                'quality' => 100, // No compression
                'crop' => 'maintain_ratio'
            ]
        ]);
    }

    /**
     * Get hero image for detail page (NO compression)
     *
     * @return string
     */
    public function getHeroImageUrlAttribute(): string
    {
        return $this->getOptimizedImageUrl([
            [
                'width' => 1200,
                'height' => 600,
                'quality' => 100, // No compression
                'crop' => 'maintain_ratio'
            ]
        ]);
    }

    /**
     * Get original image URL without any transformations
     *
     * @return string
     */
    public function getOriginalImageUrlAttribute(): string
    {
        // Return the original URL without any transformations
        return $this->thumb_image ?? '';
    }
}
