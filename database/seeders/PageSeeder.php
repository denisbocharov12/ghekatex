<?php

namespace Database\Seeders;

use App\Enums\PageTemplate;
use App\Models\Page;
use Database\Seeders\Concerns\TranslatesSeedData;
use Illuminate\Database\Seeder;

/**
 * Системные страницы. Тексты — рабочая основа: юрист заказчика правит их
 * из админки, структура и обязательные разделы уже на месте.
 */
class PageSeeder extends Seeder
{
    use TranslatesSeedData;

    public function run(): void
    {
        foreach ($this->pages() as $slug => $attributes) {
            Page::query()->updateOrCreate(['slug' => $slug], $attributes);
        }
    }

    /** @return array<string, array<string, mixed>> */
    private function pages(): array
    {
        return [
            'about' => [
                'template' => PageTemplate::About->value,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 1,
                'title' => $this->t('Despre companie', 'About the company', 'О компании'),
                'subtitle' => $this->t(
                    'Croitorie industrială pentru branduri de damă din Europa',
                    'Industrial garment making for European womenswear brands',
                    'Промышленный пошив для европейских женских брендов',
                ),
                'body' => $this->t($this->aboutRo(), $this->aboutEn(), $this->aboutRu()),
            ],
            'privacy-policy' => [
                'template' => PageTemplate::Legal->value,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 90,
                'title' => $this->t('Politica de confidențialitate', 'Privacy Policy', 'Политика конфиденциальности'),
                'subtitle' => $this->t(
                    'Cum prelucrăm datele cu caracter personal',
                    'How we process personal data',
                    'Как мы обрабатываем персональные данные',
                ),
                'body' => $this->t($this->privacyRo(), $this->privacyEn(), $this->privacyRu()),
            ],
            'cookie-policy' => [
                'template' => PageTemplate::Legal->value,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 91,
                'title' => $this->t('Politica de cookie-uri', 'Cookie Policy', 'Политика использования cookie'),
                'subtitle' => $this->t(
                    'Ce fișiere folosim și cum le puteți controla',
                    'Which files we use and how you can control them',
                    'Какие файлы мы используем и как ими управлять',
                ),
                'body' => $this->t($this->cookieRo(), $this->cookieEn(), $this->cookieRu()),
            ],
            'terms-of-use' => [
                'template' => PageTemplate::Legal->value,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 92,
                'title' => $this->t('Termeni de utilizare', 'Terms of Use', 'Условия использования'),
                'subtitle' => $this->t(
                    'Regulile de utilizare a site-ului',
                    'Rules for using this website',
                    'Правила пользования сайтом',
                ),
                'body' => $this->t($this->termsRo(), $this->termsEn(), $this->termsRu()),
            ],
        ];
    }

    private function aboutRu(): string
    {
        return <<<'HTML'
<h2>Кто мы</h2>
<p>GHEKATEX — производственная компания из Республики Молдова, которая шьёт женскую одежду для европейских брендов. Мы работаем по моделям заказчика и по собственным разработкам, беря на себя весь цикл: от конструкции и опытного образца до упакованной партии, готовой к отгрузке.</p>

<h2>Миссия</h2>
<p>Дать европейским маркам производственного партнёра, которому не нужно объяснять дважды: предсказуемые сроки, повторяемое качество строчки и прозрачная коммуникация на языке заказчика.</p>

<h2>Почему нас выбирают</h2>
<ul>
  <li>Близость к рынкам ЕС — отгрузка в Италию, Польшу и Испанию за несколько дней.</li>
  <li>Полный цикл на одной площадке: конструирование, раскрой, пошив, влажно-тепловая обработка, контроль, упаковка.</li>
  <li>Опытные технологи и закройщики, знакомые с требованиями премиального сегмента.</li>
  <li>Работа с широким спектром материалов: от трикотажа и вискозы до плотных костюмных тканей.</li>
</ul>

<h2>Как мы работаем</h2>
<p>Первым шагом обсуждаем задачу и получаем техническое описание модели. Затем изготавливаем опытный образец и согласовываем посадку. После утверждения запускаем партию, ведём пооперационный контроль и передаём готовую продукцию логистике.</p>
HTML;
    }

