<x-mail::message>
# {{ __('Новая заявка с сайта') }}

**{{ __('Имя') }}:** {{ $contactRequest->name }}
**Email:** {{ $contactRequest->email }}
@if ($contactRequest->phone)
**{{ __('Телефон') }}:** {{ $contactRequest->phone }}
@endif
@if ($contactRequest->company)
**{{ __('Компания') }}:** {{ $contactRequest->company }}
@endif
@if ($contactRequest->country)
**{{ __('Страна') }}:** {{ $contactRequest->country }}
@endif
**{{ __('Источник') }}:** {{ $contactRequest->source->value }}
**{{ __('Язык') }}:** {{ $contactRequest->locale }}

---

{{ $contactRequest->message }}

<x-mail::button :url="url('/admin/requests/'.$contactRequest->id)">
{{ __('Открыть в панели') }}
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
