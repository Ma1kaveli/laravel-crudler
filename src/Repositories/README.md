### Repository Layer

Repository в Crudler — это слой для чтения данных из базы данных (без записи/изменений, для разделения concerns). Он расширяет `BaseRepository`, предоставляя готовые методы для типичных операций: получение списков, поиск по ID, пагинация, поиск, агрегаты и т.д. Это упрощает взаимодействие с Eloquent, добавляя поддержку soft deletes (`withTrashed`), кэширования, joins, scopes и фильтров. Repository фокусируется на чтении, оставляя запись/обновление для Service.

Поддерживаемые возможности:
- **Базовые запросы**: Получение всех, по ID, пагинированные списки (из BaseRepository).
- **Фильтры и поиск**: По колонкам, like, multiple conditions, subqueries (из BaseRepository).
- **Агрегаты**: Count, sum, groupBy, having (из BaseRepository).
- **Отношения**: With, withCount, join (из BaseRepository).
- **Soft deletes**: Методы с `withTrashed`, onlyTrashed, trashedCount (из BaseRepository).
- **Кэширование**: Cached get/paginated (из BaseRepository).
- **Scopes и raw**: Поддержка global scopes, raw where (из BaseRepository).
- **Crudler-специфично**: Проверка уникальности (`_isUnique`), показ одной записи с relations (`_showOnceById`).

Repository использует `CrudlerRepository` (расширение `CoreCrudlerRepository`), который добавляет методы `crudlerIsUnique` и `crudlerShowOnceById`, оборачивающие базовые с обработкой ошибок. Конфигурация преобразуется в `CrudlerRepositoryDTO`, который включает специфические DTO для уникальности (`RepositoryUniqueDTO`) и показа одной записи (`RepositoryShowOnceDTO`). Методы конфига позволяют настраивать разные аспекты:
- `FULL_REPOSITORY_CRUDLER`: Полная настройка с FormDTO (для динамических фильтров из запроса).
- `BASE_REPOSITORY_CRUDLER`: Базовая настройка, опционально с Unique/ShowOnce DTO.
- `BASE_REPOSITORY_UNIQUE_DTO`: DTO для проверки уникальности (поля, игнор ID, withTrashed).
- `BASE_REPOSITORY_SHOW_ONCE_DTO`: DTO для показа одной записи (relations, withTrashed, custom query).

#### Использование через Builder
Настройка происходит в статических методах конфигурации (определённых в интерфейсе `ICoreCrudler`). Используйте `RepositoryBuilder::make()` для цепочной настройки. Builder поддерживает:
- `unique(RepositoryUniqueDTO $dto)`: Настройка уникальности.
- `showOnceById(RepositoryShowOnceDTO $dto)`: Настройка показа одной записи.
- `uniqueBuilder(RepositoryUniqueBuilder $builder)`: Через sub-builder для уникальности.
- `showOnceByIdBuilder(RepositoryShowOnceBuilder $builder)`: Через sub-builder для show once.
- `fromConfig(FormDTO $formDTO, array $config)`: Через конфиг

После настройки вызовите `build()`, чтобы получить `CrudlerRepositoryDTO`. Если слой не нужен, верните `null`. Для специфических DTO (Unique/ShowOnce) используйте соответствующие builders: `RepositoryUniqueBuilder::make(FormDTO $dto)` или `RepositoryShowOnceBuilder::make(FormDTO $dto)`, с методами вроде `addUniqueItem`, `with`, `query` и `fromConfig`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use App\Modules\MyModule\Models\MyModel;

use Core\DTO\FormDTO;
use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Repositories\Builders\RepositoryBuilder;
use Crudler\Repositories\Builders\RepositoryShowOnceBuilder;
use Crudler\Repositories\Builders\RepositoryUniqueBuilder;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Repositories\DTO\RepositoryUniqueDTO;
use Crudler\Repositories\DTO\RepositoryShowOnceDTO;
use Crudler\Repositories\Interfaces\{
    IUniqueItemCallable,
    IUniqueFromConfigCallable
};

class MyModuleCrudler implements ICrudlerConfig {
    public static function FULL_REPOSITORY_CRUDLER(FormDTO $dto, ...$args): ?CrudlerRepositoryDTO
    {
        return RepositoryBuilder::make()
            ->uniqueBuilder(
                RepositoryUniqueBuilder::make($dto)
                    ->addUniqueItem(
                        field: 'name',
                        column: DB::raw('LOWER(name)'),
                        modifier: fn($v) => trim(strtolower($v)),
                        isOrWhere: false
                    )
                    ->addUniqueItem(
                        field: 'organizationId',
                        column: 'organization_id'
                    )
                    ->message('Чат с такими данными уже существует!')
            )
            ->showOnceByIdBuilder(
                RepositoryShowOnceBuilder::make($dto)
                    ->with(['user', 'role'])
                    ->withCount(['purshaces as purshaces_count'])
                    ->withTrashed()
                    ->message('Не найдена модель!')
                    ->query(
                        fn(Builder $q, FormDTO $dto) => $q->when($dto->go, fn($q) => $q->where('go', true))
                    )
            )
            ->build();
    }

    public static function BASE_REPOSITORY_CRUDLER(
        ?RepositoryUniqueDTO $uniqueDTO = null,
        ?RepositoryShowOnceDTO $showOnceDTO = null
    ): ?CrudlerRepositoryDTO {
        return CrudlerRepositoryDTO::start(
            uniqueDTO: $uniqueDTO,
            showOnceDTO: $showOnceDTO,
        );
    }

