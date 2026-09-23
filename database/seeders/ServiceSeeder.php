<?php

namespace Database\Seeders;

use App\Models\Service;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        foreach ($this->services() as $index => $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                array_merge($service, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function services(): array
    {
        return [
            [
                'slug' => 'cmt-production',
                'icon' => 'factory',
                'is_featured' => true,
                'name' => $this->t('Producție la comandă (CMT)', 'Made-to-order manufacturing (CMT)', 'Производство под заказ (CMT)'),
                'short_description' => $this->t(
                    'Coasem după tiparele și materialele dumneavoastră, cu control pe fiecare operație.',
                    'We sew to your patterns and materials, with inspection at every operation.',
                    'Шьём по вашим лекалам и материалам, с контролем на каждой операции.',
                ),
                'description' => $this->t(
                    'Preluăm tiparul și materialul, pregătim linia, coasem lotul și îl predăm ambalat conform cerințelor brandului. Raportăm pe etape, astfel încât să știți în orice moment unde se află comanda.',
                    'We take your pattern and fabric, set up the line, sew the batch and hand it over packed to the brand’s specification. We report stage by stage so you always know where the order stands.',
                    'Принимаем лекала и материал, готовим линию, отшиваем партию и передаём её упакованной по требованиям бренда. Отчитываемся по этапам, поэтому вы всегда знаете статус заказа.',
                ),
                'lead_time' => $this->t('14 – 28 de zile', '14 – 28 days', '14 – 28 дней'),
                'highlights' => [
                    ['icon' => 'package', 'label' => $this->t('Lot minim', 'Minimum batch', 'Минимальная партия'), 'value' => $this->t('300 bucăți', '300 pieces', '300 единиц')],
                    ['icon' => 'clock', 'label' => $this->t('Termen', 'Lead time', 'Срок'), 'value' => $this->t('de la 14 zile', 'from 14 days', 'от 14 дней')],
                    ['icon' => 'shield-check', 'label' => $this->t('Control', 'Inspection', 'Контроль'), 'value' => $this->t('100% din articole', '100% of garments', '100% изделий')],
                ],
                'process_steps' => [
                    ['title' => $this->t('Brief și fișa tehnică', 'Brief and tech pack', 'Бриф и техописание'), 'text' => $this->t('Analizăm modelul, materialul și grila de mărimi.', 'We review the style, fabric and size grid.', 'Разбираем модель, материал и размерную сетку.')],
                    ['title' => $this->t('Mostră', 'Sample', 'Образец'), 'text' => $this->t('Coasem mostra și confirmăm potrivirea.', 'We sew the sample and confirm the fit.', 'Отшиваем образец и подтверждаем посадку.')],
                    ['title' => $this->t('Lansarea lotului', 'Batch launch', 'Запуск партии'), 'text' => $this->t('Croim, cusem și controlăm pe operații.', 'We cut, sew and inspect operation by operation.', 'Раскраиваем, шьём и проверяем пооперационно.')],
                    ['title' => $this->t('Ambalare și livrare', 'Packing and shipping', 'Упаковка и отгрузка'), 'text' => $this->t('Etichetăm, ambalăm și predăm către logistică.', 'We label, pack and hand over to logistics.', 'Маркируем, упаковываем и передаём логистике.')],
                ],
            ],
            [
                'slug' => 'model-development',
                'icon' => 'ruler',
                'is_featured' => true,
                'name' => $this->t('Dezvoltare de modele', 'Model development', 'Разработка моделей'),
                'short_description' => $this->t(
                    'De la schiță la tipar industrial și grila de mărimi pentru piața dumneavoastră.',
                    'From sketch to industrial pattern and a size grid for your market.',
                    'От эскиза до промышленных лекал и размерной сетки под ваш рынок.',
                ),
                'description' => $this->t(
                    'Constructorii noștri transformă schița sau mostra existentă într-un tipar pregătit pentru producție: gradare pe mărimi, consum optimizat de material și fișă tehnică completă.',
                    'Our pattern makers turn a sketch or an existing sample into a production-ready pattern: size grading, optimised fabric consumption and a complete tech pack.',
                    'Конструкторы превращают эскиз или существующий образец в готовые к производству лекала: градация по размерам, оптимизированный расход материала и полное техописание.',
                ),
                'lead_time' => $this->t('5 – 10 zile', '5 – 10 days', '5 – 10 дней'),
                'highlights' => [
                    ['icon' => 'ruler', 'label' => $this->t('Gradare', 'Grading', 'Градация'), 'value' => $this->t('34 – 52 (EU)', '34 – 52 (EU)', '34 – 52 (EU)')],
                    ['icon' => 'palette', 'label' => $this->t('Mostre', 'Samples', 'Образцы'), 'value' => $this->t('până la 3 iterații', 'up to 3 iterations', 'до 3 итераций')],
                ],
                'process_steps' => [
                    ['title' => $this->t('Schița sau mostra', 'Sketch or sample', 'Эскиз или образец'), 'text' => $this->t('Primim referința și discutăm detaliile constructive.', 'We receive the reference and discuss construction details.', 'Получаем референс и обсуждаем конструктивные детали.')],
                    ['title' => $this->t('Tiparul de bază', 'Base pattern', 'Базовые лекала'), 'text' => $this->t('Construim tiparul și coasem prototipul.', 'We build the pattern and sew the prototype.', 'Строим лекала и отшиваем прототип.')],
                    ['title' => $this->t('Gradare', 'Grading', 'Градация'), 'text' => $this->t('Extindem tiparul pe toată grila de mărimi.', 'We grade the pattern across the full size range.', 'Разводим лекала на всю размерную сетку.')],
                ],
            ],
            [
                'slug' => 'quality-control',
                'icon' => 'shield-check',
                'is_featured' => true,
                'name' => $this->t('Controlul calității', 'Quality control', 'Контроль качества'),
                'short_description' => $this->t(
                    'Verificarea materialului, a cusăturii și a măsurilor înainte de ambalare.',
                    'Checking fabric, stitching and measurements before packing.',
                    'Проверка материала, строчки и мерок до упаковки.',
                ),
                'description' => $this->t(
                    'Laboratorul testează materialul la contracție, frecare și stabilitatea culorii, iar linia aplică controlul pe operații. Fiecare articol este verificat înainte de ambalare, iar rezultatele intră în raportul de lot.',
                    'The laboratory tests fabric for shrinkage, rubbing and colour fastness, while the line applies operation-level inspection. Every garment is checked before packing and the results go into the batch report.',
                    'Лаборатория проверяет материал на усадку, истирание и стойкость цвета, а линия ведёт пооперационный контроль. Каждое изделие проверяется до упаковки, результаты попадают в отчёт по партии.',
                ),
                'lead_time' => $this->t('inclus în termenul lotului', 'included in the batch lead time', 'входит в срок партии'),
                'highlights' => [
                    ['icon' => 'badge-check', 'label' => $this->t('Testări', 'Tests', 'Испытания'), 'value' => $this->t('contracție, frecare, culoare', 'shrinkage, rubbing, colour', 'усадка, истирание, цвет')],
                    ['icon' => 'award', 'label' => $this->t('Raport', 'Report', 'Отчёт'), 'value' => $this->t('pentru fiecare lot', 'for every batch', 'по каждой партии')],
                ],
                'process_steps' => [
                    ['title' => $this->t('Intrarea materialului', 'Fabric intake', 'Входной контроль'), 'text' => $this->t('Verificăm metrajul, culoarea și defectele.', 'We check yardage, colour and defects.', 'Проверяем метраж, цвет и пороки.')],
                    ['title' => $this->t('Control pe linie', 'In-line inspection', 'Контроль на линии'), 'text' => $this->t('Verificăm cusătura la fiecare operație-cheie.', 'We check the seam at every key operation.', 'Проверяем шов на каждой ключевой операции.')],
                    ['title' => $this->t('Control final', 'Final inspection', 'Финальный контроль'), 'text' => $this->t('Măsurători, aspect, etichete, ambalaj.', 'Measurements, appearance, labels, packaging.', 'Мерки, внешний вид, этикетки, упаковка.')],
                ],
            ],
            [
                'slug' => 'fabric-sourcing',
                'icon' => 'globe',
                'is_featured' => false,
                'name' => $this->t('Selecția materialelor', 'Fabric sourcing', 'Подбор материалов'),
                'short_description' => $this->t(
                    'Găsim materialul potrivit la furnizori verificați din Europa și Turcia.',
                    'We find the right fabric from vetted suppliers in Europe and Türkiye.',
                    'Находим подходящий материал у проверенных поставщиков Европы и Турции.',
                ),
                'description' => $this->t(
                    'Dacă brandul nu are furnizor propriu, selectăm materialul după cerințele de compoziție, gramaj și preț, comandăm mostre și confirmăm alegerea împreună cu dumneavoastră.',
                    'If the brand has no supplier of its own, we select fabric to your composition, weight and price requirements, order swatches and confirm the choice together with you.',
                    'Если у бренда нет своего поставщика, подбираем материал по составу, плотности и цене, заказываем образцы и согласовываем выбор с вами.',
                ),
                'lead_time' => $this->t('7 – 21 de zile', '7 – 21 days', '7 – 21 день'),
                'highlights' => [
                    ['icon' => 'truck', 'label' => $this->t('Furnizori', 'Suppliers', 'Поставщики'), 'value' => $this->t('UE și Turcia', 'EU and Türkiye', 'ЕС и Турция')],
                ],
                'process_steps' => [
                    ['title' => $this->t('Cerințe', 'Requirements', 'Требования'), 'text' => $this->t('Compoziție, gramaj, buget, certificări.', 'Composition, weight, budget, certifications.', 'Состав, плотность, бюджет, сертификация.')],
                    ['title' => $this->t('Mostre', 'Swatches', 'Образцы'), 'text' => $this->t('Comandăm și trimitem mostrele spre aprobare.', 'We order and send swatches for approval.', 'Заказываем и отправляем образцы на утверждение.')],
                ],
            ],
        ];
    }
}
