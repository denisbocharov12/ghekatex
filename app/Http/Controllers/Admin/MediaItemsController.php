<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\MediaItemRequest;
use App\Managers\MediaItemManager;
use App\Repositories\Contracts\MediaAlbumRepositoryInterface;
use App\Repositories\Contracts\MediaItemRepositoryInterface;

class MediaItemsController extends AdminResourceController
{
    public function __construct(MediaItemManager $manager, MediaItemRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'MediaItems';
    }

    protected function routePrefix(): string
    {
        return 'media-items';
    }

    protected function requestClass(): string
    {
        return MediaItemRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['title', 'caption'],
            plain: ['album_id', 'type', 'video_provider', 'video_url', 'sort_order', 'is_active'],
            casts: ['album_id' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['file', 'poster'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'albums' => app(MediaAlbumRepositoryInterface::class)->all(),
            'providers' => ['youtube', 'vimeo', 'file'],
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}
