<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): string
    {
        return User::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['name', 'email', 'position'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['name', 'created_at', 'last_login_at'];
    }

    protected function defaultSort(): string
    {
        return 'name';
    }

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['roles'];
    }
}
