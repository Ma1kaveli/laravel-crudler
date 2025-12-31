### Action Layer

Action в Crudler — это слой для выполнения бизнес-действий (CRUD-операций и связанных задач), интегрирующий Service (для мутаций), Repository (для чтения), Policies (для авторизации) и другие компоненты (e.g. events, notifications). Он предоставляет унифицированный интерфейс для действий вроде show (просмотр), create, update, delete, restore, с возможностью кастомной логики (closures для pre/post hooks). Action отделяет исполнение от контроллеров, позволяя повторно использовать логику в разных контекстах (API, jobs, commands).

Поддерживаемые возможности:
- **Действия**: Show (чтение одной записи), create/update (с маппингом данных), delete/restore (с опциями soft/force).
- **Hooks**: Pre/post closures для каждого действия (e.g. валидация, logging, dispatch events).
- **Интеграция**: Автоматический вызов Service/Repository/Policy на основе config.
- **Опции**: Через `ExecutionOptionsDTO` для транзакций/валидации (как в Service).
- **Crudler-специфично**: Динамическая настройка через DTO для каждого действия (ActionShowDTO, ActionCreateDTO и т.д.), с маппингом/closures для логики.

Action не дублирует Service/Repository, а оркестрирует их (e.g. check policy → read from repo → mutate via service → post-hook). Конфигурация преобразуется в `CrudlerActionDTO`, который включает специфические DTO для действий (`ActionShowDTO`, `ActionCreateDTO` и т.д.). Эти DTO определяют hooks/маппинг с помощью closures или `CrudlerMapper`. Методы конфига позволяют настраивать разные аспекты:
- `FULL_ACTION_CRUDLER`: Полная настройка с FormDTO/OnceDTO (для динамики) и ...$args (дополнительные параметры).
- `BASE_ACTION_CRUDLER`: Базовая, с опциональными DTO для show/create/update/delete/restore.
- `BASE_ACTION_SHOW_DTO`: DTO для show (relations, post-processing).
- И аналогично для create, update, delete, restore.

#### Использование через Builder
Настройка происходит в статических методах конфигурации (определённых в интерфейсе `ICoreCrudler`). Используйте `ActionBuilder::make(FormDTO|OnceDTO $dto)` для цепочной настройки. Builder поддерживает:
- `addShow(Closure|CrudlerMapper $mapper)`: Логика для show (e.g. closure для post-processing модели).
- `addCreate(Closure|CrudlerMapper $mapper)`: Логика для create (pre/post hooks, маппинг).
- `addUpdate(Closure|CrudlerMapper $mapper)`: Логика для update.
- `addDelete(bool|Closure|CrudlerMapper $mapper)`: Включить delete (bool или closure/mapper для hooks).
- `addRestore(bool|Closure|CrudlerMapper $mapper)`: Включить restore.
- `fromConfig(array $config)`: Загружает конфигурацию из массива (опционально, для bulk-настройки). Массив вида `['show' => fn($model) => ..., 'create' => ['pre' => fn($data) => ...], 'delete' => true]`.

После настройки вызовите `build()`, чтобы получить `CrudlerActionDTO`. Если слой не нужен, верните `null`. Для специфических DTO (Show/Create и т.д.) используйте прямое создание или closures: `ActionCreateDTO::start(Closure $preHook, Closure $postHook)`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use App\Modules\Logger\Constants\LoggerSlugs;

use Core\DTO\FormDTO;
use Core\DTO\OnceDTO;
use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Mapper\CrudlerMapper;
use Crudler\Actions\Builders\ActionBuilder;
use Crudler\Actions\Builders\ActionShowBuilder;
use Crudler\Actions\Builders\ActionItemBuilder;
use Crudler\Actions\DTO\CrudlerActionDTO;
use Crudler\Actions\DTO\ActionShowDTO;
use Crudler\Actions\DTO\ActionCreateDTO;
use Crudler\Actions\DTO\ActionUpdateDTO;
use Crudler\Actions\DTO\ActionDeleteDTO;
use Crudler\Actions\DTO\ActionRestoreDTO;
use Crudler\Service\DTO\CrudlerServiceDTO;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Illuminate\Database\Eloquent\Model;

