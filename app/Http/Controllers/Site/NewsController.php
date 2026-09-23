<?php

namespace App\Http\Controllers\Site;

use App\Models\Post;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Services\SeoService;
use App\Support\Presenters\SitePresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\SchemaOrg\Schema;

class NewsController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly PostRepositoryInterface $posts,
        private readonly PostCategoryRepositoryInterface $categories,
    ) {
        parent::__construct($seo);
    }

    public function index(Request $request, string $locale): Response
    {
        $category = $request->string('category')->toString() ?: null;

        $categories = $this->categories->activeOrdered();
        $categoryId = $category === null
            ? null
            : $categories->firstWhere('slug', $category)?->getKey();

        $filters = [
            'type' => $request->string('type')->toString() ?: null,
            'category_id' => $categoryId,
            'search' => $request->string('q')->toString() ?: null,
        ];

        $paginator = $this->posts->feed($filters);
        $title = __('Новости и медиа');
        $crumbs = $this->crumbs([['label' => $title]]);

        return Inertia::render('News/Index', [
            'posts' => [
                'data' => SitePresenter::collect($paginator->items(), fn ($item) => SitePresenter::post($item)),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
                'links' => [
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ],
            'categories' => $categories->map(fn ($item) => [
                'slug' => $item->slug,
                'name' => $item->getTranslation('name', $locale, true),
            ])->all(),
            'filters' => array_merge($filters, ['category' => $category]),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forPage($title, breadcrumbs: $crumbs)->toArray(),
        ]);
    }

    public function show(string $locale, Post $post): Response
    {
        abort_unless($post->is_active && $post->published_at !== null && $post->published_at->isPast(), 404);

        $post->load(['category', 'author', 'media', 'seo']);
        $this->posts->incrementViews($post);

        $title = (string) $post->getTranslation('title', $locale, true);
        $presented = SitePresenter::post($post, full: true);

        $crumbs = $this->crumbs([
            ['label' => __('Новости и медиа'), 'url' => route('news.index', ['locale' => $locale])],
            ['label' => $title],
        ]);

        return Inertia::render('News/Show', [
            'post' => $presented,
            'related' => SitePresenter::collect($this->posts->latestExcept($post), fn ($item) => SitePresenter::post($item)),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forModel(
                model: $post,
                fallbackTitle: $title,
                fallbackDescription: $presented['excerpt'],
                fallbackImage: $presented['wide'] ?? $presented['cover'],
                breadcrumbs: $crumbs,
                schema: [
                    Schema::newsArticle()
                        ->headline($title)
                        ->description($presented['excerpt'])
                        ->datePublished($post->published_at)
                        ->dateModified($post->updated_at)
                        ->image(array_values(array_filter([$presented['wide'] ?? $presented['cover']])))
                        ->toArray(),
                ],
            )->toArray(),
        ]);
    }
}
