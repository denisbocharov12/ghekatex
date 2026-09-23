<?php

namespace Database\Seeders;

use App\Enums\SettingGroup;
use App\Enums\SettingType;
use App\Models\Setting;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

/**
 * Реестр настроек. Ключи создаются здесь и не заводятся из админки:
 * витрина обращается к ним по имени, и недостающий ключ ломал бы страницу.
 */
class SettingSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        $order = 0;

        foreach ($this->definitions() as [$key, $group, $type, $translatable, $label, $value]) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => $group->value,
                    'type' => $type->value,
                    'is_translatable' => $translatable,
                    'label' => $label,
                    'sort_order' => $order++,
                    // Существующие значения не затираем: сидер можно гонять на рабочей базе
                    'value' => Setting::query()->where('key', $key)->value('value')
                        ?? ($translatable ? $value : ['value' => $value]),
                ],
            );
        }
    }

    /** @return array<int, array{0: string, 1: SettingGroup, 2: SettingType, 3: bool, 4: string, 5: mixed}> */
    private function definitions(): array
    {
        return [
            // Общие
            ['company_name', SettingGroup::General, SettingType::String, false, 'Название компании', 'GHEKATEX'],
            ['company_legal_name', SettingGroup::General, SettingType::String, false, 'Юридическое лицо', 'Ghekatex Group SRL'],
            ['company_tagline', SettingGroup::General, SettingType::String, true, 'Слоган', $this->t(
                'Producție de îmbrăcăminte pentru femei pentru brandurile europene',
                'Womenswear manufacturing for European brands',
                'Производство женской одежды для европейских брендов',
            )],
            ['company_short_about', SettingGroup::General, SettingType::Text, true, 'Короткое описание в футере', $this->t(
                'Fabrică din Republica Moldova care produce colecții de damă pentru branduri din Italia, Polonia și Spania.',
                'A Moldovan factory producing womenswear collections for brands in Italy, Poland and Spain.',
                'Фабрика в Молдове, выпускающая женские коллекции для брендов Италии, Польши и Испании.',
            )],

            // Контакты
            ['contact_phone', SettingGroup::Contacts, SettingType::String, false, 'Основной телефон', '+373 22 000 000'],
            ['contact_phone_extra', SettingGroup::Contacts, SettingType::String, false, 'Дополнительный телефон', '+373 69 000 000'],
            ['contact_email', SettingGroup::Contacts, SettingType::String, false, 'E-mail для заявок', 'office@ghekatex.md'],
            ['contact_email_sales', SettingGroup::Contacts, SettingType::String, false, 'E-mail отдела продаж', 'sales@ghekatex.md'],
            ['contact_address', SettingGroup::Contacts, SettingType::String, true, 'Адрес в футере', $this->t(
                'Chișinău, Republica Moldova',
                'Chisinau, Republic of Moldova',
                'Кишинёв, Республика Молдова',
            )],
            ['contact_hours', SettingGroup::Contacts, SettingType::String, true, 'Часы работы', $this->t(
                'Luni – Vineri, 08:00 – 17:00',
                'Monday – Friday, 08:00 – 17:00',
                'Пн – Пт, 08:00 – 17:00',
            )],

            // Социальные сети
            ['social_facebook', SettingGroup::Social, SettingType::String, false, 'Facebook', 'https://www.facebook.com/ghekatex'],
            ['social_instagram', SettingGroup::Social, SettingType::String, false, 'Instagram', 'https://www.instagram.com/ghekatex'],
            ['social_linkedin', SettingGroup::Social, SettingType::String, false, 'LinkedIn', 'https://www.linkedin.com/company/ghekatex'],

            // Аналитика
            ['analytics_ga4_id', SettingGroup::Analytics, SettingType::String, false, 'Google Analytics 4 — Measurement ID', ''],
            ['analytics_metrika_id', SettingGroup::Analytics, SettingType::String, false, 'Яндекс.Метрика — номер счётчика', ''],
            ['analytics_gtm_id', SettingGroup::Analytics, SettingType::String, false, 'Google Tag Manager — ID контейнера (GTM-XXXXXXX)', ''],

            // SEO по умолчанию
            ['seo_title_suffix', SettingGroup::Seo, SettingType::String, false, 'Суффикс заголовков', 'GHEKATEX'],
            ['seo_home_title', SettingGroup::Seo, SettingType::String, true, 'Title главной', $this->t(
                'Producător de îmbrăcăminte pentru femei în Moldova',
                'Womenswear manufacturer in Moldova',
                'Производитель женской одежды в Молдове',
            )],
            ['seo_home_description', SettingGroup::Seo, SettingType::Text, true, 'Description главной', $this->t(
                'GHEKATEX produce colecții de damă la comandă pentru branduri din Italia, Polonia, Spania și alte țări europene: dezvoltare de modele, croire, cusut, control al calității.',
                'GHEKATEX manufactures made-to-order womenswear collections for brands in Italy, Poland, Spain and other European markets: pattern development, cutting, sewing, quality control.',
                'GHEKATEX производит женские коллекции под заказ для брендов Италии, Польши, Испании и других стран Европы: разработка моделей, раскрой, пошив, контроль качества.',
            )],
            ['seo_default_description', SettingGroup::Seo, SettingType::Text, true, 'Description по умолчанию', $this->t(
                'GHEKATEX — producător de îmbrăcăminte pentru femei din Republica Moldova.',
                'GHEKATEX — womenswear manufacturer based in the Republic of Moldova.',
                'GHEKATEX — производитель женской одежды из Республики Молдова.',
            )],
            ['seo_default_og_image', SettingGroup::Seo, SettingType::Image, false, 'Картинка для соцсетей', ''],

            // Главная страница
            ['home_intro_eyebrow', SettingGroup::Home, SettingType::String, true, 'Надзаголовок блока «О компании»', $this->t(
                'Despre GHEKATEX',
                'About GHEKATEX',
                'О GHEKATEX',
            )],
            ['home_intro_title', SettingGroup::Home, SettingType::String, true, 'Заголовок блока «О компании»', $this->t(
                'Partener de producție pentru branduri europene',
                'A manufacturing partner for European brands',
                'Производственный партнёр европейских брендов',
            )],
            ['home_intro_text', SettingGroup::Home, SettingType::Html, true, 'Текст блока «О компании»', $this->t(
                '<p>Lucrăm cu branduri de damă din Italia, Polonia și Spania: de la schiță și tipar până la colecția ambalată și livrată. Fabrica noastră combină experiența croitorilor cu echipamente moderne de croire și control al calității.</p>',
                '<p>We work with womenswear brands from Italy, Poland and Spain: from sketch and pattern to the packed, delivered collection. Our factory combines the craft of experienced tailors with modern cutting and quality-control equipment.</p>',
                '<p>Мы работаем с женскими брендами Италии, Польши и Испании: от эскиза и лекал до упакованной и отгруженной коллекции. Производство сочетает опыт закройщиков и швей с современным оборудованием раскроя и контроля качества.</p>',
            )],
            ['home_video_url', SettingGroup::Home, SettingType::String, false, 'Ссылка на видео о производстве', ''],
            ['home_video_title', SettingGroup::Home, SettingType::String, true, 'Подпись к видео', $this->t(
                'O zi la fabrica GHEKATEX',
                'A day at the GHEKATEX factory',
                'Один день на производстве GHEKATEX',
            )],

            // Юридические данные
            ['legal_registration', SettingGroup::Legal, SettingType::String, false, 'IDNO / регистрационный номер', ''],
            ['legal_vat', SettingGroup::Legal, SettingType::String, false, 'Код плательщика НДС', ''],
            ['legal_data_officer_email', SettingGroup::Legal, SettingType::String, false, 'E-mail по вопросам персональных данных', 'privacy@ghekatex.md'],
        ];
    }
}
