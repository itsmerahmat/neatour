<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $table = 'testimonials';
    
    protected $fillable = [
        'destination_id',
        'name',
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