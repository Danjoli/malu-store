<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:80'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Escolha uma nota de 1 a 5 estrelas.',
            'rating.between' => 'A nota deve estar entre 1 e 5 estrelas.',
            'comment.required' => 'Conte como foi sua experiência com a peça.',
            'comment.min' => 'Escreva pelo menos 10 caracteres sobre o produto.',
            'comment.max' => 'A avaliação pode ter no máximo 1000 caracteres.',
        ];
    }
}