    private function aboutRo(): string
    {
        return <<<'HTML'
<h2>Cine suntem</h2>
<p>GHEKATEX este o companie de producție din Republica Moldova care coase îmbrăcăminte pentru femei destinată brandurilor europene. Lucrăm atât după modelele clientului, cât și după dezvoltări proprii, asumându-ne întregul ciclu: de la construcție și mostră până la lotul ambalat, pregătit pentru expediere.</p>

<h2>Misiune</h2>
<p>Să oferim mărcilor europene un partener de producție căruia nu trebuie să-i explici de două ori: termene previzibile, calitate constantă a cusăturii și comunicare transparentă în limba clientului.</p>

<h2>De ce suntem aleși</h2>
<ul>
  <li>Proximitatea de piețele UE — livrare în Italia, Polonia și Spania în câteva zile.</li>
  <li>Ciclu complet într-un singur loc: construcție, croire, cusut, călcare, control, ambalare.</li>
  <li>Tehnologi și croitori cu experiență, familiarizați cu cerințele segmentului premium.</li>
  <li>Lucrăm cu o gamă largă de materiale: de la tricot și vâscoză până la țesături groase pentru costume.</li>
</ul>

<h2>Cum lucrăm</h2>
<p>Primul pas este discuția despre proiect și primirea fișei tehnice a modelului. Urmează mostra și confirmarea potrivirii. După aprobare lansăm lotul, aplicăm controlul pe operații și predăm marfa finită către logistică.</p>
HTML;
    }

    private function aboutEn(): string
    {
        return <<<'HTML'
<h2>Who we are</h2>
<p>GHEKATEX is a manufacturing company based in the Republic of Moldova that produces womenswear for European brands. We work both to customer specifications and to our own designs, covering the full cycle: from pattern making and sample to a packed batch ready for shipment.</p>

<h2>Mission</h2>
<p>To give European labels a manufacturing partner they never need to brief twice: predictable lead times, repeatable stitch quality and transparent communication in the customer's own language.</p>

<h2>Why brands choose us</h2>
<ul>
  <li>Proximity to EU markets — delivery to Italy, Poland and Spain within days.</li>
  <li>A complete cycle on one site: pattern making, cutting, sewing, pressing, inspection, packing.</li>
  <li>Experienced technologists and cutters familiar with premium-segment requirements.</li>
  <li>A broad material range: from jersey and viscose to heavy suiting fabrics.</li>
</ul>

<h2>How we work</h2>
<p>We start by discussing the project and receiving the technical sheet for the model. Next comes the sample and fit approval. Once approved, we launch the batch, run operation-level inspection and hand the finished goods to logistics.</p>
HTML;
    }

    private function privacyRu(): string
    {
        return <<<'HTML'
<h2>1. Кто обрабатывает данные</h2>
<p>Оператором персональных данных является Ghekatex Group SRL, Республика Молдова. Вопросы об обработке данных направляйте на privacy@ghekatex.md.</p>

<h2>2. Какие данные мы собираем</h2>
<ul>
  <li><strong>Данные форм:</strong> имя, e-mail, телефон, компания, страна и текст обращения — то, что вы указали сами.</li>
  <li><strong>Технические данные:</strong> хешированный IP-адрес, тип браузера, язык, страницы визита.</li>
  <li><strong>Данные аналитики:</strong> обезличенная статистика посещений — только при вашем согласии.</li>
</ul>

<h2>3. Зачем мы их используем</h2>
<p>Чтобы ответить на ваше обращение, подготовить коммерческое предложение, вести деловую переписку, улучшать сайт и выполнять требования закона. Мы не продаём и не передаём ваши данные третьим лицам в рекламных целях.</p>

<h2>4. Правовые основания</h2>
<p>Обработка ведётся на основании вашего согласия (ст. 6(1)(a) GDPR), необходимости по преддоговорным отношениям (ст. 6(1)(b)) и законного интереса компании в развитии деятельности (ст. 6(1)(f)).</p>

<h2>5. Сроки хранения</h2>
<p>Обращения хранятся до 3 лет с момента последнего контакта, журнал согласий на cookie — 180 дней, бухгалтерские документы — в сроки, установленные законодательством.</p>

<h2>6. Ваши права</h2>
<p>Вы вправе запросить доступ к своим данным, их исправление или удаление, ограничить обработку, отозвать согласие и подать жалобу в надзорный орган. Запрос направляйте на privacy@ghekatex.md — ответим в течение 30 дней.</p>

<h2>7. Передача данных</h2>
<p>Данные могут обрабатываться нашими подрядчиками — хостинг-провайдером, почтовым сервисом и сервисами веб-аналитики. Со всеми заключены соглашения об обработке данных.</p>

<h2>8. Безопасность</h2>
<p>Сайт работает по HTTPS, доступ к административной панели ограничен ролями и паролями, IP-адреса в журналах хранятся только в виде хеша.</p>
HTML;
    }

