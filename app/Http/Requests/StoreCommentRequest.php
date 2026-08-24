<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:120'],
            'author_email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'min:5', 'max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'author_name' => 'họ tên',
            'author_email' => 'email',
            'body' => 'bình luận',
        ];
    }
}
