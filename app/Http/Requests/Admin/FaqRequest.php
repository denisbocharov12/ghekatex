<?php

namespace App\Http\Requests\Admin;

use App\Enums\FaqGroup;
use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'in:'.implode(',', FaqGroup::values())],
            'question' => ['array'],
            'question.ro' => ['required', 'string'],
            'question.en' => ['nullable', 'string'],
            'question.ru' => ['nullable', 'string'],
            'answer' => ['array'],
            'answer.ro' => ['required', 'string'],
            'answer.en' => ['nullable', 'string'],
            'answer.ru' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
