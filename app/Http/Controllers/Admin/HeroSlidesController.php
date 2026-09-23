<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\HeroSlideRequest;
use App\Managers\HeroSlideManager;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;

class HeroSlidesController extends AdminResourceController
{
    public function __construct(HeroSlideManager $manager, HeroSlideRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'HeroSlides';
    }

    protected function routePrefix(): string
    {
        return 'hero-slides';
    }

    protected function requestClass(): string
    {
        return HeroSlideRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['eyebrow', 'title', 'description', 'cta_label', 'secondary_cta_label'],
            plain: ['cta_url', 'secondary_cta_url', 'overlay_opacity', 'sort_order', 'is_active'],
            casts: ['overlay_opacity' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['image', 'video'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}
