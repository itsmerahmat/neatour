<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationTestimonial extends Model
{
    protected $table = 'testimonials';
    
    protected $fillable = [
        'destination_id',
        'comment',
        'rating'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}