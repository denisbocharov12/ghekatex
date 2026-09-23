<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Support\Presenters\SitePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->posts->feed([
            'type' => $request->string('type')->toString() ?: null,
            'category_id' => $request->integer('category_id') ?: null,
            'search' => $request->string('q')->toString() ?: null,
        ], min((int) $request->integer('per_page', 12) ?: 12, 50));

        return response()->json([
            'data' => SitePresenter::collect($paginator->items(), fn ($item) => SitePresenter::post($item)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = $this->posts->findActiveBySlugOrFail($slug);
        $post->load(['category', 'author', 'media']);

        return response()->json(['data' => SitePresenter::post($post, full: true)]);
    }
}
