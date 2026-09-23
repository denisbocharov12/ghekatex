<?php

namespace App\Managers;

use App\Models\Redirect;
use App\Repositories\Contracts\RedirectRepositoryInterface;

class RedirectManager extends BaseContentManager
{
    public function __construct(RedirectRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Redirect::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}
