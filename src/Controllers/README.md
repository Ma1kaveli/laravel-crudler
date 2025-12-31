### Controller Layer

Controller в Crudler — это слой для обработки HTTP-запросов, интегрирующий Request (валидация), Action (бизнес-логика), Resource (форматирование ответа) и Router (регистрация маршрутов). Он расширяет `CrudlerController` (на основе `CoreCrudlerController`), предоставляя готовые методы для CRUD: `crudlerList` (список с пагинацией или getAll), `crudlerShow` (просмотр одной записи), `crudlerCreate` (создание), `crudlerUpdate` (обновление), `crudlerDelete` (удаление), `crudlerRestore` (восстановление). Это упрощает контроллеры, делая их declarative через DTO, с автоматической инъекцией (FormDTO, ListDTO, OnceDTO) и обработкой ошибок (404/403).

Поддерживаемые возможности:
- **Методы контроллера**: Стандартизированные crudler-методы, которые вызывают action (execute) и возвращают resource (или коллекцию/paginated).
- **Интеграция**: Автоматический вызов Request validated(), Action _create/_update/etc., Resource resource/collection.
- **DTO-инъекция**: Через CrudlerRoute, DTO генерируются динамически (e.g. ListDTO для list из request).
- **Кастомизация**: Для list — getAll/paginateResponse/additionalData; для show/create/update — additionalData/requestTag/successMessage; для delete/restore — requestTag/successMessage.
- **Crudler-специфично**: Динамическая настройка через DTO для каждого метода (ControllerListDTO, ControllerShowDTO и т.д.), с callable для DTO/resolver.

Controller не содержит бизнес-логики (делегирует Action), а фокусируется на HTTP (request/response/JSON). Конфигурация преобразуется в `CrudlerControllerDTO`, который включает специфические DTO для методов (`ControllerListDTO`, `ControllerShowDTO` и т.д.). Эти DTO определяют config для formDTO/request/additionalData/successMessage. Методы конфига позволяют настраивать разные аспекты:
- `FULL_CONTROLLER_CRUDLER`: Полная настройка с ...$args (для динамики, e.g. extra params).
- `BASE_CONTROLLER_CRUDLER`: Базовая, с опциональными DTO для list/show/create/update/delete/restore и ...$args (fallback на BASE_..._DTO).
- `BASE_CONTROLLER_LIST_DTO`: DTO для list (dto/isGetAll/isPaginateResponse/additionalData).
- И аналогично для show (showDTO/additionalData), create (formDTO/request/requestTag/additionalData/successMessage), update (аналогично create), delete (onceDTO/request/requestTag/successMessage), restore (аналогично delete).

#### Использование через Builder
Настройка происходит в статических методах конфигурации (определённых в интерфейсе `ICoreCrudler`). Используйте `ControllerBuilder::make()` для цепочной настройки. Builder поддерживает:
- `listDTO(ControllerListDTO $listDTO)`: Config для list.
- `showDTO(ControllerShowDTO $showDTO)`: Config для show.
- `createDTO(ControllerCreateDTO $createDTO)`: Config для create.
- `updateDTO(ControllerUpdateDTO $updateDTO)`: Config для update.
- `deleteDTO(ControllerDeleteDTO $deleteDTO)`: Config для delete.
- `restoreDTO(ControllerRestoreDTO $restoreDTO)`: Config для restore.
- `resource(BaseResource|CrudlerResourceDTO $resource)`: Ресурс для ответа.
- `action(IActionFunction $actionFunction)`: Функция для action DTO.
- `fromConfig(array $config, ?FormDTO $createDTO = null, ?FormDTO $updateDTO = null)`: Загружает конфигурацию из массива (опционально, для bulk-настройки). Массив вида `['list' => [...], 'create' => [...], 'delete' => true]`.
- Sub-builders: `listBuilder(ControllerListBuilder $builder)`, `showBuilder(ControllerShowBuilder $builder)` и т.д. для детальной настройки.

После настройки вызовите `build()`, чтобы получить `CrudlerControllerDTO`. Если слой не нужен, верните `null`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Controllers\Builders\ControllerBuilder;
use Crudler\Controllers\Builders\ControllerListBuilder;
use Crudler\Controllers\DTO\CrudlerControllerDTO;
use Crudler\Controllers\DTO\ControllerListDTO;
use Crudler\Controllers\DTO\ControllerShowDTO;
use Crudler\Controllers\DTO\ControllerCreateDTO;
use Crudler\Controllers\DTO\ControllerUpdateDTO;
use Crudler\Controllers\DTO\ControllerDeleteDTO;
use Crudler\Controllers\DTO\ControllerRestoreDTO;
use Crudler\Resources\DTO\CrudlerResourceDTO;
use Crudler\Repositories\Interfaces\IActionFunction;

use Core\DTO\FormDTO;

