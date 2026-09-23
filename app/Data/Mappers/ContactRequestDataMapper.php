<?php

namespace App\Data\Mappers;

use App\Data\ContactRequestData;
use App\Enums\ContactRequestSource;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

final class ContactRequestDataMapper
{
    /** Тип связанной сущности приходит коротким словом, а не именем класса. */
    private const RELATED = [
        'product' => Product::class,
        'service' => Service::class,
    ];

    public static function fromRequest(FormRequest $request): ContactRequestData
    {
        $source = ContactRequestSource::tryFrom((string) $request->input('source'))
            ?? ContactRequestSource::Contacts;

        $relatedType = self::RELATED[(string) $request->input('related_type')] ?? null;
        $relatedId = $request->integer('related_id') ?: null;

        return new ContactRequestData(
            name: trim((string) $request->input('name')),
            email: mb_strtolower(trim((string) $request->input('email'))),
            phone: TranslationNormalizer::nullableString($request->input('phone')),
            company: TranslationNormalizer::nullableString($request->input('company')),
            country: TranslationNormalizer::nullableString($request->input('country')),
            subject: TranslationNormalizer::nullableString($request->input('subject')),
            message: trim((string) $request->input('message')),
            source: $source,
            relatedType: $relatedType,
            relatedId: $relatedType === null ? null : $relatedId,
            locale: app()->getLocale(),
        );
    }
}
