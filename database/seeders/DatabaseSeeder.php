<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Порядок важен: права нужны пользователям, пользователи — авторам новостей
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            SettingSeeder::class,
            PageSeeder::class,
            NavigationSeeder::class,
            HomeContentSeeder::class,
            CompanySeeder::class,
            CatalogSeeder::class,
            ServiceSeeder::class,
            FaqSeeder::class,
            NewsSeeder::class,
            MediaSeeder::class,
        ]);
    }
}
