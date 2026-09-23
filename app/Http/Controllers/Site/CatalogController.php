<?php

namespace App\Http\Controllers\Site;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\Contracts\FabricRepositoryInterface;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\TreatmentRepositoryInterface;
use App\Services\SeoService;
use App\Support\Presenters\SitePresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\SchemaOrg\Schema;

class CatalogController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly ProductRepositoryInterface $products,
        private readonly ProductCategoryRepositoryInterface $categories,
        private readonly FabricRepositoryInterface $fabrics,
        private readonly TreatmentRepositoryInterface $treatments,
    ) {
        parent::__construct($seo);
    }

    public function index(Request $request, string $locale): Response
    {
        return $this->renderList($request, null);
    }

    public function category(Request $request, string $locale, ProductCategory $category): Response
    {
        abort_unless($category->is_active, 404);

        return $this->renderList($request, $category);
    }

    public function show(string $locale, Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'fabrics.media', 'treatments', 'media', 'seo']);

        $name = (string) $product->getTranslation('name', $locale, true);

        $crumbs = $this->crumbs(array_values(array_filter([
            ['label' => __('Каталог продукции'), 'url' => route('catalog.index', ['locale' => $locale])],
            $product->category === null ? null : [
                'label' => $product->category->getTranslation('name', $locale, true),
                'url' => route('catalog.category', ['locale' => $locale, 'category' => $product->category->slug]),
            ],
            ['label' => $name],
        ])));

        $presented = SitePresenter::product($product, full: true);

        return Inertia::render('Catalog/Show', [
            'product' => $presented,
            'related' => SitePresenter::collect($this->products->related($product), fn ($item) => SitePresenter::product($item)),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forModel(
                model: $product,
                fallbackTitle: $name,
                fallbackDescription: $presented['summary'],
                fallbackImage: $presented['cover'],
                breadcrumbs: $crumbs,
                schema: [
                    Schema::product()
                        ->name($name)
                        ->description($presented['summary'])
                        ->sku($product->article)
                        ->image(array_values(array_filter([$presented['cover']])))
                        ->toArray(),
                ],
            )->toArray(),
        ]);
    }

    private function renderList(Request $request, ?ProductCategory $category): Response
    {
        $locale = app()->getLocale();

        $filters = [
            'category_id' => $category?->getKey(),
            'fabric' => $request->string('fabric')->toString() ?: null,
            'treatment' => $request->string('treatment')->toString() ?: null,
            'search' => $request->string('q')->toString() ?: null,
        ];

        $paginator = $this->products->catalog($filters);

        /*
        | Срезы по фильтрам и страницы со второй — тот же товарный состав в
        | другом порядке. Каноникал у них и так чистый, но из индекса их лучше
        | убрать, оставив обход ссылок: иначе поисковик разбавляет каталог
        | десятками почти одинаковых адресов.
        */
        $isSlice = $filters['fabric'] !== null
            || $filters['treatment'] !== null
            || $filters['search'] !== null
            || $paginator->currentPage() > 1;

        $robots = $isSlice ? 'noindex,follow' : 'index,follow';

        $title = $category !== null
            ? (string) $category->getTranslation('name', $locale, true)
            : __('Каталог продукции');

        $crumbs = $this->crumbs(array_values(array_filter([
            $category === null
                ? ['label' => $title]
                : ['label' => __('Каталог продукции'), 'url' => route('catalog.index', ['locale' => $locale])],
            $category === null ? null : ['label' => $title],
        ])));

        return Inertia::render('Catalog/Index', [
            'category' => $category === null ? null : SitePresenter::productCategory($category),
            'categories' => SitePresenter::collect(
                $this->categories->activeWithCounts(),
                fn ($item) => SitePresenter::productCategory($item, withCount: true),
            ),
            'fabrics' => $this->fabrics->activeOrdered()->map(fn ($item) => [
                'slug' => $item->slug,
                'name' => $item->getTranslation('name', $locale, true),
                'color' => $item->color_hex,
            ])->all(),
            'treatments' => $this->treatments->activeOrdered()->map(fn ($item) => [
                'slug' => $item->slug,
                'name' => $item->getTranslation('name', $locale, true),
                'icon' => $item->icon,
            ])->all(),
            'products' => [
                'data' => SitePresenter::collect($paginator->items(), fn ($item) => SitePresenter::product($item)),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                ],
                'links' => [
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ],
            'filters' => $filters,
            'breadcrumbs' => $crumbs,
            'seo' => ($category !== null
                ? $this->seo->forModel($category, $title, breadcrumbs: $crumbs, robots: $robots)
                : $this->seo->forPage($title, breadcrumbs: $crumbs, robots: $robots)
            )->toArray(),
        ]);
    }
}