class MyModuleCrudler implements ICrudlerConfig {
    public static function FULL_ACTION_CRUDLER(FormDTO|OnceDTO $dto, ...$args): ?CrudlerActionDTO
    {
        $builder = ActionBuilder::make($dto)
            ->repositoryFunction(
                new class implements IRepositoryFunction {
                    public function __invoke(FormDTO $dto, ...$args): CrudlerRepositoryDTO {
                        return MyModuleCrudler::FULL_REPOSITORY_CRUDLER($dto);
                    }
                }
            )->serviceFunction(
                new class implements IServiceFunction {
                    public function __invoke(FormDTO $dto, ?Model $data = null, ...$args): CrudlerServiceDTO
                    {
                        return MyModuleCrudler::FULL_SERVICE_CRUDLER($dto, $model);
                    }
                }
            )->createBuilder(
                ActionItemBuilder::make()
                    ->successLog()
                    ->errorLog(LoggerSlugs::MODEL_STORE_SLUG)
                    ->buildCreate(LoggerSlugs::MODEL_STORE_SLUG)
            )->updateBuilder(
                ActionItemBuilder::make()
                    ->successLog(LoggerSlugs::MODEL_UPDATE_SLUG)
                    ->errorLog(LoggerSlugs::MODEL_UPDATE_SLUG)
                    ->beforeActionBuilder(
                        BeforeActionBuilder::make()->setBeforeValidation(
                            new class implements IBeforeValidation {
                                public function __invoke(BeforeWithValidationResult $result): BeforeValidationResult {
                                    return BeforeValidationResult::create(
                                        formDTO: $result->formDTO,
                                        previous: $result,
                                        result: [
                                            'something_to_other_step' => true
                                        ]
                                    );
                                }
                            }
                        )
                    )
                    ->buildUpdate()
            )->deleteBuilder(
                ActionItemBuilder::make()
                    ->placeUnique(CrudlerPlaceUniqueEnum::before_custom_validation)
                    ->successLog(LoggerSlugs::MODEL_DELETE_SLUG)
                    ->errorLog(LoggerSlugs::MODEL_DELETE_SLUG)
                    ->buildDelete()
            )->restoreBuilder(
                ActionItemBuilder::make()
                    ->successLog(LoggerSlugs::MODEL_RESTORE_SLUG)
                    ->errorLog(LoggerSlugs::MODEL_RESTORE_SLUG)
                    ->buildRestore()
            );

        if ($dto instanceof OnceDTO) {
            $builder = $builder->showBuilder(
                ActionShowBuilder::make($dto)
                    ->after(
                        new class implements IShowAfterAction {
                            public function __invoke(OnceDTO $dto, Model $data): mixed {
                                $resolver = new CrudlerPolicyResolver();
                                $resolver->resolve(
                                    MyModuleCrudler::BASE_POLICY_CRUDLER(), 
                                    'can_view', 
                                    $dto, 
                                    $model
                                );

                                return true;
                            }
                        }
                    )->return(
                        new class implements IShowReturnAction {
                            public function __invoke(OnceDTO $dto, Model $data, mixed $afterResult = null): mixed {
                                if ($afterResult) {
                                    return $data;
                                }

                                // Something do
                                return $data;
                            }
                        }
                    )->builder()
            );
        }

        // Опционально: загрузка из массива конфига
        // ->fromConfig(['show' => fn($model) => ..., 'delete' => false])

        return $builder->build();
    }

