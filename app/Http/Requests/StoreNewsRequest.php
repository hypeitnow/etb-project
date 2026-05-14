<?php

namespace App\Http\Requests;

use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', News::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'publish_at' => ['nullable', 'date'],
            'is_visible' => ['nullable', 'boolean'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:15'],
            'gallery.*' => ['image', 'max:5120'],
        ];
    }
}