    private function privacyRo(): string
    {
        return <<<'HTML'
<h2>1. Cine prelucrează datele</h2>
<p>Operatorul de date cu caracter personal este Ghekatex Group SRL, Republica Moldova. Întrebările privind prelucrarea datelor pot fi trimise la privacy@ghekatex.md.</p>

<h2>2. Ce date colectăm</h2>
<ul>
  <li><strong>Date din formulare:</strong> nume, e-mail, telefon, companie, țară și textul mesajului — informațiile pe care le furnizați dumneavoastră.</li>
  <li><strong>Date tehnice:</strong> adresa IP sub formă de hash, tipul browserului, limba, paginile vizitate.</li>
  <li><strong>Date de analiză:</strong> statistici anonimizate de trafic — doar cu acordul dumneavoastră.</li>
</ul>

<h2>3. În ce scop</h2>
<p>Pentru a răspunde solicitării, a pregăti o ofertă, a purta corespondență de afaceri, a îmbunătăți site-ul și a respecta obligațiile legale. Nu vindem și nu transmitem datele către terți în scopuri publicitare.</p>

<h2>4. Temeiul juridic</h2>
<p>Prelucrarea se bazează pe consimțământ (art. 6(1)(a) GDPR), pe necesitatea măsurilor precontractuale (art. 6(1)(b)) și pe interesul legitim al companiei (art. 6(1)(f)).</p>

<h2>5. Perioada de păstrare</h2>
<p>Solicitările se păstrează până la 3 ani de la ultimul contact, registrul consimțămintelor pentru cookie-uri — 180 de zile, documentele contabile — conform termenelor legale.</p>

<h2>6. Drepturile dumneavoastră</h2>
<p>Aveți dreptul de acces, rectificare, ștergere, restricționare a prelucrării, retragere a consimțământului și depunere a unei plângeri la autoritatea de supraveghere. Scrieți la privacy@ghekatex.md — răspundem în 30 de zile.</p>

<h2>7. Transferul datelor</h2>
<p>Datele pot fi prelucrate de furnizorii noștri: găzduire web, serviciu de e-mail și servicii de analiză web. Cu toți avem încheiate acorduri de prelucrare a datelor.</p>

<h2>8. Securitate</h2>
<p>Site-ul funcționează prin HTTPS, accesul la panoul de administrare este limitat prin roluri și parole, iar adresele IP sunt stocate doar sub formă de hash.</p>
HTML;
    }

    private function privacyEn(): string
    {
        return <<<'HTML'
<h2>1. Who processes your data</h2>
<p>The data controller is Ghekatex Group SRL, Republic of Moldova. Questions about data processing can be sent to privacy@ghekatex.md.</p>

<h2>2. What we collect</h2>
<ul>
  <li><strong>Form data:</strong> name, e-mail, phone, company, country and the message text — whatever you provide yourself.</li>
  <li><strong>Technical data:</strong> hashed IP address, browser type, language, pages visited.</li>
  <li><strong>Analytics data:</strong> anonymised traffic statistics — only with your consent.</li>
</ul>

<h2>3. Why we use it</h2>
<p>To answer your enquiry, prepare a quotation, conduct business correspondence, improve the website and meet legal obligations. We never sell or share your data with third parties for advertising purposes.</p>

<h2>4. Legal basis</h2>
<p>Processing relies on your consent (Art. 6(1)(a) GDPR), on pre-contractual necessity (Art. 6(1)(b)) and on the company's legitimate interest (Art. 6(1)(f)).</p>

<h2>5. Retention</h2>
<p>Enquiries are kept for up to 3 years from the last contact, the cookie-consent log for 180 days, accounting records for the periods required by law.</p>

<h2>6. Your rights</h2>
<p>You may request access to your data, its correction or erasure, restrict processing, withdraw consent and lodge a complaint with a supervisory authority. Write to privacy@ghekatex.md — we respond within 30 days.</p>

<h2>7. Data sharing</h2>
<p>Your data may be processed by our providers: the hosting company, the e-mail service and web-analytics services. Data processing agreements are in place with all of them.</p>

<h2>8. Security</h2>
<p>The site runs over HTTPS, access to the admin panel is restricted by roles and passwords, and IP addresses are stored only as hashes.</p>
HTML;
    }

