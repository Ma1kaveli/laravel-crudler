### Service Layer

Service в Crudler — это слой для бизнес-логики, фокусирующийся на мутациях данных (create, update, delete, restore), интеграции с Repository (для чтения), Policies (для авторизации) и обработке транзакций. Он расширяет `BaseService`, предоставляя готовые методы для CRUD, манипуляций с полями (increment, hash, encrypt), отношениями (attach/detach/sync), timestamps и другими операциями (bulk insert/update/delete, JSON/array fields). Service отделяет логику от контроллеров, обеспечивая повторное использование и тестируемость.

Поддерживаемые возможности:
- **CRUD**: Create/update/delete/restore с поддержкой soft deletes, force delete.
- **Мутации полей**: Increment/decrement, toggle boolean, set timestamp/uuid/slug/random/hash/encrypt.
- **Отношения**: Attach/detach/sync для many-to-many.
- **Массовые операции**: Bulk insert/update/delete, batched insert, upsert.
- **JSON/Array**: Merge/append/prepend/remove/set/unset для полей.
- **Дополнительно**: Duplicate model, lock for update, truncate, touch.
- **Транзакции и опции**: Через `ExecutionOptionsDTO` (withTransaction, withValidation, writeErrorLog, getFunc).
- **Crudler-специфично**: Динамическая настройка через DTO для каждого действия (create/update/delete/restore), с маппингом полей (closures для динамики).

Service не читает данные напрямую (использует Repository), но фокусируется на записи. Конфигурация преобразуется в `CrudlerServiceDTO`, который включает специфические DTO для действий (`ServiceCreateDTO`, `ServiceUpdateDTO` и т.д.). Эти DTO определяют поля для маппинга (из FormDTO в array для модели) с помощью `CrudlerMapper`. Методы конфига позволяют настраивать разные аспекты:
- `FULL_SERVICE_CRUDLER`: Полная настройка с FormDTO и ?Model (для динамики, e.g. update на существующей модели).
- `BASE_SERVICE_CRUDLER`: Базовая, с опциональными DTO для create/update/delete/restore.
- `BASE_SERVICE_CREATE_DTO`: DTO для create (маппинг полей).
- И аналогично для update, delete, restore (delete/restore могут быть bool для отключения или DTO с config).

#### Использование через Builder
Настройка происходит в статических методах конфигурации (определённых в интерфейсе `ICoreCrudler`). Используйте `ServiceBuilder::make(FormDTO $dto)` для цепочной настройки. Builder поддерживает:
- `addCreate(CrudlerMapper $mapper)`: Маппинг для create (поля из DTO в модель).
- `addUpdate(CrudlerMapper $mapper)`: Маппинг для update.
- `addDelete(bool|CrudlerMapper $mapper)`: Включить delete (bool true/false или mapper для кастомной логики).
- `addRestore(bool|CrudlerMapper $mapper)`: Включить restore (аналогично delete).
- `setData(?Model $data)`: Установить модель для update/delete/restore.
- `fromConfig(array $config)`: Загружает конфигурацию из массива (опционально, для bulk-настройки). Массив вида `['create' => [...fields...], 'update' => [...], 'delete' => true/false]`.

После настройки вызовите `build()`, чтобы получить `CrudlerServiceDTO`. Если слой не нужен, верните `null`. Для специфических DTO (Create/Update/Delete/Restore) используйте прямое создание или мапперы: `ServiceCreateDTO::start(CrudlerMapper::make()->field(...))`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use App\Modules\MyModule\Models\MyModel;

use Core\DTO\FormDTO;
use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Mapper\CrudlerMapper;
use Crudler\Services\Builders\ServiceBuilder;
use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\DTO\ServiceCreateDTO;
use Crudler\Services\DTO\ServiceUpdateDTO;
use Crudler\Services\DTO\ServiceDeleteDTO;
use Crudler\Services\DTO\ServiceRestoreDTO;
use Illuminate\Database\Eloquent\Model;

