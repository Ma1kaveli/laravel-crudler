### Resource Layer

Resource в Crudler — это слой для преобразования моделей (или коллекций) в JSON-ответы, подходящие для API. Он расширяет `BaseResource` из Laravel, добавляя динамические поля (вычисляемые на основе модели), дополнительные ленивые поля (вычисляются только при необходимости) и пользовательские методы. Это позволяет гибко определять, какие данные возвращать, без жесткого кодирования в классе ресурса.

Поддерживаемые возможности:
- **Data**: Основные поля для `toArray()` (вычисляются сразу, поддерживают строки, closures, массивы для вложенности).
- **AdditionalData**: Ленивые поля (closures, вычисляются on-demand через `getAdditionalData()`).
- **Methods**: Динамические методы (closures), вызываемые через `call(string $name, ...$args)`.
- **Resource class**: Опциональный базовый класс ресурса (по умолчанию `CoreCrudlerResource`).

Resource не требует ручного маппинга — поля определяются через `CrudlerMapper` (для удобного chainable-маппинга). Конфигурация преобразуется в `CrudlerResourceGeneratorDTO` (внутренне) и `CrudlerResourceDTO`, который используется для создания `CrudlerResource`. Этот класс имеет методы `resource($model, array $additionalFields = [])` для одиночного ресурса и `collection($collection, array $additionalFields = [])` для коллекций (автоматически обрабатывает пагинацию).

#### Использование через Builder
Настройка происходит в статическом методе конфигурации `BASE_RESOURCE_CRUDLER` (определённом в интерфейсе `ICoreCrudler`). Используйте `ResourceBuilder::make()` для цепочной настройки. Builder поддерживает:
- `data(CrudlerMapper $mapper)`: Основные поля (строки как имена атрибутов модели, closures для вычислений, массивы для структур).
- `additional(CrudlerMapper $mapper)`: Ленивые дополнительные поля (обычно closures).
- `methods(CrudlerMapper $mapper)`: Пользовательские методы (closures, принимающие ресурс и аргументы).
- `resource(string $resource)`: Базовый класс ресурса (если нужно расширить).
- `fromConfig(array $config)`: Загружает конфигурацию из массива (опционально, для bulk-настройки). Массив вида `['data' => [...], 'additional_data' => [...], 'methods' => [...]]`.

После настройки вызовите `build()`, чтобы получить `CrudlerResourceDTO`. Если слой не нужен, верните `null`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use App\Modules\MyModule\Resources\CustomResource; // Пример кастомного класса

use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Mapper\CrudlerMapper;
use Crudler\Resources\Builders\ResourceBuilder;
use Crudler\Resources\DTO\CrudlerResourceDTO;

class MyModuleCrudler implements ICrudlerConfig {
    public static function BASE_RESOURCE_CRUDLER(...$args): ?CrudlerResourceDTO
    {
        return ResourceBuilder::make()
            ->data(
                CrudlerMapper::make()
                    ->field('id') // Строка: берёт $model->id
                    ->field('name')
                    ->field('full_name', fn($r) => $r->first_name . ' ' . $r->last_name) // Closure
                    ->field('settings', ['theme' => 'dark', 'notifications' => fn($r) => $r->prefs]) // Массив с вложенностью
            )
            ->additional(
                CrudlerMapper::make()
                    ->field('related_items', fn($r) => $r->items->pluck('id')) // Ленивое: вычисляется on-demand
                    ->field('computed_score', fn($r) => $r->views + $r->likes)
            )
            ->methods(
                CrudlerMapper::make()
                    ->field('greet', fn($r, $name) => "Hello, $name from " . $r->name) // Метод: $resource->call('greet', 'User')
            )
            ->resource(CustomResource::class) // Опционально: базовый класс
            // Опционально: загрузка из массива конфига
            // ->fromConfig(['data' => ['id', 'name'], 'additional_data' => ['extra' => fn($r) => 'value']])
            ->build();
    }
}
```

В этом примере:
- `data`: Основные поля, включая вычисляемые и вложенные.
- `additional`: Ленивые поля для оптимизации (не загружаются, если не запрошены).
- `methods`: Динамические методы для кастомной логики.
- `resource`: Если указан, используется как базовый класс вместо дефолтного.

#### Как использовать Resource в коде
1. Получите DTO из конфига: `$resourceDto = MyModuleCrudler::BASE_RESOURCE_CRUDLER();`.
2. Создайте CrudlerResource: `$crudlerResource = new CrudlerResource($resourceDto->generator());`.
3. Верните ресурс: `return $crudlerResource->resource($model, ['related_items']);` (второй аргумент — фильтр дополнительных полей, опционально).
   - Для коллекций: `return $crudlerResource->collection($paginator, ['computed_score']);` (автоматически извлекает модели из пагинации).
   - Вызов метода: `$result = $crudlerResource->call('greet', 'User');`.

**Пример в контроллере:**
```php
public function show($id): JsonResponse
{
    $data = $this->myModuleRepository->showFullById($id);

    $dataDto = MyModuleCrudler::RESOURCE_CRUDLER()->generator;

    return response()->json([
        'data' => (new CrudlerResource($dataDto))->resource(
            $data,
            ['related_items', 'computed_score']
        )
    ]);
}

// Или коллекция
public function index(Request $request): JsonResponse
{
    $data = $this->myModuleRepository->getPaginatedList(
        ListDTO::fromRequest($request)
    );

    return response()->json([
        'data' => new PaginatedCollection(
            $data,
            (new CrudlerResource(
                MyModuleCrudler::RESOURCE_CRUDLER()->generator
            ))->collection($data, [])
        )
    ]);
}
```

#### Примечания
- **CrudlerMapper**: Упрощает маппинг: `field(string $key, string|callable|array $value = null)`. Для массивов поддерживает рекурсию и автоключи (например, `['id', 'name']` → `['id' => $model->id, 'name' => $model->name]`).
- **Ленивость**: Additional поля — closures, вычисляются только если запрошены в `$additionalFields`.
- **Ошибки**: Если метод не найден в `call()`, бросается исключение.
- **Интеграция**: Используйте в контроллерах после Actions/Services для форматирования ответа. Поддержка вложенных ресурсов (например, через другие CrudlerResource).
- **fromConfig**: Автоматически парсит массив в мапперы.