    public static function BASE_REPOSITORY_UNIQUE_DTO(FormDTO $formDTO): ?RepositoryUniqueDTO
    {
        return RepositoryUniqueBuilder::make($formDTO)
            ->addUniqueItem(field: 'email', column: 'email')
            ->closure(
                new class implements IUniqueItemCallable {
                    public function __invoke(FormDTO $dto) {
                        return [
                            'custom' => new RepositoryUniqueItemDTO('field', 'column')
                        ]
                    }
                }
            ) // Или closure для динамики
            ->build();
    }

    public static function BASE_REPOSITORY_SHOW_ONCE_DTO(FormDTO $formDTO): ?RepositoryShowOnceDTO
    {
        return RepositoryShowOnceBuilder::make($formDTO)
            ->with(['user'])
            ->closure(fn (FormDTO $dto) => new RepositoryShowOnceConfigDTO(with: ['dynamic'])) // Или closure
            ->build();
    }
}
```

В этом примере:
- `FULL_REPOSITORY_CRUDLER`: Полная настройка с sub-builders и динамикой из DTO.
- `BASE_REPOSITORY_CRUDLER`: Базовая, с fallback на другие методы если DTO null.
- `BASE_REPOSITORY_UNIQUE_DTO`: Для уникальности (поля с modifiers, orWhere).
- `BASE_REPOSITORY_SHOW_ONCE_DTO`: Для показа одной записи (relations, custom query).

#### Создание из конфига-массива

```php
$config = [
    "uniques" => new class implements IFromConfigCallable {
        public function __invoke(FormDTO $formDTO): array {
            return [
                'name' => [
                    'column' => DB::raw('LOWER(name)'),
                    'modifier' => fn($v) => trim(strtolower($v)),
                    'is_or_where' => false
                ],
                'organizationId' => 'organization_id',
            ];
        }
    },
    "unique_message" => 'Чат с такими данными уже существует!',
    "show_once_by_id" => function (FormDTO $dto) {
        return [
            "with" => ["user", "role"],
            "with_count" => ["purshaces as purshaces_count"],
            "with_trashed" => true,
            "message" => "Не найдена модель!",
            "query" => fn (Builder $query, FormDTO $dto) => $query->when(
                $dto->go, 
                fn ($q) => $q->where('go', true)
            ),
        ];
    }
];

$builder = RepositoryBuilder::make()
    ->uniqueBuilder(
        RepositoryUniqueBuilder::make($formDTO)
            ->fromConfig($config['uniques'])
            ->build()
    )->showOnceByIdBuilder(
        RepositoryShowOnceBuilder::make($onceDTO)
            ->fromConfig($config['show_once_by_id'])
            ->build()
    )->build();
/* OR */
$builder = RepositoryBuilder::make()->fromConfig($formDTO, $config)->build();
```

#### Как использовать Repository в коде
1. Получите DTO из конфига: `$repoDto = MyModuleCrudler::FULL_REPOSITORY_CRUDLER($dto);`.
2. Создайте Repository: `$repo = new CrudlerRepository('App\\MyModel');` (modelClass).
3. Вызовите методы: `$isUnique = $repo->crudlerIsUnique($repoDto);` или базовые из `BaseRepository` как `$repo->getAll(true);`.
   - Crudler-специфично:
     - `crudlerIsUnique(CrudlerRepositoryDTO $dto)`: Проверяет уникальность по config, throws если не unique.
     - `crudlerShowOnceById(CrudlerRepositoryDTO $dto)`: Получает модель по ID с relations, throws если не найдена.
   - Полный список из `BaseRepository` (примеры):
     - `getAll(bool $withTrashed = false)`: Все записи.
     - `findByIdOrFail(int $id, bool $withTrashed = false, string $notFoundMessage = 'Не найдено!')`: По ID с orFail.
     - `getPaginatedList(ListDTO $dto)`: Пагинированный список.
     - `searchLike(string $column, string $searchTerm)`: Поиск like.
     - `countBy(array $conditions)`: Count по условиям.
     - `cachedGet(string $cacheKey, int $ttl = 60)`: Кэшированный get.
     - `withCount(string $relation)`: С подсчётом отношения.
     - `onlyTrashed()`: Только soft-deleted.
     - И многие другие (join, groupBy, whereMultiple, rawWhere и т.д.).

**Пример в сервисе:**
```php
$repoDto = MyModuleCrudler::FULL_REPOSITORY_CRUDLER($formDTO);
$repo = new CrudlerRepository(MyModuleModel::class);

$isUnique = $repo->crudlerIsUnique($repoDto); // bool or throw
$model = $repo->crudlerShowOnceById($repoDto); // Model or throw

// Базовые функции из BaseRepository
$list = $repo->getPaginatedList(ListDTO::fromRequest($request));
```

#### Примечания
- **DTO интеграция**: `RepositoryUniqueDTO` для уникальности (с modifiers как fn($v) => strtolower($v), orWhere), `RepositoryShowOnceDTO` для show (with, query как fn($q, $dto) => $q->where(...)).
- **Ошибки**: Методы как `_isUnique` возвращают bool или throw LogicException если не поддерживается; crudler-обертки бросают custom errors.
- **Интеграция**: Используйте в Actions/Services для чтения перед мутациями. `fromConfig` в builders парсит массивы в DTO.
- **Гибкость**: Closures для динамической config (например, fn($dto) => [...] на основе FormDTO).