    private function cookieRu(): string
    {
        return <<<'HTML'
<h2>Что такое cookie</h2>
<p>Cookie — небольшие текстовые файлы, которые сайт сохраняет в вашем браузере. Они позволяют запомнить выбранный язык, состояние согласия и собрать обезличенную статистику посещений.</p>

<h2>Категории файлов</h2>
<h3>Необходимые</h3>
<p>Работают всегда и не требуют согласия: сессия, защита форм от подделки запросов (CSRF), выбранный язык интерфейса и сохранённое решение по cookie.</p>

<h3>Аналитические</h3>
<p>Google Analytics 4 и Яндекс.Метрика. Подключаются только после вашего согласия и помогают понять, какие разделы сайта востребованы. Отключение этой категории не влияет на работу сайта.</p>

<h3>Маркетинговые</h3>
<p>Используются для оценки эффективности рекламных кампаний и ремаркетинга. Подключаются только после согласия.</p>

<h2>Как управлять</h2>
<p>Настроить категории можно в баннере при первом визите или в любой момент через ссылку «Настройки cookie» в подвале сайта. Решение хранится 180 дней. Также вы можете удалить файлы средствами браузера — обычно это раздел «Конфиденциальность и безопасность».</p>

<h2>Журнал согласий</h2>
<p>Мы сохраняем факт согласия: анонимный идентификатор, выбранные категории, версию политики и дату. Это требуется GDPR как доказательство согласия и не позволяет идентифицировать вас лично.</p>
HTML;
    }

    private function cookieRo(): string
    {
        return <<<'HTML'
<h2>Ce sunt cookie-urile</h2>
<p>Cookie-urile sunt fișiere text mici pe care site-ul le salvează în browserul dumneavoastră. Ele permit reținerea limbii alese, a stării consimțământului și colectarea de statistici anonimizate.</p>

<h2>Categorii de fișiere</h2>
<h3>Necesare</h3>
<p>Funcționează permanent și nu necesită consimțământ: sesiunea, protecția formularelor împotriva CSRF, limba interfeței și decizia salvată privind cookie-urile.</p>

<h3>De analiză</h3>
<p>Google Analytics 4 și Yandex.Metrica. Se activează doar după consimțământ și ne ajută să înțelegem ce secțiuni sunt căutate. Dezactivarea lor nu afectează funcționarea site-ului.</p>

<h3>De marketing</h3>
<p>Servesc la evaluarea eficienței campaniilor publicitare și la remarketing. Se activează doar după consimțământ.</p>

<h2>Cum le gestionați</h2>
<p>Puteți configura categoriile în bannerul afișat la prima vizită sau oricând prin linkul „Setări cookie” din subsolul site-ului. Decizia se păstrează 180 de zile. Puteți șterge fișierele și din setările browserului, de obicei din secțiunea „Confidențialitate și securitate”.</p>

<h2>Registrul consimțămintelor</h2>
<p>Păstrăm faptul consimțământului: un identificator anonim, categoriile alese, versiunea politicii și data. Este cerut de GDPR ca dovadă a consimțământului și nu permite identificarea dumneavoastră.</p>
HTML;
    }