    public static function BASE_ACTION_CRUDLER(
        ?ActionShowDTO $actionShowDTO = null,
        ?ActionCreateDTO $actionCreateDTO = null,
        ?ActionUpdateDTO $actionUpdateDTO = null,
        ?ActionDeleteDTO $actionDeleteDTO = null,
        ?ActionRestoreDTO $actionRestoreDTO = null,
        ...$args
    ): ?CrudlerActionDTO {
        return CrudlerActionDTO::start(
            repositoryFunc: new class implements IRepositoryFunction {
                public function __invoke(FormDTO $dto, ...$args): CrudlerRepositoryDTO {
                    return MyModuleCrudler::FULL_REPOSITORY_CRUDLER($dto);
                }
            },
            serviceFunc: new class implements IServiceFunction {
                public function __invoke(FormDTO $dto, ?Model $data = null, ...$args): CrudlerServiceDTO
                {
                    return MyModuleCrudler::FULL_SERVICE_CRUDLER($dto, $model);
                }
            },
            actionShowDTO: $actionShowDTO,
            actionCreateDTO: $actionCreateDTO,
            actionUpdateDTO: $actionUpdateDTO,
            actionDeleteDTO: $actionDeleteDTO,
            actionRestoreDTO: $actionRestoreDTO,
        );
    }

    public static function BASE_ACTION_SHOW_DTO(OnceDTO $onceDTO, ...$args): ?ActionShowDTO
    {
        return ActionShowBuilder::make($onceDTO)->build();
    }

    public static function BASE_ACTION_CREATE_DTO(FormDTO $formDTO, ...$args): ?ActionCreateDTO
    {
        return ActionItemBuilder::make()
            ->successLog()
            ->errorLog(LoggerSlugs::MODEL_STORE_SLUG)
            ->buildCreate(LoggerSlugs::MODEL_STORE_SLUG);
}

    public static function BASE_ACTION_UPDATE_DTO(FormDTO $formDTO, ...$args): ?ActionUpdateDTO
    {
        return ActionItemBuilder::make()
            ->successLog(LoggerSlugs::MODEL_UPDATE_SLUG)
            ->errorLog(LoggerSlugs::MODEL_UPDATE_SLUG)
            ->beforeActionBuilder(
                BeforeActionBuilder::make()->setBeforeValidation(
                    new class implements IBeforeValidation {
                        public function __invoke(BeforeWithValidationResult $result): BeforeValidationResult {
                            return BeforeValidationResult::create(
                                formDTO: $result->formDTO,
                                previous: $result,
                                result: [
                                    'something_to_other_step' => true
                                ]
                            );
                        }
                    }
                )
            )
            ->buildUpdate();
    }

    public static function BASE_ACTION_DELETE_DTO(FormDTO $formDTO, ...$args): ?ActionDeleteDTO
    {
        return ActionItemBuilder::make()
            ->placeUnique(CrudlerPlaceUniqueEnum::before_custom_validation)
            ->successLog(LoggerSlugs::MODEL_DELETE_SLUG)
            ->errorLog(LoggerSlugs::MODEL_DELETE_SLUG)
            ->buildDelete();
    }

    public static function BASE_ACTION_RESTORE_DTO(FormDTO $formDTO, ...$ar): ?ActionRestoreDTO
    {
        return ActionItemBuilder::make()
            ->successLog(LoggerSlugs::MODEL_RESTORE_SLUG)
            ->errorLog(LoggerSlugs::MODEL_RESTORE_SLUG)
            ->buildRestore();
    }
}
```

В этом примере:
- `FULL_ACTION_CRUDLER`: Полная настройка с динамикой из DTO.
- `BASE_ACTION_CRUDLER`: Базовая, с fallback на другие методы если DTO null.
- `BASE_ACTION_SHOW_DTO`: Closure для show (e.g. load relations).
- Аналогично для create, update (pre/post hooks), delete/restore.

### Доступные параметры ActionItemBuilder

| Метод                  | Назначение                         |
| ---------------------- | ---------------------------------- |
| `withoutValidation()`  | отключить валидацию                |
| `getFunc()`            | вернуть callable вместо выполнения |
| `withoutTransaction()` | отключить транзакцию               |
| `placeUnique()`        | место проверки уникальности        |
| `errorMessage()`       | сообщение ошибки                   |
| `successLog()`         | success-лог                        |
| `errorLog()`           | error-лог                          |
| `beforeActionDTO()`    | хуки до сервиса                    |
| `inActionDTO()`        | хуки вокруг сервиса                |
| `repositoryDTO()`      | кастомный RepositoryDTO            |

---

#### Хуки BeforeAction (валидация)

Хуки валидации задаются через `BeforeActionDTO`.

### Доступные хуки

| Хук                    | Интерфейс               | Момент выполнения                                |
| ---------------------- | ----------------------- | ------------------------------------------------ |
| `beforeWithValidation` | `IBeforeWithValidation` | Перед блоком withValidation                      |
| `beforeValidation`     | `IBeforeValidation`     | До выполнения проверки в блоке WithValidation    |
| `afterValidation`      | `IAfterValidation`      | После выполнения проверки в блоке WithValidation |
| `afterWithValidation`  | `IAfterWithValidation`  | После блока withValidation                       |


### Пример inline-хуков

```php
$before = BeforeActionBuilder::make()
    ->setBeforeWithValidation(
        new class implements IBeforeWithValidation {
            public function __invoke(FormDTO $dto): BeforeWithValidationResult {
                return BeforeWithValidationResult::create($dto);
            }
        }
    )
    ->build();