class MyModuleCrudler implements ICrudlerConfig {
    public static function FULL_SERVICE_CRUDLER(FormDTO $dto, ?Model $data = null, ...$args): ?CrudlerServiceDTO
    {
        $builder = ServiceBuilder::make($dto)
            ->addCreate(
                CrudlerMapper::make()
                    ->field('name')
                    ->field('description')
                    ->field('role_id')
                    ->field(
                        'created_by',
                        fn (FormDTO $dto) => $dto->user->id
                    )
            )
            ->addUpdate(
                CrudlerMapper::make()
                    ->field('name')
                    ->field('description')
                    ->field('role_id')
                    ->field(
                        'updated_by',
                        fn (FormDTO $dto) => $dto->user->id  // Modifier
                    )
            )
            ->addDelete(
                modelWithSoft: true,
                alreadyDeleteMessage: 'Уже удалено',
                successMessage: 'Удалено успешно',
                errorMessage: 'Ошибка удаления',
                configOpts: ['withTransaction' => false]  // Опции для ExecutionOptionsDTO
            )
            ->addRestore(
                notDeleteMessage: 'Не удалено',
                successMessage: 'Восстановлено',
                errorMessage: 'Ошибка восстановления',
                configOpts: ['writeErrorLog' => false]
            );

        if ($data instanceof Model) {
            $builder = $builder->setData($data);
        }

        // Опционально: загрузка из массива конфига
        // ->fromConfig(['create' => ['extra' => 'value'], 'delete' => false])

        return $builder->build();
    }

    public static function BASE_SERVICE_CRUDLER(
        ?ServiceCreateDTO $serviceCreateDTO = null,
        ?ServiceUpdateDTO $serviceUpdateDTO = null,
        ?ServiceDeleteDTO $serviceDeleteDTO = null,
        ?ServiceRestoreDTO $serviceRestoreDTO = null
    ): ?CrudlerServiceDTO {
        return CrudlerServiceDTO::start(
            serviceCreateDTO: $serviceCreateDTO,
            serviceUpdateDTO: $serviceUpdateDTO,
            serviceDeleteDTO: $serviceDeleteDTO,
            serviceRestoreDTO: $serviceRestoreDTO,
        );
    }

    public static function BASE_SERVICE_CREATE_DTO(FormDTO $formDTO): ?ServiceCreateDTO
    {
        return ServiceBuilder::make($formDTO)->addCreate(
            CrudlerMapper::make()
                ->field('name')
                ->field('description')
                ->field('role_id')
                ->field(
                    'created_by',
                    fn (FormDTO $dto) => $dto->user->id
                )
        )->buildCreate();
    }

    public static function BASE_SERVICE_UPDATE_DTO(FormDTO $formDTO, Model $data): ?ServiceUpdateDTO
    {
        return ServiceBuilder::make($formDTO)->addUpdate(
            CrudlerMapper::make()
                ->field('name')
                ->field('description')
                ->field('role_id')
                ->field(
                    'updated_by',
                    fn (FormDTO $dto) => $dto->user->id  // Modifier
                )
        )->setData($data)->buildUpdate();
    }

    public static function BASE_SERVICE_DELETE_DTO(FormDTO $formDTO, Model $data): ?ServiceDeleteDTO
    {
        return ServiceBuilder::make($formDTO)->addDelete(
            modelWithSoft: true,
            alreadyDeleteMessage: 'Уже удалено',
            successMessage: 'Удалено успешно',
            errorMessage: 'Ошибка удаления',
            configOpts: ['withTransaction' => false]
        )->setData($data)->buildDelete();
    }

