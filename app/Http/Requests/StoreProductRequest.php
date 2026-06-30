<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesUploadedImages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    use ValidatesUploadedImages;

    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'gestionnaire') ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:'.config('shae.upload.max_kilobytes', 10240)],
            'status' => ['nullable', Rule::in(['draft', 'pending', 'approved', 'rejected'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du produit est obligatoire.',
            'price.min' => 'Le prix doit être positif.',
            'image.max' => 'L\'image ne doit pas dépasser 10 Mo.',
            'image.uploaded' => 'Le téléversement de l\'image a échoué. Vérifiez le format (JPG, PNG) et relancez lancer-shae.bat.',
        ];
    }
}
