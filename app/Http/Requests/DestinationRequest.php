<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestinationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        
        // Superadmin can do anything
        if ($user && $user->role === 'superadmin') {
            return true;
        }
        
        // For update/edit actions, check if the user owns the destination
        $destination = $this->route('destination');
        if ($destination && $user && $destination->pic_id === $user->id) {
            return true;
        }
        
        // For create actions, set the pic_id to the current user if not superadmin
        if ($this->isMethod('POST') && $user) {
            $this->merge(['pic_id' => $user->id]);
            return true;
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
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'facility' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'address' => 'nullable|string',
            'operating_hours' => 'nullable|string',
            'pic_id' => 'required|exists:users,id',
            'published' => 'boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'thumb_image' => $this->isMethod('PUT') || $this->isMethod('PATCH') 
                ? 'nullable|image|max:2048'
                : 'required|image|max:2048',
        ];
    }
}