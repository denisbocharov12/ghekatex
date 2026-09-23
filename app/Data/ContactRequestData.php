<?php

namespace App\Data;

use App\Enums\ContactRequestSource;
use Spatie\LaravelData\Data;

class ContactRequestData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $company,
        public readonly ?string $country,
        public readonly ?string $subject,
        public readonly string $message,
        public readonly ContactRequestSource $source,
        public readonly ?string $relatedType,
        public readonly ?int $relatedId,
        public readonly string $locale,
    ) {}

    /** @return array<string, mixed> */
    public function toModelAttributes(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'country' => $this->country,
            'subject' => $this->subject,
            'message' => $this->message,
            'source' => $this->source->value,
            'related_type' => $this->relatedType,
            'related_id' => $this->relatedId,
            'locale' => $this->locale,
        ];
    }
}