    private function cookieEn(): string
    {
        return <<<'HTML'
<h2>What cookies are</h2>
<p>Cookies are small text files that the website stores in your browser. They let us remember your chosen language, your consent state and collect anonymised visit statistics.</p>

<h2>Categories</h2>
<h3>Necessary</h3>
<p>Always active and exempt from consent: the session, CSRF protection for forms, the interface language and your saved cookie decision.</p>

<h3>Analytics</h3>
<p>Google Analytics 4 and Yandex.Metrica. Loaded only after your consent; they help us understand which sections are in demand. Turning this category off does not affect how the site works.</p>

<h3>Marketing</h3>
<p>Used to measure advertising performance and for remarketing. Loaded only after consent.</p>

<h2>How to manage them</h2>
<p>You can configure the categories in the banner shown on your first visit, or at any time via the “Cookie settings” link in the site footer. Your decision is stored for 180 days. You can also clear the files in your browser, usually under “Privacy and security”.</p>

<h2>Consent log</h2>
<p>We record the fact of consent: an anonymous identifier, the chosen categories, the policy version and the date. GDPR requires this as proof of consent, and it does not allow us to identify you personally.</p>
HTML;
    }

    private function termsRu(): string
    {
        return <<<'HTML'
<h2>1. Общие положения</h2>
<p>Сайт ghekatex.md принадлежит Ghekatex Group SRL. Пользуясь сайтом, вы соглашаетесь с настоящими условиями.</p>

<h2>2. Содержание сайта</h2>
<p>Материалы носят информационный характер и не являются публичной офертой. Технические характеристики изделий, сроки и условия производства подтверждаются в переписке и договоре.</p>

<h2>3. Интеллектуальная собственность</h2>
<p>Логотип, фирменный стиль, тексты и фотографии принадлежат компании или используются с разрешения правообладателей. Копирование материалов без письменного согласия не допускается.</p>

<h2>4. Обращения через формы</h2>
<p>Отправляя форму, вы подтверждаете достоверность указанных данных и согласие на их обработку в соответствии с Политикой конфиденциальности.</p>

<h2>5. Ссылки на другие сайты</h2>
<p>Мы не отвечаем за содержание сторонних ресурсов, на которые ведут ссылки с нашего сайта.</p>

<h2>6. Изменения</h2>
<p>Условия могут обновляться. Актуальная редакция всегда доступна на этой странице.</p>
HTML;
    }

    private function termsRo(): string
    {
        return <<<'HTML'
<h2>1. Dispoziții generale</h2>
<p>Site-ul ghekatex.md aparține companiei Ghekatex Group SRL. Prin utilizarea site-ului acceptați acești termeni.</p>

<h2>2. Conținutul site-ului</h2>
<p>Materialele au caracter informativ și nu constituie o ofertă publică. Caracteristicile tehnice ale produselor, termenele și condițiile de producție se confirmă prin corespondență și contract.</p>

<h2>3. Proprietate intelectuală</h2>
<p>Logo-ul, identitatea vizuală, textele și fotografiile aparțin companiei sau sunt folosite cu acordul deținătorilor de drepturi. Copierea materialelor fără acord scris nu este permisă.</p>

<h2>4. Solicitări prin formulare</h2>
<p>Prin trimiterea formularului confirmați corectitudinea datelor și acordul pentru prelucrarea lor conform Politicii de confidențialitate.</p>

<h2>5. Linkuri către alte site-uri</h2>
<p>Nu răspundem pentru conținutul resurselor terțe către care duc linkurile de pe site-ul nostru.</p>

<h2>6. Modificări</h2>
<p>Termenii pot fi actualizați. Versiunea în vigoare este mereu disponibilă pe această pagină.</p>
HTML;
    }

    private function termsEn(): string
    {
        return <<<'HTML'
<h2>1. General provisions</h2>
<p>The ghekatex.md website belongs to Ghekatex Group SRL. By using the site you accept these terms.</p>

<h2>2. Website content</h2>
<p>The materials are informational and do not constitute a public offer. Product specifications, lead times and production conditions are confirmed in correspondence and in the contract.</p>

<h2>3. Intellectual property</h2>
<p>The logo, visual identity, texts and photographs belong to the company or are used with the rights holders' permission. Copying materials without written consent is not permitted.</p>

<h2>4. Enquiries via forms</h2>
<p>By submitting a form you confirm that the data provided is accurate and consent to its processing in line with the Privacy Policy.</p>

<h2>5. Links to other sites</h2>
<p>We are not responsible for the content of third-party resources linked from our website.</p>

<h2>6. Changes</h2>
<p>These terms may be updated. The current version is always available on this page.</p>
HTML;
    }
}
