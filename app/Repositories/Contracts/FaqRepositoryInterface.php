<?php

namespace App\Repositories\Contracts;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends CrudRepositoryInterface<Faq>
 */
interface FaqRepositoryInterface extends CrudRepositoryInterface
{
    /** @return Collection<int, Faq> Активные вопросы одной подборки. */
    public function activeInGroup(string $group, ?int $limit = null): Collection;
}
