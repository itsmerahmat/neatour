<?php

namespace App\Http\Requests;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // If superadmin, always authorize
        if ($user && $user->role === 'superadmin') {
            return true;
        }
        
        // For POST requests to store testimonials (create new testimonial)
        if ($this->isMethod('POST') && $this->routeIs('testimonial.store')) {
            // Allow both authenticated and anonymous users to create testimonials
            $destinationId = $this->input('destination_id');
            if ($destinationId) {
                $destination = Destination::find($destinationId);
                return $destination !== null; // Allow if destination exists
            }
            return false;
        }
        
        // For other operations (index, show, edit, update, delete), require authentication
        if (!$user) {
            return false;
        }
        
        // For update/edit/delete actions, check if user owns the destination
        $testimonial = $this->route('testimonial');
        if ($testimonial) {
            $testimonial = is_object($testimonial) ? $testimonial : \App\Models\Testimonial::with('destination')->find($testimonial);
            if ($testimonial && $testimonial->destination && $testimonial->destination->pic_id === $user->id) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination_id' => 'required|exists:destinations,id',
            'name' => 'required|string|max:255',
            'comment' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
        ];
    }
}