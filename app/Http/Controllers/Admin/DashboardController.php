<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\ContactRequestRepositoryInterface;
use App\Repositories\Contracts\MediaItemRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly ServiceRepositoryInterface $services,
        private readonly PostRepositoryInterface $posts,
        private readonly MediaItemRepositoryInterface $media,
        private readonly CertificateRepositoryInterface $certificates,
        private readonly ContactRequestRepositoryInterface $requests,
    ) {}

    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'counters' => [
                'products' => $this->products->count(),
                'services' => $this->services->count(),
                'posts' => $this->posts->count(),
                'media' => $this->media->count(),
                'certificates' => $this->certificates->count(),
                'requests_new' => ContactRequest::query()->where('status', ContactRequestStatus::New)->count(),
            ],
            'latestRequests' => ContactRequest::query()
                ->latest()
                ->limit(8)
                ->get(['id', 'name', 'email', 'company', 'source', 'status', 'created_at']),
            'requestsChart' => $this->requestsPerDay(),
        ]);
    }

    /**
     * Заявки за последние 30 дней — по дню на точку, включая пустые.
     *
     * @return array<int, array{date: string, count: int}>
     */
    private function requestsPerDay(): array
    {
        $from = now()->subDays(29)->startOfDay();

        $counts = ContactRequest::query()
            ->where('created_at', '>=', $from)
            ->get(['created_at'])
            ->groupBy(fn (ContactRequest $item) => $item->created_at->toDateString())
            ->map->count();

        $result = [];

        for ($day = 0; $day < 30; $day++) {
            $date = $from->copy()->addDays($day)->toDateString();
            $result[] = ['date' => $date, 'count' => (int) ($counts[$date] ?? 0)];
        }

        return $result;
    }
}
