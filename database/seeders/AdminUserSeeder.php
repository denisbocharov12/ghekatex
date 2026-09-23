<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@ghekatex.md'],
            [
                'name' => 'GHEKATEX Admin',
                'password' => 'password',
                'position' => 'Администратор сайта',
                'locale' => 'ru',
                'is_active' => true,
            ],
        );

        $admin->syncRoles([RoleName::SuperAdmin->value]);

        $editor = User::query()->updateOrCreate(
            ['email' => 'editor@ghekatex.md'],
            [
                'name' => 'Content Editor',
                'password' => 'password',
                'position' => 'Контент-менеджер',
                'locale' => 'ru',
                'is_active' => true,
            ],
        );

        $editor->syncRoles([RoleName::ContentManager->value]);
    }
}