    public static function BASE_SERVICE_RESTORE_DTO(FormDTO $formDTO, Model $data): ?ServiceRestoreDTO
    {
        return ServiceBuilder::make($formDTO)->addRestore(
            notDeleteMessage: 'Не удалено',
            successMessage: 'Восстановлено',
            errorMessage: 'Ошибка восстановления',
            configOpts: ['writeErrorLog' => false]
        )->setData($data)->buildRestore();
    }
}
```

В этом примере:
- `FULL_SERVICE_CRUDLER`: Полная настройка с динамикой из DTO и модели.
- `BASE_SERVICE_CRUDLER`: Базовая, с fallback на другие методы если DTO null.
- `BASE_SERVICE_CREATE_DTO`: Маппинг для create (с closures).
- Аналогично для update/delete/restore (delete/restore как bool или mapper).

### Создание из конфига-массива
```php
$config = [
    "create" => [
        "name",
        "description",
        "created_by" => fn ($d) => $d->user->id,
        "role_id"
    ],
    "update" => [
        "name",
        "description",
        "updated_by" => fn ($d) => $d->user->id,
        "role_id"
    ],
    "delete" => [
        "model_with_soft" => true,
        "already_delete_message" => "",
        "success_message" => "",
        "error_message" => "",
        "config" => ['withTransaction' => false]
    ],
    "restore" => [
        "not_delete_message" => "",
        "success_message" => "",
        "error_message" => "",
        "config" => ['writeErrorLog' => false]
    ],
];

$builder = ServiceBuilder::make($dto)
    ->fromConfig($config)
    ->setData($data)
    ->build();
```

#### Как использовать Service в коде
1. Получите DTO из конфига: `$serviceDto = MyModuleCrudler::FULL_SERVICE_CRUDLER($dto, $model);`.
2. Создайте Service: `$service = new CrudlerService(MyModel::class);` (modelClass).
3. Вызовите методы: `$newModel = $service->create($dto->toArray());` или базовые из `BaseService`.
   - Crudler-специфично: Методы используют маппинг из DTO для фильтрации/модификации данных перед сохранением.
   - Полный список из `BaseService` (примеры):
     - `create(array $data)`: Создание модели.
     - `update(Model $model, array $data)`: Обновление.
     - `destroy(Model $data, ExecutionOptionsDTO $config, string $alreadyDeleteMessage, string $successMessage, string $errorMessage)`: Soft delete с опциями.
     - `restore(Model $data, ExecutionOptionsDTO $config, string $notDeleteMessage, string $successMessage, string $errorMessage)`: Restore.
     - `incrementField(Model $model, string $field, int $amount = 1)`: Инкремент.
     - `attachRelations(Model $model, string $relation, array $ids)`: Attach many-to-many.
     - `bulkUpdate(array $updates, array $conditions)`: Массовое обновление.
     - `hashField(Model $model, string $field)`: Хэширование (e.g. password).
     - `mergeJsonField(Model $model, string $field, array $newData)`: Merge JSON.
     - И многие другие (forceDelete, upsert, duplicate, setUuid и т.д.).

**Пример в контроллере:**
```php
$serviceDto = MyModuleCrudler::FULL_SERVICE_CRUDLER($dto, $model);
$crudler = new CrudlerService(MyModel::class);

// Create
$createdModel = $crudler->crudlerCreate($dto);  // $dto - CrudlerServiceDTO из builder. Возвращает Model или Exception

// Update (требует setData в builder для модели)
$updatedModel = $crudler->crudlerUpdate($dto);  // $dto - CrudlerServiceDTO из builder. Возвращает Model или Exception

// Destroy (мягкое удаление или force)
$result = $crudler->crudlerDestroy($dto);  // $dto - CrudlerServiceDTO из builder. Возвращает array (result) или Exception

// Restore
$result = $crudler->crudlerRestore($dto);  // $dto - CrudlerServiceDTO из builder. Возвращает array (result) или Exception
```

#### Примечания
- **CrudlerMapper**: Упрощает маппинг: `field(string $key, string|callable|array $value = null)`. Closures принимают FormDTO для динамики (e.g. fn($dto) => $dto->user->id).
- **Delete/Restore**: Как bool (вкл/выкл) или mapper для кастом (e.g. set 'deleted_by' = $dto->user->id).
- **Ошибки**: Destroy/restore возвращают array с success/message или throw при ошибке (если writeErrorLog).
- **Интеграция**: Используйте с Repository для чтения, Policies для checks перед мутациями. `fromConfig` парсит массивы в мапперы.
- **Гибкость**: ExecutionOptionsDTO для контроля (e.g. withoutValidation). Поддержка транзакций по умолчанию.
