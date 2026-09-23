<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Fabric;
use App\Models\Facility;
use App\Models\HeroSlide;
use App\Models\MediaItem;
use App\Models\Office;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

/**
 * Демонстрационные фото и видео.
 *
 * Файлы лежат в `database/seed-media` и не входят в репозиторий: их
 * скачивает `node database/seed-media/download.mjs`. Если каталог пуст,
 * сидер просто пропускается — сборка проекта от этого не ломается.
 */
class MediaSeeder extends Seeder
{
    private string $root;

    public function __construct()
    {
        $this->root = database_path('seed-media');
    }

    public function run(): void
    {
        // В тестах база пересоздаётся на каждый метод: копировать полсотни
        // файлов ради смоук-проверок — минуты впустую
        if (app()->environment('testing')) {
            return;
        }

        if (! is_dir($this->root)) {
            $this->command?->warn('Демо-медиа не найдено — пропускаем.');

            return;
        }

        $this->attachBySlug(Product::class, 'products', 'cover');
        $this->attachBySlug(ProductCategory::class, 'categories', 'cover');
        $this->attachBySlug(Service::class, 'services', 'cover');
        $this->attachBySlug(Facility::class, 'facilities', 'cover');
        $this->attachBySlug(Post::class, 'posts', 'cover');
        $this->attachBySlug(Fabric::class, 'fabrics', 'swatch');

        $this->productGalleries();
        $this->galleryItems();
        $this->offices();
        $this->slides();
        $this->aboutPage();
        $this->certificates();
        $this->partnerLogos();
    }

    /**
     * Словесные знаки партнёров.
     *
     * Логотипы нарисованы под проект и лежат в `public/brand/partners`,
     * а не скачиваются вместе с фотографиями: это часть оформления сайта.
     */
    private function partnerLogos(): void
    {
        foreach (Partner::query()->get() as $partner) {
            if ($partner->getMedia('logo')->isNotEmpty()) {
                continue;
            }

            $file = public_path('brand/partners/'.Str::slug($partner->name).'.svg');

            if (! is_file($file)) {
                continue;
            }

            $partner->addMedia($file)->preservingOriginal()->toMediaCollection('logo');
        }
    }

    /**
     * Прикладывает файл `<группа>/<slug>.jpg` к одноимённой записи.
     *
     * @param  class-string<Model>  $model
     */
    private function attachBySlug(string $model, string $group, string $collection): void
    {
        foreach ($model::query()->get() as $record) {
            $this->attach($record, $collection, "{$group}/{$record->slug}.jpg");
        }
    }

    private function productGalleries(): void
    {
        // Каждому изделию — пара кадров из лукбука, чтобы галерея не пустовала
        $extra = ['gallery/lookbook-1.jpg', 'gallery/lookbook-2.jpg', 'gallery/lookbook-3.jpg'];

        foreach (Product::query()->get() as $index => $product) {
            if ($product->getMedia('gallery')->isNotEmpty()) {
                continue;
            }

            $this->attach($product, 'gallery', $extra[$index % count($extra)]);
            $this->attach($product, 'gallery', $extra[($index + 1) % count($extra)]);
        }
    }

    private function galleryItems(): void
    {
        $byAlbum = [
            'production-floor' => ['gallery/production-1.jpg', 'gallery/production-2.jpg', 'gallery/production-3.jpg', 'gallery/production-4.jpg'],
            'lookbook' => ['gallery/lookbook-1.jpg', 'gallery/lookbook-2.jpg', 'gallery/lookbook-3.jpg'],
        ];

        foreach ($byAlbum as $albumSlug => $files) {
            $items = MediaItem::query()
                ->whereHas('album', fn ($query) => $query->where('slug', $albumSlug))
                ->orderBy('sort_order')
                ->get();

            foreach ($items as $index => $item) {
                $this->attach($item, 'file', $files[$index % count($files)]);
            }
        }
    }

    private function offices(): void
    {
        foreach (Office::query()->get() as $office) {
            $this->attach($office, 'photo', 'offices/'.$office->type->value.'.jpg');
        }
    }

    private function slides(): void
    {
        $slides = HeroSlide::query()->ordered()->get();

        // Первый слайд — видеообложка, остальные держатся на фотографии
        $plan = [
            ['video' => 'media/video/hero_home.mp4', 'image' => 'slides/slide-atelier.jpg'],
            ['video' => 'media/video/production_video.mp4', 'image' => 'slides/slide-atelier.jpg'],
            ['video' => null, 'image' => 'slides/slide-rack.jpg'],
        ];

        foreach ($slides as $index => $slide) {
            $config = $plan[$index] ?? $plan[count($plan) - 1];

            $this->attach($slide, 'image', $config['image']);

            if ($config['video'] !== null) {
                $this->attachPublic($slide, 'video', $config['video']);
            }
        }
    }

    private function aboutPage(): void
    {
        $page = Page::query()->where('slug', 'about')->first();

        if ($page !== null) {
            $this->attach($page, 'cover', 'pages/about.jpg');
        }

        $setting = Setting::query()->where('key', 'seo_default_og_image')->first();

        if ($setting !== null && $setting->getMedia('file')->isEmpty()) {
            $this->attach($setting, 'file', 'pages/og-default.jpg');
            $setting->update(['value' => ['value' => $setting->getFirstMediaUrl('file')]]);
        }
    }

    private function certificates(): void
    {
        $files = ['gallery/production-2.jpg', 'gallery/production-4.jpg', 'fabrics/linen.jpg', 'fabrics/silk.jpg'];

        foreach (Certificate::query()->ordered()->get() as $index => $certificate) {
            $this->attach($certificate, 'image', $files[$index % count($files)]);
        }
    }

    private function attach(Model $record, string $collection, string $relative): void
    {
        if (! $record instanceof HasMedia) {
            return;
        }

        $path = $this->root.'/'.$relative;

        if (! is_file($path)) {
            return;
        }

        // Одиночные коллекции не дублируем, галереи наполняем один раз
        if ($record->getMedia($collection)->isNotEmpty() && $collection !== 'gallery') {
            return;
        }

        $record->addMedia($path)->preservingOriginal()->toMediaCollection($collection);
    }

    /** Видео берём из `public/media` — оно отдаётся напрямую и не дублируется в хранилище. */
    private function attachPublic(Model $record, string $collection, string $relative): void
    {
        if (! $record instanceof HasMedia) {
            return;
        }

        $path = public_path($relative);

        if (! is_file($path) || $record->getMedia($collection)->isNotEmpty()) {
            return;
        }

        $record->addMedia($path)->preservingOriginal()->toMediaCollection($collection);
    }
}
