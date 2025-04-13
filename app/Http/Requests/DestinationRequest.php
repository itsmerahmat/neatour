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
        return true;
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
            'pic_id' => 'required|exists:users,id',
            'published' => 'boolean',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'thumb_image' => $this->isMethod('PUT') || $this->isMethod('PATCH') 
                ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}