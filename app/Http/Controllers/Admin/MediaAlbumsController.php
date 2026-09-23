<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\MediaAlbumRequest;
use App\Managers\MediaAlbumManager;
use App\Repositories\Contracts\MediaAlbumRepositoryInterface;

class MediaAlbumsController extends AdminResourceController
{
    public function __construct(MediaAlbumManager $manager, MediaAlbumRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'MediaAlbums';
    }

    protected function routePrefix(): string
    {
        return 'media-albums';
    }

    protected function requestClass(): string
    {
        return MediaAlbumRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['title', 'description'],
            plain: ['slug', 'sort_order', 'is_active'],
            casts: ['sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}
