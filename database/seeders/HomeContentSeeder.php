<?php

namespace Database\Seeders;

use App\Models\Advantage;
use App\Models\HeroSlide;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        $this->slides();
        $this->advantages();
    }

    private function slides(): void
    {
        $slides = [
            [
                // «Made in Moldova» теперь несёт знак происхождения в углу обложки
                'eyebrow' => $this->t('Ciclu complet de producție', 'Full-cycle manufacturing', 'Полный цикл производства'),
                'title' => $this->t(
                    'Croim colecții de damă pentru branduri europene',
                    'We craft womenswear collections for European brands',
                    'Шьём женские коллекции для европейских брендов',
                ),
                'description' => $this->t(
                    'Ciclu complet: construcție, mostră, croire, cusut, control al calității și ambalare.',
                    'A full cycle: pattern making, sampling, cutting, sewing, quality control and packing.',
                    'Полный цикл: конструирование, образец, раскрой, пошив, контроль качества и упаковка.',
                ),
                'cta_label' => $this->t('Discutați proiectul', 'Discuss your project', 'Обсудить проект'),
                'cta_url' => '/ro/contacts',
                'secondary_cta_label' => $this->t('Vezi catalogul', 'View the catalogue', 'Смотреть каталог'),
                'secondary_cta_url' => '/ro/catalog',
                'overlay_opacity' => 60,
            ],
            [
                'eyebrow' => $this->t('Calitate', 'Quality', 'Качество'),
                'title' => $this->t(
                    'Control pe fiecare operație, nu doar la final',
                    'Inspection at every operation, not only at the end',
                    'Контроль на каждой операции, а не только в конце',
                ),
                'description' => $this->t(
                    'Fiecare lot trece prin verificarea cusăturii, a măsurilor și a aspectului înainte de ambalare.',
                    'Every batch is checked for stitching, measurements and appearance before packing.',
                    'Каждая партия проверяется по строчке, меркам и внешнему виду до упаковки.',
                ),
                'cta_label' => $this->t('Despre proces', 'About the process', 'О процессе'),
                'cta_url' => '/ro/services',
                'overlay_opacity' => 55,
            ],
            [
                'eyebrow' => $this->t('Livrare', 'Delivery', 'Логистика'),
                'title' => $this->t(
                    'Italia, Polonia, Spania — în câteva zile',
                    'Italy, Poland, Spain — within days',
                    'Италия, Польша, Испания — за несколько дней',
                ),
                'description' => $this->t(
                    'Poziția geografică ne permite termene scurte fără costurile transportului din Asia.',
                    'Our location delivers short lead times without the cost of shipping from Asia.',
                    'География даёт короткие сроки без стоимости доставки из Азии.',
                ),
                'cta_label' => $this->t('Contacte', 'Contacts', 'Контакты'),
                'cta_url' => '/ro/contacts',
                'overlay_opacity' => 50,
            ],
        ];

        foreach ($slides as $index => $slide) {
            HeroSlide::query()->updateOrCreate(
                ['cta_url' => $slide['cta_url'], 'sort_order' => $index],
                array_merge($slide, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }

    private function advantages(): void
    {
        $items = [
            [
                'icon' => 'factory',
                'value' => '120 000',
                'is_counter' => true,
                'value_suffix' => $this->t('articole pe an', 'garments per year', 'изделий в год'),
                'title' => $this->t('Capacitate de producție', 'Production capacity', 'Производственная мощность'),
                'description' => $this->t(
                    'Două linii de cusut și o secție de croire care acoperă loturi de la 300 de bucăți.',
                    'Two sewing lines and a cutting department covering batches from 300 pieces.',
                    'Две швейные линии и раскройный участок, партии от 300 единиц.',
                ),
            ],
            [
                'icon' => 'users',
                'value' => '180',
                'is_counter' => true,
                'value_suffix' => $this->t('angajați', 'employees', 'сотрудников'),
                'title' => $this->t('Echipă cu experiență', 'An experienced team', 'Опытная команда'),
                'description' => $this->t(
                    'Croitori, tehnologi și constructori cu experiență în segmentul premium.',
                    'Tailors, technologists and pattern makers experienced in the premium segment.',
                    'Швеи, технологи и конструкторы с опытом в премиальном сегменте.',
                ),
            ],
            [
                'icon' => 'clock',
                'value' => '21',
                'is_counter' => true,
                'value_suffix' => $this->t('zile — termen mediu', 'days average lead time', 'дней — средний срок'),
                'title' => $this->t('Termene previzibile', 'Predictable lead times', 'Предсказуемые сроки'),
                'description' => $this->t(
                    'De la aprobarea mostrei până la lotul ambalat, cu raportare pe etape.',
                    'From sample approval to a packed batch, with stage-by-stage reporting.',
                    'От утверждения образца до упакованной партии, с отчётностью по этапам.',
                ),
            ],
            [
                'icon' => 'globe',
                'value' => '8',
                'is_counter' => true,
                'value_suffix' => $this->t('țări de export', 'export markets', 'стран экспорта'),
                'title' => $this->t('Export european', 'European export', 'Европейский экспорт'),
                'description' => $this->t(
                    'Italia, Polonia, Spania, Germania, Franța, România, Cehia, Țările de Jos.',
                    'Italy, Poland, Spain, Germany, France, Romania, Czechia, the Netherlands.',
                    'Италия, Польша, Испания, Германия, Франция, Румыния, Чехия, Нидерланды.',
                ),
            ],
            [
                'icon' => 'ruler',
                'title' => $this->t('Dezvoltare de modele', 'Model development', 'Разработка моделей'),
                'description' => $this->t(
                    'Construim tiparul de la schiță și adaptăm grila de mărimi la piața dumneavoastră.',
                    'We build the pattern from a sketch and adapt the size grid to your market.',
                    'Строим лекала от эскиза и адаптируем размерную сетку под ваш рынок.',
                ),
            ],
            [
                'icon' => 'shield-check',
                'title' => $this->t('Standarde de calitate', 'Quality standards', 'Стандарты качества'),
                'description' => $this->t(
                    'Proceduri documentate de control și trasabilitatea fiecărui lot.',
                    'Documented inspection procedures and traceability for every batch.',
                    'Документированные процедуры контроля и прослеживаемость каждой партии.',
                ),
            ],
        ];

        foreach ($items as $index => $item) {
            Advantage::query()->updateOrCreate(
                ['icon' => $item['icon'], 'sort_order' => $index],
                array_merge($item, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }
}
