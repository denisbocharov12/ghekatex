<?php

namespace App\Managers;

use App\Data\ContactRequestData;
use App\Enums\ContactRequestStatus;
use App\Mail\ContactRequestReceived;
use App\Models\ContactRequest;
use App\Repositories\Contracts\ContactRequestRepositoryInterface;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Приём и обработка заявок. Письмо уходит после коммита: если почта
 * недоступна, заявка всё равно сохранена и видна в админке.
 */
class ContactRequestManager
{
    public function __construct(
        private readonly ContactRequestRepositoryInterface $requests,
        private readonly SettingService $settings,
    ) {}

    public function submit(ContactRequestData $data, Request $request): ContactRequest
    {
        $model = DB::transaction(fn () => $this->requests->create(array_merge($data->toModelAttributes(), [
            // IP храним только в виде хеша: он нужен для защиты от спама, не для профилирования
            'ip_hash' => hash('sha256', (string) $request->ip().config('app.key')),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'status' => ContactRequestStatus::New->value,
        ])));

        $this->notify($model);

        return $model;
    }

    public function updateStatus(ContactRequest $request, ContactRequestStatus $status, ?string $note, ?int $userId): ContactRequest
    {
        return $this->requests->update($request, [
            'status' => $status->value,
            'admin_note' => $note,
            'handled_by' => $userId,
            'handled_at' => now(),
        ]);
    }

    public function delete(ContactRequest $request): void
    {
        $this->requests->delete($request);
    }

    private function notify(ContactRequest $model): void
    {
        $to = $this->settings->get('contact_email') ?: config('ghekatex.forms.notify_to');

        if ($to === null || $to === '') {
            return;
        }

        try {
            Mail::to($to)->send(new ContactRequestReceived($model));
        } catch (\Throwable $exception) {
            // Заявка уже в базе — уведомление не должно ронять ответ посетителю
            Log::warning('Не удалось отправить уведомление о заявке', [
                'request_id' => $model->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
