<?php

namespace Database\Seeders;

use App\Models\Fabric;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Treatment;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        $categories = $this->categories();
        $fabrics = $this->fabrics();
        $treatments = $this->treatments();

        $this->products($categories, $fabrics, $treatments);
    }

    /** @return array<string, int> */
    private function categories(): array
    {
        $items = [
            ['dresses', $this->t('Rochii', 'Dresses', 'Платья'), $this->t(
                'Rochii de zi, de seară și de birou în serii mici și medii.',
                'Day, evening and office dresses in small and medium runs.',
                'Повседневные, вечерние и офисные платья малыми и средними сериями.',
            )],
            ['blouses', $this->t('Bluze și cămăși', 'Blouses and shirts', 'Блузы и рубашки'), $this->t(
                'Modele din mătase, vâscoză și bumbac cu finisaje fine.',
                'Silk, viscose and cotton styles with fine finishing.',
                'Модели из шёлка, вискозы и хлопка с тонкой отделкой.',
            )],
            ['knitwear', $this->t('Tricotaje', 'Knitwear', 'Трикотаж'), $this->t(
                'Tricouri, topuri și rochii din tricot cu cusătură elastică.',
                'T-shirts, tops and jersey dresses with elastic seams.',
                'Футболки, топы и трикотажные платья с эластичным швом.',
            )],
            ['outerwear', $this->t('Îmbrăcăminte exterioară', 'Outerwear', 'Верхняя одежда'), $this->t(
                'Sacouri, trenciuri și paltoane cu structură și căptușeală.',
                'Blazers, trench coats and coats with structure and lining.',
                'Жакеты, тренчи и пальто с бортовкой и подкладкой.',
            )],
            ['trousers', $this->t('Pantaloni și fuste', 'Trousers and skirts', 'Брюки и юбки'), $this->t(
                'Croieli clasice și moderne, inclusiv modele cu talie înaltă.',
                'Classic and modern cuts, including high-waisted styles.',
                'Классические и современные посадки, включая высокую талию.',
            )],
            ['uniforms', $this->t('Uniforme corporative', 'Corporate uniforms', 'Корпоративная форма'), $this->t(
                'Seturi pentru hoteluri, retail și companii de servicii.',
                'Sets for hotels, retail and service companies.',
                'Комплекты для отелей, ритейла и сервисных компаний.',
            )],
        ];

        $map = [];

        foreach ($items as $index => [$slug, $name, $description]) {
            $category = ProductCategory::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'sort_order' => $index, 'is_active' => true],
            );

            $map[$slug] = $category->id;
        }

        return $map;
    }

    /** @return array<string, int> */
    private function fabrics(): array
    {
        $items = [
            ['viscose', $this->t('Vâscoză', 'Viscose', 'Вискоза'), '100% viscose', 130, '#d8cfc2'],
            ['silk', $this->t('Mătase naturală', 'Natural silk', 'Натуральный шёлк'), '100% silk', 90, '#e9dcd0'],
            ['cotton-poplin', $this->t('Popline de bumbac', 'Cotton poplin', 'Хлопковый поплин'), '100% cotton', 120, '#f2efe9'],
            ['jersey', $this->t('Tricot jerse', 'Jersey', 'Джерси'), '95% cotton, 5% elastane', 200, '#cfd3d8'],
            ['wool-suiting', $this->t('Stofă de lână', 'Wool suiting', 'Костюмная шерсть'), '80% wool, 20% polyester', 280, '#4a5162'],
            ['linen', $this->t('In', 'Linen', 'Лён'), '100% linen', 180, '#d6cfbb'],
            ['crepe', $this->t('Crep', 'Crepe', 'Креп'), '96% polyester, 4% elastane', 150, '#b8b3ae'],
        ];

        $map = [];

        foreach ($items as $index => [$slug, $name, $composition, $weight, $color]) {
            $fabric = Fabric::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'composition' => $this->t($composition, $composition, $composition),
                    'weight_gsm' => $weight,
                    'color_hex' => $color,
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );

            $map[$slug] = $fabric->id;
        }

        return $map;
    }

    /** @return array<string, int> */
    private function treatments(): array
    {
        $items = [
            ['embroidery', 'palette', $this->t('Broderie', 'Embroidery', 'Вышивка'), $this->t(
                'Broderie computerizată pe piept, mânecă sau spate.',
                'Computerised embroidery on the chest, sleeve or back.',
                'Компьютерная вышивка на груди, рукаве или спинке.',
            )],
            ['screen-print', 'palette', $this->t('Serigrafie', 'Screen printing', 'Шелкография'), $this->t(
                'Imprimare cu până la șase culori, inclusiv pe tricot.',
                'Printing in up to six colours, including on jersey.',
                'Печать до шести цветов, в том числе по трикотажу.',
            )],
            ['pleating', 'scissors', $this->t('Plisare', 'Pleating', 'Плиссировка'), $this->t(
                'Plisare fixată termic pentru fuste și rochii.',
                'Heat-set pleating for skirts and dresses.',
                'Термофиксированная плиссировка для юбок и платьев.',
            )],
            ['garment-wash', 'droplet', $this->t('Spălare finală', 'Garment wash', 'Финишная стирка'), $this->t(
                'Spălare industrială pentru moliciune și stabilitate dimensională.',
                'Industrial washing for softness and dimensional stability.',
                'Промышленная стирка для мягкости и стабильности размеров.',
            )],
            ['digital-print', 'printer', $this->t('Imprimare digitală', 'Digital printing', 'Цифровая печать'), $this->t(
                'Imprimare pe metraj pentru colecții cu imprimeu propriu.',
                'Roll printing for collections with a bespoke print.',
                'Печать по рулону для коллекций с собственным принтом.',
            )],
        ];

        $map = [];

        foreach ($items as $index => [$slug, $icon, $name, $description]) {
            $treatment = Treatment::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $description,
                    'icon' => $icon,
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );

            $map[$slug] = $treatment->id;
        }

        return $map;
    }

    /**
     * @param  array<string, int>  $categories
     * @param  array<string, int>  $fabrics
     * @param  array<string, int>  $treatments
     */
    private function products(array $categories, array $fabrics, array $treatments): void
    {
        $items = [
            [
                'slug' => 'midi-wrap-dress', 'category' => 'dresses', 'article' => 'GH-DR-101', 'featured' => true,
                'name' => $this->t('Rochie midi petrecută', 'Midi wrap dress', 'Платье миди на запах'),
                'summary' => $this->t(
                    'Croi petrecut cu cordon, din vâscoză cu cădere moale.',
                    'A wrap cut with a belt, in soft-draping viscose.',
                    'Запашной крой с поясом, из вискозы с мягкой драпировкой.',
                ),
                'min' => 300, 'lead' => 21,
                'fabrics' => ['viscose', 'crepe'], 'treatments' => ['digital-print'],
            ],
            [
                'slug' => 'silk-blouse', 'category' => 'blouses', 'article' => 'GH-BL-204', 'featured' => true,
                'name' => $this->t('Bluză din mătase', 'Silk blouse', 'Шёлковая блуза'),
                'summary' => $this->t(
                    'Bluză cu guler ascuțit și manșete duble, cusătură franceză.',
                    'A blouse with a pointed collar and double cuffs, French seams.',
                    'Блуза с острым воротником и двойными манжетами, французский шов.',
                ),
                'min' => 300, 'lead' => 18,
                'fabrics' => ['silk', 'viscose'], 'treatments' => ['embroidery'],
            ],
            [
                'slug' => 'cotton-shirt', 'category' => 'blouses', 'article' => 'GH-BL-208', 'featured' => false,
                'name' => $this->t('Cămașă din popline', 'Poplin shirt', 'Рубашка из поплина'),
                'summary' => $this->t(
                    'Model clasic cu platcă dublă și nasturi din sidef.',
                    'A classic style with a double yoke and mother-of-pearl buttons.',
                    'Классическая модель с двойной кокеткой и перламутровыми пуговицами.',
                ),
                'min' => 400, 'lead' => 16,
                'fabrics' => ['cotton-poplin'], 'treatments' => ['screen-print'],
            ],
            [
                'slug' => 'jersey-tee', 'category' => 'knitwear', 'article' => 'GH-KN-310', 'featured' => true,
                'name' => $this->t('Tricou din jerse', 'Jersey T-shirt', 'Футболка из джерси'),
                'summary' => $this->t(
                    'Tricot cu elastan, cusătură de acoperire la tiv.',
                    'Jersey with elastane and a coverstitched hem.',
                    'Трикотаж с эластаном, распошивальный шов по низу.',
                ),
                'min' => 500, 'lead' => 14,
                'fabrics' => ['jersey'], 'treatments' => ['screen-print', 'garment-wash'],
            ],
            [
                'slug' => 'wool-blazer', 'category' => 'outerwear', 'article' => 'GH-OW-402', 'featured' => true,
                'name' => $this->t('Sacou din lână', 'Wool blazer', 'Шерстяной жакет'),
                'summary' => $this->t(
                    'Sacou structurat cu căptușeală completă și buzunare pasepoal.',
                    'A structured blazer with full lining and jetted pockets.',
                    'Структурированный жакет с полной подкладкой и прорезными карманами.',
                ),
                'min' => 200, 'lead' => 28,
                'fabrics' => ['wool-suiting'], 'treatments' => [],
            ],
            [
                'slug' => 'linen-trousers', 'category' => 'trousers', 'article' => 'GH-TR-505', 'featured' => false,
                'name' => $this->t('Pantaloni din in', 'Linen trousers', 'Льняные брюки'),
                'summary' => $this->t(
                    'Talie înaltă, croi drept, buzunare laterale ascunse.',
                    'High waist, straight leg, concealed side pockets.',
                    'Высокая талия, прямой крой, скрытые боковые карманы.',
                ),
                'min' => 300, 'lead' => 18,
                'fabrics' => ['linen'], 'treatments' => ['garment-wash'],
            ],
            [
                'slug' => 'pleated-skirt', 'category' => 'trousers', 'article' => 'GH-TR-512', 'featured' => false,
                'name' => $this->t('Fustă plisată', 'Pleated skirt', 'Плиссированная юбка'),
                'summary' => $this->t(
                    'Plisare fixată termic, betelie elastică ascunsă.',
                    'Heat-set pleats with a concealed elastic waistband.',
                    'Термофиксированная плиссировка, скрытый эластичный пояс.',
                ),
                'min' => 300, 'lead' => 20,
                'fabrics' => ['crepe'], 'treatments' => ['pleating'],
            ],
            [
                'slug' => 'hotel-uniform-set', 'category' => 'uniforms', 'article' => 'GH-UN-601', 'featured' => false,
                'name' => $this->t('Set uniformă de hotel', 'Hotel uniform set', 'Комплект формы для отеля'),
                'summary' => $this->t(
                    'Sacou, cămașă și fustă cu broderia logo-ului clientului.',
                    'Blazer, shirt and skirt with the client’s embroidered logo.',
                    'Жакет, рубашка и юбка с вышитым логотипом заказчика.',
                ),
                'min' => 150, 'lead' => 25,
                'fabrics' => ['wool-suiting', 'cotton-poplin'], 'treatments' => ['embroidery'],
            ],
        ];

        foreach ($items as $index => $item) {
            $product = Product::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categories[$item['category']] ?? null,
                    'article' => $item['article'],
                    'name' => $item['name'],
                    'short_description' => $item['summary'],
                    'description' => $item['summary'],
                    'min_order_quantity' => $item['min'],
                    'lead_time_days' => $item['lead'],
                    'is_featured' => $item['featured'],
                    'attributes' => [
                        [
                            'label' => $this->t('Grilă de mărimi', 'Size grid', 'Размерная сетка'),
                            'value' => $this->t('34 – 46 (EU)', '34 – 46 (EU)', '34 – 46 (EU)'),
                        ],
                        [
                            'label' => $this->t('Ambalare', 'Packing', 'Упаковка'),
                            'value' => $this->t('individual, pe umeraș', 'individual, hanging', 'индивидуальная, на вешалке'),
                        ],
                    ],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );

            $product->fabrics()->sync(array_values(array_intersect_key($fabrics, array_flip($item['fabrics']))));
            $product->treatments()->sync(array_values(array_intersect_key($treatments, array_flip($item['treatments']))));
        }
    }
}