```

---

#### Хуки InAction (вокруг сервиса)

`InActionDTO` управляет хуками вокруг вызова сервиса.

### Доступные хуки

| Хук            | Интерфейс       | Момент выполнения                     |
| -------------- | --------------- | ------------------------------------- |
| `beforeAction` | `IBeforeAction` | До выполнения действия (в service)    |
| `afterAction`  | `IAfterAction`  | После выполнения действия (в service) |
| `return`       | `IReturn`       | В момент возврата                     |

### Пример inline-хуков

```php
$inAction = InActionBuilder::make()
    ->setReturn(
        new class implements IReturn {
            public function __invoke(AfterActionResult $result): ReturnResult {
                return ReturnResult::create(
                    $result->formDTO,
                    $result,
                    ['id' => $result->data->id]
                );
            }
        }
    )
    ->build();
```

#### Как использовать Action в коде
1. Получите DTO из конфига: `$actionDto = MyModuleCrudler::FULL_ACTION_CRUDLER($dto, ...$args);`.
2. Создайте Action: `$action = new CrudlerAction(MyModel::class);` (modelClass, инжектируя Service/Repo/Policy).
3. Вызовите методы: `$result = $action->create($actionDto);` (использует config из DTO).
   - Основные методы (интегрируют BaseService):
     - `show(OnceDTO $dto)`: Возвращает модель после hooks.
     - `create(FormDTO $dto)`: Создаёт и возвращает модель.
     - `update(FormDTO $dto, Model $model)`: Обновляет и возвращает.
     - `destroy(OnceDTO $dto, Model $model, ExecutionOptionsDTO $config)`: Delete с результатом.
     - `restore(OnceDTO $dto, Model $model, ExecutionOptionsDTO $config)`: Restore с результатом.
     - Другие из BaseService (incrementField, attachRelations и т.д.), оборачиваемые в hooks если config.

**Пример в контроллере:**
```php
$actionDto = MyModuleCrudler::FULL_ACTION_CRUDLER($dto);
$action = new CrudlerAction(MyModel::class);

// Show
$result = $action->crudlerShow($actionDTO);

// Create
$result = $action->crudlerCreate($actionDTO);

// Update
$result = $action->crudlerUpdate($actionDTO);

// Delete
$result = $action->crudlerDelete($actionDTO);