class MyModuleCrudler implements ICrudlerConfig {
    public static function FULL_CONTROLLER_CRUDLER(...$args): ?CrudlerControllerDTO
    {
        $builder = ControllerBuilder::make()
            ->resource(new CrudlerResourceDTO(/* generator */))
            ->action(new class implements IActionFunction { /* impl */ })
            ->listBuilder(
                ControllerListBuilder::make()
                    ->isGetAll()
                    ->additionalData(['extra' => 'value'])
            )
            ->showBuilder(
                ControllerShowBuilder::make()
                    ->additionalData(['related' => 'data'])
            )
            ->createBuilder(
                ControllerCreateBuilder::make(fn() => FormDTO::make())
                    ->tag('tag_create')
            )
            ->updateBuilder(
                ControllerUpdateBuilder::make(fn() => FormDTO::make())
                    ->tag('tag_update')
            )
            ->deleteBuilder(
                ControllerDeleteBuilder::make()
                    ->tag('tag_delete')
            )
            ->restoreBuilder(
                ControllerRestoreBuilder::make()
                    ->successMessage('Restored!')
            );

        // Опционально: загрузка из массива конфига
        // ->fromConfig(['list' => ['is_get_all' => true], 'create' => ['success_message' => 'Created!']])

        return $builder->build();
    }

    public static function BASE_CONTROLLER_CRUDLER(
        ?ControllerListDTO $controllerListDTO = null,
        ?ControllerShowDTO $controllerShowDTO = null,
        ?ControllerCreateDTO $controllerCreateDTO = null,
        ?ControllerUpdateDTO $controllerUpdateDTO = null,
        ?ControllerDeleteDTO $controllerDeleteDTO = null,
        ?ControllerRestoreDTO $controllerRestoreDTO = null,
        ...$args
    ): ?CrudlerControllerDTO {
        return CrudlerControllerDTO::start(
            resource: new CrudlerResourceDTO(/* generator */),
            actionFunction: new class implements IActionFunction { /* impl */ },
            indexDTO: controllerListDTO,
            indexDTO: controllerListDTO,
            showDTO: $controllerShowDTO,
            createDTO: $controllerCreateDTO,
            updateDTO: $controllerUpdateDTO,
            deleteDTO: $controllerDeleteDTO,
            restoreDTO: $controllerRestoreDTO,
        );
    }

    public static function BASE_CONTROLLER_LIST_DTO(...$args): ?ControllerListDTO
    {
        return ControllerListBuilder::make()
            ->isGetAll()
            ->additionalData(['extra' => 'value'])
            ->build();
    }

    public static function BASE_CONTROLLER_SHOW_DTO(...$args): ?ControllerShowDTO
    {
        return ControllerShowBuilder::make()
            ->additionalData(['extra'])
            ->build();
    }

    public static function BASE_CONTROLLER_CREATE_DTO(...$args): ?ControllerCreateDTO
    {
        return ControllerCreateBuilder::make(fn() => FormDTO::make())
            ->tag('create_tag')
            ->build();
    }

    public static function BASE_CONTROLLER_UPDATE_DTO(...$args): ?ControllerUpdateDTO
    {
        return ControllerUpdateBuilder::make(fn() => FormDTO::make())
            ->tag('update_tag')
            ->build();
    }

    public static function BASE_CONTROLLER_DELETE_DTO(...$args): ?ControllerDeleteDTO
    {
        return ControllerDeleteBuilder::make()
            ->tag('delete_tag')
            ->build();
    }

    public static function BASE_CONTROLLER_RESTORE_DTO(...$args): ?ControllerRestoreDTO
    {
        return ControllerRestoreBuilder::make()
            ->tag('restore_tag')
            ->build();
    }
}
```

В этом примере:
- `FULL_CONTROLLER_CRUDLER`: Полная настройка с sub-builders.
- `BASE_CONTROLLER_CRUDLER`: Базовая, с fallback на другие методы если DTO null.
- `BASE_CONTROLLER_LIST_DTO`: Для list (isGetAll/additionalData).
- Аналогично для других.

#### Как использовать Controller в коде
1. Создайте контроллер: `class MyModuleController extends CrudlerController { }` (наследует crudler-методы).
2. Получите DTO из конфига: `$controllerDto = MyModuleCrudler::FULL_CONTROLLER_CRUDLER(...$args);`.
3. Зарегистрируйте роут: `CrudlerRoute::get('/items', [MyModuleController::class, 'crudlerList', fn() => MyModuleCrudler::BASE_CONTROLLER_LIST_DTO()]);`.
   - DTO инжектируется автоматически в метод (e.g. `crudlerList(CrudlerControllerDTO $dto)`).
   - Метод вызывает action (e.g. _list) и resource (collection/resource), возвращает JSON с 'data'/'message'.

**Пример в контроллере:**
```php
class MyModuleController extends CrudlerController {
    public function __construct(IHttpContext $http) {
        parent::__construct(
            actions: new CrudlerAction(MyModel::class),
            http: $http,
        );
    }
}
```

#### Примечания
- **CrudlerRoute**: Автоматическая инъекция CrudlerControllerDTO в метод (fn() возвращает BASE_..._DTO, но полный DTO через FULL/BASE_CRUDLER).
- **Ошибки**: Методы бросают 404/400 на основе action (e.g. 'Not found!').
- **Интеграция**: Вызывает action из actionFunction, resource для форматирования. `fromConfig` парсит массивы в DTO.
- **Гибкость**: ...$args для кастом (e.g. params для DTO). Sub-builders (ControllerListBuilder etc.) для детальной настройки (e.g. isGetAll(), additionalData()). Поддержка paginated (QueryBuilder\Resources\PaginatedCollection).
