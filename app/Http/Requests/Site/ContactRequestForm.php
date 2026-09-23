<?php

namespace App\Http\Requests\Site;

use App\Enums\ContactRequestSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ContactRequestForm extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:80'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'source' => ['nullable', 'string', 'in:'.implode(',', ContactRequestSource::values())],
            'related_type' => ['nullable', 'string', 'in:product,service'],
            'related_id' => ['nullable', 'integer'],
            'consent' => ['accepted'],
            // Ловушка для ботов и метка времени открытия формы
            config('ghekatex.forms.honeypot') => ['nullable', 'prohibited'],
            'form_started_at' => ['nullable', 'numeric'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'consent.accepted' => __('Подтвердите согласие на обработку персональных данных.'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $startedAt = (float) $this->input('form_started_at', 0);
            $minSeconds = (int) config('ghekatex.forms.min_seconds');

            // Человек не успевает заполнить форму за пару секунд — это бот
            if ($startedAt > 0 && (microtime(true) - $startedAt / 1000) < $minSeconds) {
                $validator->errors()->add('message', __('Форма отправлена слишком быстро. Попробуйте ещё раз.'));
            }
        });
    }
}