// Restore
$result = $action->crudlerRestore($actionDTO);
```

#### Описание CrudlerPlaceUniqueEnum
`CrudlerPlaceUniqueEnum` — это enum для определения позиции проверки уникальности в пайплайне действия. Проверка уникальности (unique check) использует config из Repository (RepositoryUniqueDTO) и вставляется в поток BeforeActionPipeline в зависимости от выбранного места. Это позволяет контролировать, когда проверять уникальность (e.g. до/после валидации).

Доступные вариации (cases enum):
- `default`: Проверка после `beforeValidation` хука, но перед `afterValidation` (стандартное место внутри withValidation блока).
- `before_validation`: Перед `beforeValidation` хуком (ранняя проверка внутри withValidation).
- `after_validation`: После `afterValidation` хука (поздняя проверка внутри withValidation, но в коде явно не указана — возможно, fallback в default).
- `before_with_validation`: Перед `beforeWithValidation` хуком (до всего withValidation блока).
- `after_with_validation`: После `afterWithValidation` хука (после всего withValidation блока).
- `before_custom_validation`: Перед кастомной валидацией (если custom validation в beforeWithValidation или других hooks; самое раннее место).
- `after_custom_validation`: После кастомной валидации (если custom в afterWithValidation; позднее место).
- `none`: Отключить проверку уникальности полностью.

Рекомендации:
- Используйте `default` для стандартных случаев.
- `before_custom_validation` для проверки до любых хуков (как в примере delete).
- `none` если уникальность не нужна (e.g. для delete/restore).

#### Полная карта потока (с хуками и CrudlerPlaceUniqueEnum)
Поток выполнения действия (для create/update/delete/restore) организован в пайплайнах: BeforeActionPipeline (валидация и pre-хуки) и InActionPipeline (вокруг service call). Unique check (если включен) вставляется в BeforeActionPipeline на основе CrudlerPlaceUniqueEnum. Вот последовательность шагов (диаграмма в текстовой форме):

1. **Начало (BeforeActionPipeline)**:
   - **Unique if `before_custom_validation`**: Проверка уникальности (если placeUnique = before_custom_validation).
   - **Hook: beforeWithValidation** (IBeforeWithValidation): Pre-hook перед валидацией блока.
   - **Unique if `before_with_validation`**: Проверка уникальности.
   - **If withValidation = true**:
     - **Unique if `before_validation`**: Проверка уникальности.
     - **Hook: beforeValidation** (IBeforeValidation): Pre-hook перед основной валидацией.
     - **Unique if `default`**: Проверка уникальности (стандарт).
     - **Основная валидация** (Laravel validated()).
     - **Hook: afterValidation** (IAfterValidation): Post-hook после валидации.
     - **Unique if `after_validation`**: Проверка уникальности (если реализовано; в коде fallback в default).
   - **Hook: afterWithValidation** (IAfterWithValidation): Post-hook после валидационного блока.
   - **Unique if `after_with_validation`**: Проверка уникальности.
   - **Unique if `after_custom_validation`**: Проверка уникальности (если custom validation в afterWithValidation).

2. **Сервис вызов (InActionPipeline)**:
   - **Hook: beforeAction** (IBeforeAction): Pre-hook перед service.
   - **Service call**: Вызов _create/_update/_delete/_restore из Service (с маппингом данных).
   - **Hook: afterAction** (IAfterAction): Post-hook после service (с данными).
   - **Hook: return** (IReturn): Финальный возврат результата (модификация).

Для show: Отдельный поток без валидации/unique — policy check → repo _showOnceById → after hook → return hook.

- Если placeUnique = none: Нет unique check.
- Hooks возвращают Results (e.g. BeforeWithValidationResult), передавая состояние (formDTO, previous, result) для chain.
- Политика (policy resolve) вызывается автоматически перед repo/service (для show/update/delete/restore).

#### Примечания
- **Closures/Hooks**: Для create/update: pre (модифицирует data), post (обрабатывает модель). Для show/delete/restore: full closure или bool.
- **CrudlerMapper**: Опционально для маппинга в DTO (как в Service).
- **Ошибки**: Действия бросают исключения или возвращают results (как в Service destroy/restore).
- **Интеграция**: Action вызывает Service для мутаций, Repository для чтения, Policy для checks (автоматически если config). `fromConfig` парсит массивы в closures/mappers.
- **Гибкость**: ...$args для кастомных параметров (e.g. extra data). Поддержка транзакций по умолчанию.
