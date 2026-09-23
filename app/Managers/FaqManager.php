<?php

namespace App\Managers;

use App\Models\Faq;
use App\Repositories\Contracts\FaqRepositoryInterface;

class FaqManager extends BaseContentManager
{
    public function __construct(FaqRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Faq::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}
