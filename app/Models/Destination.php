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
}
