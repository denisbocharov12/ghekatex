<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Support\Presenters\SitePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly ProductCategoryRepositoryInterface $categories,
    ) {}

    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => SitePresenter::collect(
                $this->categories->activeWithCounts(),
                fn ($item) => SitePresenter::productCategory($item, withCount: true),
            ),
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $paginator = $this->products->catalog([
            'category_id' => $request->integer('category_id') ?: null,
            'fabric' => $request->string('fabric')->toString() ?: null,
            'treatment' => $request->string('treatment')->toString() ?: null,
            'search' => $request->string('q')->toString() ?: null,
        ], min((int) $request->integer('per_page', 24) ?: 24, 100));

        return response()->json([
            'data' => SitePresenter::collect($paginator->items(), fn ($item) => SitePresenter::product($item)),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function product(string $slug): JsonResponse
    {
        $product = $this->products->findActiveBySlugOrFail($slug);
        $product->load(['category', 'fabrics.media', 'treatments', 'media']);

        return response()->json(['data' => SitePresenter::product($product, full: true)]);
    }
}
