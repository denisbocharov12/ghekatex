<?php

namespace Database\Seeders;

use App\Enums\OfficeType;
use App\Models\Certificate;
use App\Models\CompanyMilestone;
use App\Models\Facility;
use App\Models\Office;
use App\Models\Partner;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        $this->milestones();
        $this->facilities();
        $this->certificates();
        $this->partners();
        $this->offices();
    }

    private function milestones(): void
    {
        $items = [
            ['2014', $this->t('Fondarea companiei', 'The company is founded', 'Основание компании'), $this->t(
                'Primul atelier cu 20 de angajați și comenzi pentru piața locală.',
                'The first workshop with 20 employees and orders for the local market.',
                'Первый цех на 20 сотрудников и заказы для локального рынка.',
            )],
            ['2017', $this->t('Primul contract european', 'First European contract', 'Первый европейский контракт'), $this->t(
                'Începem producția pentru un brand de damă din Italia.',
                'We start manufacturing for an Italian womenswear label.',
                'Начинаем производство для женского бренда из Италии.',
            )],
            ['2019', $this->t('Secție proprie de croire', 'In-house cutting department', 'Собственный раскройный участок'), $this->t(
                'Instalăm masa automată de croire și reducem termenele cu o treime.',
                'We install an automated cutting table and cut lead times by a third.',
                'Устанавливаем автоматический раскройный стол и сокращаем сроки на треть.',
            )],
            ['2021', $this->t('Extindere în Polonia și Spania', 'Expansion to Poland and Spain', 'Выход в Польшу и Испанию'), $this->t(
                'Portofoliul ajunge la cinci branduri europene permanente.',
                'The portfolio grows to five permanent European brands.',
                'Портфель вырастает до пяти постоянных европейских брендов.',
            )],
            ['2023', $this->t('Laborator de control al calității', 'Quality control laboratory', 'Лаборатория контроля качества'), $this->t(
                'Verificăm rezistența cusăturii, stabilitatea culorii și contracția materialului.',
                'We test seam strength, colour fastness and fabric shrinkage.',
                'Проверяем прочность шва, стойкость цвета и усадку материала.',
            )],
            ['2025', $this->t('Linie de producție sustenabilă', 'Sustainable production line', 'Линия устойчивого производства'), $this->t(
                'Trecem la ambalaje reciclabile și optimizăm consumul de material.',
                'We switch to recyclable packaging and optimise material consumption.',
                'Переходим на перерабатываемую упаковку и оптимизируем расход материала.',
            )],
        ];

        foreach ($items as $index => [$year, $title, $description]) {
            CompanyMilestone::query()->updateOrCreate(
                ['year' => $year],
                ['title' => $title, 'description' => $description, 'sort_order' => $index, 'is_active' => true],
            );
        }
    }

    private function facilities(): void
    {
        $items = [
            [
                'slug' => 'cutting',
                'icon' => 'scissors',
                'name' => $this->t('Secția de croire', 'Cutting department', 'Раскройный участок'),
                'summary' => $this->t(
                    'Croire automată cu optimizarea consumului de material.',
                    'Automated cutting with material-consumption optimisation.',
                    'Автоматический раскрой с оптимизацией расхода материала.',
                ),
                'capacity_per_month' => 12000,
                'employees_count' => 18,
                'specs' => [
                    ['label' => $this->t('Masă de croire', 'Cutting table', 'Раскройный стол'), 'value' => $this->t('8 m, automată', '8 m, automated', '8 м, автоматический')],
                    ['label' => $this->t('Înălțimea stivei', 'Stack height', 'Высота настила'), 'value' => $this->t('până la 7 cm', 'up to 7 cm', 'до 7 см')],
                    ['label' => $this->t('Software de tipare', 'Pattern software', 'ПО для лекал'), 'value' => $this->t('CAD 2D', 'CAD 2D', 'CAD 2D')],
                ],
            ],
            [
                'slug' => 'sewing',
                'icon' => 'factory',
                'name' => $this->t('Liniile de cusut', 'Sewing lines', 'Швейные линии'),
                'summary' => $this->t(
                    'Două linii configurabile pentru tricot și țesături.',
                    'Two configurable lines for jersey and woven fabrics.',
                    'Две перенастраиваемые линии для трикотажа и тканей.',
                ),
                'capacity_per_month' => 10000,
                'employees_count' => 120,
                'specs' => [
                    ['label' => $this->t('Posturi de lucru', 'Workstations', 'Рабочих мест'), 'value' => $this->t('120', '120', '120')],
                    ['label' => $this->t('Utilaje speciale', 'Special machines', 'Спецмашины'), 'value' => $this->t('overlock, acoperire, butoniere', 'overlock, coverstitch, buttonholes', 'оверлок, распошивальные, петельные')],
                    ['label' => $this->t('Lot minim', 'Minimum batch', 'Минимальная партия'), 'value' => $this->t('300 buc.', '300 pcs', '300 шт.')],
                ],
            ],
            [
                'slug' => 'quality-lab',
                'icon' => 'shield-check',
                'name' => $this->t('Laboratorul de calitate', 'Quality laboratory', 'Лаборатория качества'),
                'summary' => $this->t(
                    'Testăm materialele înainte de lansarea lotului.',
                    'We test materials before the batch goes into production.',
                    'Тестируем материалы до запуска партии.',
                ),
                'employees_count' => 6,
                'specs' => [
                    ['label' => $this->t('Teste', 'Tests', 'Испытания'), 'value' => $this->t('contracție, frecare, rezistență', 'shrinkage, rubbing, strength', 'усадка, истирание, прочность')],
                    ['label' => $this->t('Control final', 'Final inspection', 'Финальный контроль'), 'value' => $this->t('100% din articole', '100% of garments', '100% изделий')],
                ],
            ],
            [
                'slug' => 'finishing',
                'icon' => 'package',
                'name' => $this->t('Finisare și ambalare', 'Finishing and packing', 'Отделка и упаковка'),
                'summary' => $this->t(
                    'Călcare, etichetare și ambalare conform cerințelor brandului.',
                    'Pressing, labelling and packing to the brand’s specification.',
                    'ВТО, маркировка и упаковка по требованиям бренда.',
                ),
                'employees_count' => 24,
                'specs' => [
                    ['label' => $this->t('Ambalare', 'Packing', 'Упаковка'), 'value' => $this->t('pe umeraș sau plat', 'hanging or flat', 'на вешалке или плоская')],
                    ['label' => $this->t('Etichete', 'Labels', 'Этикетки'), 'value' => $this->t('conform manualului de brand', 'per brand manual', 'по брендбуку заказчика')],
                ],
            ],
        ];

        foreach ($items as $index => $item) {
            Facility::query()->updateOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }

    private function certificates(): void
    {
        $items = [
            [
                'name' => $this->t('ISO 9001:2015', 'ISO 9001:2015', 'ISO 9001:2015'),
                'issuer' => $this->t('Sistem de management al calității', 'Quality management system', 'Система менеджмента качества'),
                'description' => $this->t(
                    'Procese documentate de planificare, control și îmbunătățire continuă.',
                    'Documented planning, control and continuous-improvement processes.',
                    'Документированные процессы планирования, контроля и улучшений.',
                ),
                'number' => 'QMS-2023-0142',
            ],
            [
                'name' => $this->t('OEKO-TEX Standard 100', 'OEKO-TEX Standard 100', 'OEKO-TEX Standard 100'),
                'issuer' => $this->t('Siguranța materialelor textile', 'Textile safety', 'Безопасность текстиля'),
                'description' => $this->t(
                    'Materialele folosite nu conțin substanțe nocive pentru sănătate.',
                    'The materials we use contain no substances harmful to health.',
                    'Используемые материалы не содержат вредных для здоровья веществ.',
                ),
                'number' => 'OT-100-88231',
            ],
            [
                'name' => $this->t('GOTS — bumbac organic', 'GOTS — organic cotton', 'GOTS — органический хлопок'),
                'issuer' => $this->t('Global Organic Textile Standard', 'Global Organic Textile Standard', 'Global Organic Textile Standard'),
                'description' => $this->t(
                    'Lucrăm cu furnizori certificați pentru colecțiile sustenabile.',
                    'We work with certified suppliers for sustainable collections.',
                    'Работаем с сертифицированными поставщиками для устойчивых коллекций.',
                ),
                'number' => 'GOTS-MD-0097',
            ],
            [
                'name' => $this->t('BSCI — audit social', 'BSCI — social audit', 'BSCI — социальный аудит'),
                'issuer' => $this->t('amfori BSCI', 'amfori BSCI', 'amfori BSCI'),
                'description' => $this->t(
                    'Condiții de muncă verificate independent, fără muncă forțată sau a copiilor.',
                    'Independently verified working conditions, free of forced and child labour.',
                    'Независимо проверенные условия труда, без принудительного и детского труда.',
                ),
                'number' => 'BSCI-2024-5511',
            ],
        ];

        foreach ($items as $index => $item) {
            Certificate::query()->updateOrCreate(
                ['number' => $item['number']],
                array_merge($item, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }

    private function partners(): void
    {
        $items = [
            ['Maison Aurelia', 'IT', true],
            ['Nordwear Studio', 'PL', true],
            ['Casa Verde Moda', 'ES', true],
            ['Linea Prima', 'IT', false],
            ['Atelier Warszawa', 'PL', false],
            ['Textil Ibérica', 'ES', false],
        ];

        foreach ($items as $index => [$name, $country, $featured]) {
            Partner::query()->updateOrCreate(
                ['name' => $name],
                [
                    'country_code' => $country,
                    'is_featured' => $featured,
                    'description' => $this->t(
                        'Partener de producție din anul 2021.',
                        'Manufacturing partner since 2021.',
                        'Производственный партнёр с 2021 года.',
                    ),
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
        }
    }

    private function offices(): void
    {
        $items = [
            [
                'type' => OfficeType::Office->value,
                'name' => $this->t('Birou central', 'Head office', 'Центральный офис'),
                'address' => $this->t('str. Industrială 42, of. 5', '42 Industriala St., office 5', 'ул. Индустриалэ 42, оф. 5'),
                'city' => $this->t('Chișinău', 'Chisinau', 'Кишинёв'),
                'postal_code' => 'MD-2036',
                'phones' => ['+373 22 000 000'],
                'emails' => ['office@ghekatex.md'],
                'working_hours' => $this->t('Luni – Vineri, 08:00 – 17:00', 'Monday – Friday, 08:00 – 17:00', 'Пн – Пт, 08:00 – 17:00'),
                'latitude' => 47.0105,
                'longitude' => 28.8638,
                'is_primary' => true,
            ],
            [
                'type' => OfficeType::Factory->value,
                'name' => $this->t('Fabrica de producție', 'Production plant', 'Производственная площадка'),
                'address' => $this->t('str. Uzinelor 15', '15 Uzinelor St.', 'ул. Узинелор 15'),
                'city' => $this->t('Chișinău', 'Chisinau', 'Кишинёв'),
                'postal_code' => 'MD-2023',
                'phones' => ['+373 69 000 000'],
                'emails' => ['production@ghekatex.md'],
                'working_hours' => $this->t('Luni – Vineri, 07:00 – 19:00', 'Monday – Friday, 07:00 – 19:00', 'Пн – Пт, 07:00 – 19:00'),
                'latitude' => 47.0242,
                'longitude' => 28.8975,
                'is_primary' => false,
            ],
        ];

        foreach ($items as $index => $item) {
            Office::query()->updateOrCreate(
                ['type' => $item['type'], 'postal_code' => $item['postal_code']],
                array_merge($item, ['country_code' => 'MD', 'sort_order' => $index, 'is_active' => true]),
            );
        }
    }
}
