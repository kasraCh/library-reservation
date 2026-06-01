<?php

namespace App\Http\Requests\Books;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'unique:books,title'],
            'author' => ['required', 'string', 'min:3'],
            'isbn' => ['required', 'string', 'unique:books,isbn'],
            'total_copies' => ['string', 'min:1', 'nullable'],
            'available_copies' => ['string', 'min:1', 'nullable'],
        ];
    }
}
