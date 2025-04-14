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
        'published'
    ];

    protected $casts = [
        'published' => 'boolean',
        'lat' => 'float',
        'lon' => 'float',
    ];

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
}
