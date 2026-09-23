<?php

namespace Database\Seeders;

use App\Enums\FaqGroup;
use App\Models\Faq;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

/**
 * Вопросы, которые закупщик задаёт до первого письма.
 * Тексты отвечают на главное недоразумение: это производство, а не магазин.
 */
class FaqSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        foreach ($this->items() as $index => [$group, $question, $answer]) {
            Faq::query()->updateOrCreate(
                ['group' => $group->value, 'sort_order' => $index],
                [
                    'question' => $question,
                    'answer' => $answer,
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
        }
    }

    /** @return array<int, array{0: FaqGroup, 1: array<string, string>, 2: array<string, string>}> */
    private function items(): array
    {
        return [
            [
                FaqGroup::General,
                $this->t(
                    'Vindeți articolele din catalog?',
                    'Do you sell the garments shown in the catalogue?',
                    'Вы продаёте изделия из каталога?',
                ),
                $this->t(
                    '<p>Nu. GHEKATEX este o fabrică, nu un magazin: nu ținem marfă pe stoc și nu vindem bucata. Catalogul arată ce tipuri de articole putem produce și la ce calitate. Lucrăm la comandă, de la lotul minim în sus.</p>',
                    '<p>No. GHEKATEX is a factory, not a shop: we hold no stock and do not sell single items. The catalogue shows the garment types we can produce and the quality we work to. We manufacture to order, from the minimum batch upwards.</p>',
                    '<p>Нет. GHEKATEX — производство, а не магазин: склада готовой продукции у нас нет и поштучно мы не продаём. Каталог показывает, какие типы изделий мы умеем шить и с каким качеством. Работаем под заказ, от минимальной партии.</p>',
                ),
            ],
            [
                FaqGroup::General,
                $this->t('Care este lotul minim?', 'What is the minimum batch?', 'Какая минимальная партия?'),
                $this->t(
                    '<p>De regulă 300 de bucăți pe model și culoare, iar pentru articole structurate — de la 200. Pentru primul lot de probă discutăm individual: ne interesează colaborarea pe termen lung, nu o singură comandă.</p>',
                    '<p>Usually 300 pieces per style and colour, and from 200 for structured garments. For a first trial batch we discuss it individually: we are after a long-term partnership, not a single order.</p>',
                    '<p>Обычно 300 единиц на модель и цвет, для структурированных изделий — от 200. По первой пробной партии договариваемся отдельно: нам важно длительное сотрудничество, а не разовый заказ.</p>',
                ),
            ],
            [
                FaqGroup::General,
                $this->t('Cât durează o comandă?', 'How long does an order take?', 'Сколько занимает заказ?'),
                $this->t(
                    '<p>De la aprobarea mostrei până la lotul ambalat — 14–28 de zile, în funcție de complexitate și de disponibilitatea materialului. Mostra în sine se face în 5–10 zile.</p>',
                    '<p>From sample approval to a packed batch — 14 to 28 days, depending on complexity and fabric availability. The sample itself takes 5 to 10 days.</p>',
                    '<p>От утверждения образца до упакованной партии — 14–28 дней, в зависимости от сложности и наличия материала. Сам образец делаем за 5–10 дней.</p>',
                ),
            ],
            [
                FaqGroup::General,
                $this->t('Livrați în Uniunea Europeană?', 'Do you ship to the EU?', 'Отгружаете в ЕС?'),
                $this->t(
                    '<p>Da. Livrăm rutier în Italia, Polonia, Spania, România și alte țări din UE — în câteva zile de la expediere. Documentele vamale le pregătim noi.</p>',
                    '<p>Yes. We ship by road to Italy, Poland, Spain, Romania and other EU countries — within days of dispatch. We prepare the customs paperwork ourselves.</p>',
                    '<p>Да. Отгружаем автотранспортом в Италию, Польшу, Испанию, Румынию и другие страны ЕС — за несколько дней после отгрузки. Таможенные документы готовим сами.</p>',
                ),
            ],
            [
                FaqGroup::General,
                $this->t('Cine asigură materialul?', 'Who supplies the fabric?', 'Кто обеспечивает материал?'),
                $this->t(
                    '<p>Ambele variante funcționează. Dacă aveți furnizor propriu, primim materialul și îl verificăm la intrare. Dacă nu — selectăm noi ţesătura după compoziție, gramaj și buget și vă trimitem mostrele spre aprobare.</p>',
                    '<p>Both options work. If you have your own supplier, we receive the fabric and inspect it on arrival. If not, we source it to your composition, weight and budget and send swatches for approval.</p>',
                    '<p>Работают оба варианта. Если у вас свой поставщик — принимаем материал и проверяем его на входе. Если нет — подбираем ткань по составу, плотности и бюджету и присылаем образцы на утверждение.</p>',
                ),
            ],
            [
                FaqGroup::General,
                $this->t(
                    'Puteți dezvolta modelul de la zero?',
                    'Can you develop a style from scratch?',
                    'Можете разработать модель с нуля?',
                ),
                $this->t(
                    '<p>Da. Din schiță sau dintr-o mostră existentă construim tiparul industrial, facem gradarea pe mărimi și pregătim fișa tehnică. Tiparul rămâne al dumneavoastră.</p>',
                    '<p>Yes. From a sketch or an existing sample we build the industrial pattern, grade the sizes and prepare the tech pack. The pattern stays yours.</p>',
                    '<p>Да. По эскизу или существующему образцу строим промышленные лекала, делаем градацию по размерам и готовим техописание. Лекала остаются вашими.</p>',
                ),
            ],
            [
                FaqGroup::Services,
                $this->t('Ce înseamnă CMT?', 'What does CMT mean?', 'Что такое CMT?'),
                $this->t(
                    '<p>Cut, Make, Trim: croim, cusem și finisăm după tiparele și materialele dumneavoastră. Este cea mai transparentă schemă — plătiți manopera, nu marja pe material.</p>',
                    '<p>Cut, Make, Trim: we cut, sew and finish to your patterns and materials. It is the most transparent arrangement — you pay for the work, not a markup on fabric.</p>',
                    '<p>Cut, Make, Trim: кроим, шьём и отделываем по вашим лекалам и из вашего материала. Самая прозрачная схема — вы платите за работу, а не за наценку на ткань.</p>',
                ),
            ],
            [
                FaqGroup::Services,
                $this->t(
                    'Cum controlați calitatea?',
                    'How do you control quality?',
                    'Как контролируется качество?',
                ),
                $this->t(
                    '<p>În trei puncte: la intrarea materialului, pe fiecare operație-cheie a liniei și la controlul final, prin care trec 100% din articole. Rezultatele intră în raportul de lot, pe care îl primiți împreună cu marfa.</p>',
                    '<p>At three points: fabric intake, every key operation on the line, and a final inspection that 100% of garments go through. The results go into the batch report you receive with the goods.</p>',
                    '<p>В трёх точках: на входном контроле материала, на каждой ключевой операции линии и на финальной проверке, через которую проходят 100% изделий. Результаты попадают в отчёт по партии, который вы получаете вместе с товаром.</p>',
                ),
            ],
            [
                FaqGroup::Services,
                $this->t(
                    'Lucrați cu branduri mici?',
                    'Do you work with small brands?',
                    'Работаете ли с небольшими брендами?',
                ),
                $this->t(
                    '<p>Da, dacă modelul este pregătit pentru producție de serie. Pentru branduri tinere începem cu un lot de probă și cu dezvoltarea tiparului — așa vedeți calitatea înainte de a comanda sezonul întreg.</p>',
                    '<p>Yes, provided the style is ready for series production. With young brands we start with a trial batch and pattern development — you see the quality before committing to a full season.</p>',
                    '<p>Да, если модель готова к серийному производству. С молодыми брендами начинаем с пробной партии и разработки лекал — так вы видите качество до того, как заказать весь сезон.</p>',
                ),
            ],
        ];
    }
}
