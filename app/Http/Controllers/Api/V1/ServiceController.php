<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Support\Presenters\SitePresenter;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceRepositoryInterface $services,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => SitePresenter::collect($this->services->activeOrdered(), fn ($item) => SitePresenter::service($item)),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $service = $this->services->findActiveBySlugOrFail($slug);
        $service->load('media');

        return response()->json(['data' => SitePresenter::service($service, full: true)]);
    }
}
