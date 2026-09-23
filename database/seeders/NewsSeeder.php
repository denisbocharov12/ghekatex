<?php

namespace Database\Seeders;

use App\Enums\MediaItemType;
use App\Enums\PostType;
use App\Models\MediaAlbum;
use App\Models\MediaItem;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        $categories = $this->categories();
        $this->posts($categories);
        $this->albums();
    }

    /** @return array<string, int> */
    private function categories(): array
    {
        $items = [
            ['company', $this->t('Viața companiei', 'Company life', 'Жизнь компании')],
            ['production', $this->t('Producție', 'Production', 'Производство')],
            ['industry', $this->t('Industria modei', 'Fashion industry', 'Индустрия моды')],
            ['events', $this->t('Expoziții și evenimente', 'Trade shows and events', 'Выставки и события')],
        ];

        $map = [];

        foreach ($items as $index => [$slug, $name]) {
            $category = PostCategory::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'sort_order' => $index, 'is_active' => true],
            );

            $map[$slug] = $category->id;
        }

        return $map;
    }

    /** @param  array<string, int>  $categories */
    private function posts(array $categories): void
    {
        $authorId = User::query()->where('email', 'editor@ghekatex.md')->value('id');

        $items = [
            [
                'slug' => 'new-cutting-line-2026',
                'category' => 'production',
                'type' => PostType::News,
                'days' => 4,
                'featured' => true,
                'title' => $this->t(
                    'Am pus în funcțiune a doua masă automată de croire',
                    'We have commissioned a second automated cutting table',
                    'Запустили второй автоматический раскройный стол',
                ),
                'excerpt' => $this->t(
                    'Capacitatea secției de croire crește cu 40%, iar consumul de material scade.',
                    'Cutting capacity grows by 40% while fabric consumption drops.',
                    'Мощность раскройного участка выросла на 40%, расход материала снизился.',
                ),
                'body' => $this->t(
                    '<p>Noua masă permite croirea stivelor de până la 7 cm și reduce timpul de pregătire a lotului. Software-ul de optimizare a așezării tiparelor economisește în medie 4% din material la fiecare comandă.</p><p>Pentru clienți asta înseamnă termene mai scurte și un cost per articol mai previzibil.</p>',
                    '<p>The new table cuts stacks up to 7 cm high and shortens batch preparation time. The marker-optimisation software saves an average of 4% of fabric on every order.</p><p>For customers this means shorter lead times and a more predictable cost per garment.</p>',
                    '<p>Новый стол режет настил высотой до 7 см и сокращает время подготовки партии. Программа оптимизации раскладки экономит в среднем 4% материала на каждом заказе.</p><p>Для заказчиков это означает более короткие сроки и предсказуемую себестоимость изделия.</p>',
                ),
            ],
            [
                'slug' => 'milano-unica-2026',
                'category' => 'events',
                'type' => PostType::News,
                'days' => 18,
                'featured' => true,
                'title' => $this->t(
                    'GHEKATEX la Milano Unica 2026',
                    'GHEKATEX at Milano Unica 2026',
                    'GHEKATEX на Milano Unica 2026',
                ),
                'excerpt' => $this->t(
                    'Prezentăm capacitățile de producție și noile mostre de colecție.',
                    'We present our manufacturing capabilities and new collection samples.',
                    'Показываем производственные возможности и новые образцы коллекции.',
                ),
                'body' => $this->t(
                    '<p>Echipa noastră va fi prezentă la standul dedicat producătorilor din Europa de Est. Puteți programa o întâlnire pentru a discuta capacitățile, termenele și loturile minime.</p>',
                    '<p>Our team will be at the stand dedicated to Eastern European manufacturers. You can book a meeting to discuss capacity, lead times and minimum batches.</p>',
                    '<p>Наша команда будет на стенде производителей Восточной Европы. Встречу можно назначить заранее — обсудим мощности, сроки и минимальные партии.</p>',
                ),
            ],
            [
                'slug' => 'nearshoring-europe',
                'category' => 'industry',
                'type' => PostType::Article,
                'days' => 35,
                'featured' => false,
                'title' => $this->t(
                    'De ce brandurile europene revin la nearshoring',
                    'Why European brands are returning to nearshoring',
                    'Почему европейские бренды возвращаются к nearshoring',
                ),
                'excerpt' => $this->t(
                    'Termene scurte, loturi mici și control direct asupra calității.',
                    'Short lead times, small batches and direct control over quality.',
                    'Короткие сроки, малые партии и прямой контроль качества.',
                ),
                'body' => $this->t(
                    '<p>Costul transportului și imprevizibilitatea termenelor din Asia au schimbat calculul. Producția la 2 000 km de raft permite reacții rapide la cerere și loturi de reaprovizionare de câteva sute de bucăți.</p><p>Moldova oferă acces la forță de muncă calificată, tarife competitive și livrare rutieră în UE în câteva zile.</p>',
                    '<p>Shipping costs and unpredictable Asian lead times have changed the maths. Producing 2,000 km from the shelf allows fast reaction to demand and replenishment batches of a few hundred pieces.</p><p>Moldova offers skilled labour, competitive rates and road delivery into the EU within days.</p>',
                    '<p>Стоимость перевозки и непредсказуемость сроков из Азии изменили расчёт. Производство в 2 000 км от полки позволяет быстро реагировать на спрос и допоставлять партии в несколько сотен единиц.</p><p>Молдова даёт доступ к квалифицированным кадрам, конкурентные ставки и автодоставку в ЕС за несколько дней.</p>',
                ),
            ],
            [
                'slug' => 'quality-lab-report',
                'category' => 'production',
                'type' => PostType::Review,
                'days' => 52,
                'featured' => false,
                'title' => $this->t(
                    'Cum testăm materialele înainte de lansarea lotului',
                    'How we test fabric before a batch goes live',
                    'Как мы тестируем материалы до запуска партии',
                ),
                'excerpt' => $this->t(
                    'Contracție, frecare, stabilitatea culorii — trei teste care salvează comanda.',
                    'Shrinkage, rubbing, colour fastness — three tests that save an order.',
                    'Усадка, истирание, стойкость цвета — три теста, которые спасают заказ.',
                ),
                'body' => $this->t(
                    '<p>Fiecare rulou intrat în fabrică trece prin laborator. Testăm contracția după spălare, rezistența la frecare și stabilitatea culorii la transpirație și lumină.</p><p>Dacă materialul nu trece testul, informăm brandul înainte de croire — nu după ce lotul este deja cusut.</p>',
                    '<p>Every roll entering the factory goes through the laboratory. We test shrinkage after washing, rubbing resistance and colour fastness to perspiration and light.</p><p>If the fabric fails, we tell the brand before cutting — not after the batch has already been sewn.</p>',
                    '<p>Каждый поступивший рулон проходит лабораторию. Проверяем усадку после стирки, устойчивость к истиранию и стойкость цвета к поту и свету.</p><p>Если материал не проходит тест, сообщаем бренду до раскроя, а не после того, как партия уже отшита.</p>',
                ),
            ],
            [
                'slug' => 'sustainable-packaging',
                'category' => 'company',
                'type' => PostType::News,
                'days' => 70,
                'featured' => false,
                'title' => $this->t(
                    'Trecem la ambalaje reciclabile',
                    'Switching to recyclable packaging',
                    'Переходим на перерабатываемую упаковку',
                ),
                'excerpt' => $this->t(
                    'Renunțăm la polietilena de unică folosință pentru livrările în UE.',
                    'We are phasing out single-use polythene for EU shipments.',
                    'Отказываемся от одноразового полиэтилена для поставок в ЕС.',
                ),
                'body' => $this->t(
                    '<p>Începând cu acest sezon, articolele sunt ambalate în pungi din material reciclat, iar cutiile sunt certificate FSC. Schimbarea a fost cerută de doi dintre partenerii noștri și a devenit standard pentru toate comenzile.</p>',
                    '<p>From this season garments are packed in recycled-material bags and boxes are FSC certified. The change was requested by two of our partners and has become the standard for all orders.</p>',
                    '<p>С этого сезона изделия упаковываются в пакеты из переработанного материала, коробки сертифицированы FSC. Изменение запросили двое наших партнёров, и оно стало стандартом для всех заказов.</p>',
                ),
            ],
        ];

        foreach ($items as $item) {
            Post::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categories[$item['category']] ?? null,
                    'author_id' => $authorId,
                    'type' => $item['type']->value,
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'body' => $item['body'],
                    'published_at' => now()->subDays($item['days']),
                    'is_featured' => $item['featured'],
                    'reading_minutes' => 3,
                    'is_active' => true,
                ],
            );
        }
    }

    private function albums(): void
    {
        $albums = [
            [
                'slug' => 'production-floor',
                'title' => $this->t('Fluxul de producție', 'The production floor', 'Производственный поток'),
                'description' => $this->t(
                    'Croire, cusut și control — o zi obișnuită la fabrică.',
                    'Cutting, sewing and inspection — an ordinary day at the factory.',
                    'Раскрой, пошив и контроль — обычный день на фабрике.',
                ),
                'items' => [
                    $this->t('Secția de croire', 'Cutting department', 'Раскройный участок'),
                    $this->t('Linia de cusut', 'Sewing line', 'Швейная линия'),
                    $this->t('Control final', 'Final inspection', 'Финальный контроль'),
                    $this->t('Ambalare', 'Packing', 'Упаковка'),
                ],
            ],
            [
                'slug' => 'lookbook',
                'title' => $this->t('Lookbook de producție', 'Production lookbook', 'Лукбук производства'),
                'description' => $this->t(
                    'Articole cusute pentru partenerii noștri europeni.',
                    'Garments made for our European partners.',
                    'Изделия, отшитые для наших европейских партнёров.',
                ),
                'items' => [
                    $this->t('Rochii', 'Dresses', 'Платья'),
                    $this->t('Bluze', 'Blouses', 'Блузы'),
                    $this->t('Sacouri', 'Blazers', 'Жакеты'),
                ],
            ],
        ];

        foreach ($albums as $index => $album) {
            $model = MediaAlbum::query()->updateOrCreate(
                ['slug' => $album['slug']],
                [
                    'title' => $album['title'],
                    'description' => $album['description'],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );

            foreach ($album['items'] as $position => $title) {
                MediaItem::query()->updateOrCreate(
                    ['album_id' => $model->id, 'sort_order' => $position],
                    [
                        'type' => MediaItemType::Image->value,
                        'title' => $title,
                        'sort_order' => $position,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
