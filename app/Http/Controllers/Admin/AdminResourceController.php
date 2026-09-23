<?php

namespace App\Http\Controllers\Admin;

use App\Data\Mappers\ResourceDataMapper;
use App\Data\ResourceData;
use App\Data\Schemas\FieldSchema;
use App\Http\Controllers\Controller;
use App\Managers\BaseContentManager;
use App\Models\Concerns\HasSeo;
use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Каркас раздела админки.
 *
 * Разделы отличаются набором полей, а не поведением: список, форма,
 * сохранение, порядок, медиа и переключение активности везде одинаковы.
 * Наследник описывает раздел, а не повторяет эти шаги.
 */
abstract class AdminResourceController extends Controller
{
    public function __construct(
        protected readonly BaseContentManager $manager,
        protected readonly CrudRepositoryInterface $repository,
    ) {}

    /** Папка Vue-страниц раздела, например `Products`. */
    abstract protected function pagePrefix(): string;

    /** Префикс имён маршрутов без `admin.`, например `products`. */
    abstract protected function routePrefix(): string;

    abstract protected function schema(): FieldSchema;

    /**
     * Класс запроса с правилами валидации раздела.
     *
     * Резолвим его из контейнера, а не через тип аргумента: PHP не позволяет
     * сузить тип параметра в наследнике, а валидация при этом всё равно
     * отрабатывает — FormRequest проверяет себя при создании.
     *
     * @return class-string<FormRequest>
     */
    abstract protected function requestClass(): string;

    /** Колонки и подписи для таблицы списка. @return array<string, mixed> */
    abstract protected function indexProps(): array;

    /** Справочники для формы: категории, иконки, языки. @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [];
    }

    /** Дополнительные поля записи для формы редактирования. @return array<string, mixed> */
    protected function editProps(Model $model): array
    {
        return [];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->repository->paginate((int) $request->integer('per_page', 20) ?: 20);

        // Список показывает миниатюры, поэтому дополняем строки ссылками на медиа
        $paginator->getCollection()->transform(function (Model $model) {
            $row = $model->toArray();

            foreach ($this->schema()->singleMedia as $collection) {
                $row[$collection.'_url'] = method_exists($model, 'getFirstMediaUrl')
                    ? ($model->getFirstMediaUrl($collection, 'thumb') ?: $model->getFirstMediaUrl($collection) ?: null)
                    : null;
            }

            return $row;
        });

        return Inertia::render($this->pagePrefix().'/Index', array_merge([
            'items' => $paginator,
            'filters' => $request->only(['filter', 'sort', 'page']),
        ], $this->indexProps()));
    }

    public function create(): Response
    {
        return Inertia::render($this->pagePrefix().'/Form', $this->sharedFormProps());
    }

    public function store(): RedirectResponse
    {
        $data = ResourceDataMapper::fromRequest($this->validated(), $this->schema());

        $model = $this->manager->store($data->attributes, $data->singles, $data->gallery);

        $this->afterSave($model, $data);

        // Галерея и порядок доступны только на экране редактирования
        return redirect()
            ->route('admin.'.$this->routePrefix().'.edit', $model)
            ->with('success', __('Запись создана'));
    }

    public function edit(int $item): Response
    {
        $model = $this->repository->find($item);

        return Inertia::render($this->pagePrefix().'/Form', array_merge(
            $this->sharedFormProps(),
            ['item' => $this->presentForForm($model)],
        ));
    }

    public function update(int $item): RedirectResponse
    {
        $model = $this->repository->find($item);

        $data = ResourceDataMapper::fromRequest($this->validated(), $this->schema());

        $model = $this->manager->modify($model, $data->attributes, $data->singles, $data->gallery);

        $this->afterSave($model, $data);

        return back()->with('success', __('Изменения сохранены'));
    }

    public function destroy(int $item): RedirectResponse
    {
        $model = $this->repository->find($item);

        $this->manager->remove($model);

        return redirect()
            ->route('admin.'.$this->routePrefix().'.index')
            ->with('success', __('Запись удалена'));
    }

    public function toggle(int $item): RedirectResponse
    {
        $model = $this->repository->find($item);

        $this->manager->toggleActive($model);

        return back();
    }

    public function reorder(Request $request): RedirectResponse
    {
        $rows = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.id' => ['required', 'integer'],
            'rows.*.sort_order' => ['required', 'integer', 'min:0'],
        ])['rows'];

        $this->manager->reorder($rows);

        return back();
    }

    public function destroyMedia(int $item, int $media): RedirectResponse
    {
        $model = $this->repository->find($item);

        $this->manager->deleteMedia($model, $media);

        return back()->with('success', __('Файл удалён'));
    }

    public function reorderMedia(Request $request, int $item): RedirectResponse
    {
        $model = $this->repository->find($item);

        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ])['ids'];

        $this->manager->reorderMedia($model, $ids);

        return back();
    }

    /**
     * Запись в форме: переводы полным набором локалей, медиа ссылками.
     *
     * @return array<string, mixed>
     */
    protected function presentForForm(Model $model): array
    {
        $schema = $this->schema();
        $data = $model->toArray();

        foreach ($schema->singleMedia as $collection) {
            $data[$collection.'_url'] = method_exists($model, 'getFirstMediaUrl')
                ? ($model->getFirstMediaUrl($collection) ?: null)
                : null;
        }

        $data['gallery'] = $this->manager->galleryPayload($model);

        foreach ($schema->relations as $relation) {
            $data[$relation] = $model->{$relation}()->pluck('id')->all();
        }

        if (in_array(HasSeo::class, class_uses_recursive($model), true)) {
            $seo = $model->seo()->first();
            $data['seo'] = $seo === null ? null : $seo->toArray();
        }

        return array_merge($data, $this->editProps($model));
    }

    /** Провалидированный запрос раздела. */
    protected function validated(): FormRequest
    {
        return app($this->requestClass());
    }

    /** @return array<string, mixed> */
    protected function sharedFormProps(): array
    {
        return array_merge([
            'localeCodes' => config('ghekatex.locales.available'),
            'localeLabels' => config('ghekatex.locales.labels'),
            'defaultLocale' => config('ghekatex.locales.default'),
        ], $this->formOptions());
    }

    /** Сохранение связей и SEO — после основной записи. */
    protected function afterSave(Model $model, ResourceData $data): void
    {
        foreach ($data->relations as $relation => $ids) {
            $model->{$relation}()->sync($ids);
        }

        if ($data->seo !== [] && in_array(HasSeo::class, class_uses_recursive($model), true)) {
            $model->syncSeo($data->seo);
        }
    }
}
