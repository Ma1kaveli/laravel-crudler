### Router Layer

Router в Crudler — это слой для регистрации маршрутов (routes) с автоматической инъекцией `CrudlerControllerDTO` в методы контроллера. Он предоставляет удобные static методы для HTTP-методов (get/post/put/etc.), которые регистрируют роут через Laravel Route, но с closure для вызова контроллера с сгенерированным DTO. Это упрощает роутинг для CRUD, обеспечивая, что метод контроллера (e.g. crudlerCreate) получает готовый `CrudlerControllerDTO` (с конфигом для action/resource/policy), без ручной обработки. Router фокусируется на инъекции и не добавляет middleware/groups — используйте Laravel Route::group для этого.

Поддерживаемые возможности:
- **Методы HTTP**: get/post/put/patch/delete/options/any (прямые аналоги Route::$method).
- **Target**: Массив [$controllerClass, $actionMethod, $dtoFunc] — $dtoFunc это fn() => CrudlerControllerDTO (e.g. из FULL_CONTROLLER_CRUDLER, генерирует DTO динамически).
- **Инъекция**: Автоматически вызывает $dtoFunc() и передаёт `CrudlerControllerDTO` в метод контроллера (e.g. crudlerCreate(CrudlerControllerDTO $dto)).
- **Интеграция**: Работает с CrudlerController (crudler-методы), Action для логики, Resource для ответа.
- **Crudler-специфично**: Нет отдельного DTO/Builder, но роуты могут группироваться в config-методе (e.g. BASE_ROUTER_CRUDLER) для модульности.

Router не имеет сложной config — это утилита для регистрации в routes/api.php или provider. Если нужно, определите BASE_ROUTER_CRUDLER в config для вызова всех роутов модуля (void|null).

#### Использование через Builder
Router не имеет отдельного Builder (прямые static calls на CrudlerRoute), но настройка может происходить в статическом методе конфигурации вроде BASE_ROUTER_CRUDLER (опциональный, для группировки роутов модуля). В нём регистрируйте роуты с CrudlerRoute. Если слой не нужен, верните null (но обычно роуты регистрируются напрямую).

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use App\Controllers\API\MyModuleController;

use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Routing\CrudlerRoute;

class MyModuleCrudler implements ICrudlerConfig {
    // Опциональный метод для роутов (void|null)
    public static function BASE_ROUTER_CRUDLER(...$args): void|null
    {
        // List (inject CrudlerControllerDTO)
        CrudlerRoute::get('/items', [MyModuleController::class, 'crudlerList', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);

        // Show (inject CrudlerControllerDTO)
        CrudlerRoute::get('/items/{id}', [MyModuleController::class, 'crudlerShow', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);

        // Create (inject CrudlerControllerDTO)
        CrudlerRoute::post('/items', [MyModuleController::class, 'crudlerCreate', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);

        // Update (inject CrudlerControllerDTO)
        CrudlerRoute::put('/items/{id}', [MyModuleController::class, 'crudlerUpdate', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);

        // Delete (inject CrudlerControllerDTO)
        CrudlerRoute::delete('/items/{id}', [MyModuleController::class, 'crudlerDelete', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);

        // Restore (inject CrudlerControllerDTO)
        CrudlerRoute::post('/items/{id}/restore', [MyModuleController::class, 'crudlerRestore', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);

        // Группировка (Laravel-style)
        Route::group(['prefix' => 'my-module', 'middleware' => 'auth'], function () {
            CrudlerRoute::get('/custom', [MyModuleController::class, 'customMethod', fn() => self::FULL_CONTROLLER_CRUDLER(...$args)]);
        });
    }
}
```

В этом примере:
- `BASE_ROUTER_CRUDLER`: Группирует CRUD-роуты с инъекцией `CrudlerControllerDTO` (fn() вызывает FULL_CONTROLLER_CRUDLER для генерации DTO).
- Вызов в provider: `MyModuleCrudler::BASE_ROUTER_CRUDLER();` (для загрузки роутов модуля).

#### Как использовать Router в коде
1. Наследуйте контроллер от CrudlerController: `class MyModuleController extends CrudlerController { }` (использует crudler-методы с CrudlerControllerDTO).
2. Зарегистрируйте роут: `CrudlerRoute::post('/items', [MyModuleController::class, 'crudlerCreate', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);`.
   - fn() => FULL_CONTROLLER_CRUDLER() генерирует `CrudlerControllerDTO` (с конфигом для action/resource/policy).
   - Метод контроллера получает `CrudlerControllerDTO` автоматически (e.g. crudlerCreate(CrudlerControllerDTO $dto) вызывает action и возвращает resource).
   - Для list/show/delete/restore: Используйте то же fn(), DTO адаптируется внутри crudler-метода (e.g. извлекает sub-DTO как $dto->indexDTO для list).

**Пример полного роутинга в routes/api.php (без config-метода):**
```php
use App\Controllers\MyModuleController;
use App\Modules\MyModule\Crudler\MyModuleCrudler;

use Crudler\Routing\CrudlerRoute;

// List
CrudlerRoute::get('/items', [MyModuleController::class, 'crudlerList', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);

// Show
CrudlerRoute::get('/items/{id}', [MyModuleController::class, 'crudlerShow', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);

// Create
CrudlerRoute::post('/items', [MyModuleController::class, 'crudlerCreate', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);

// Update
CrudlerRoute::patch('/items/{id}', [MyModuleController::class, 'crudlerUpdate', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);

// Delete
CrudlerRoute::delete('/items/{id}', [MyModuleController::class, 'crudlerDelete', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);

// Restore
CrudlerRoute::post('/items/{id}/restore', [MyModuleController::class, 'crudlerRestore', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);
```

#### Методы
- `static get/post/put/patch/delete/options/any(string $uri, array $target)`: Регистрирует роут.
  - `$target = [Controller::class, 'method', fn() => CrudlerControllerDTO]` — fn() возвращает `CrudlerControllerDTO` для инъекции.
- `protected static register(string $method, string $uri, array $target)`: Внутренний, вызывает Route::$method с closure для app($controller)->$action($dtoFunc()).

#### Примечания
- **Инъекция**: fn() вызывает FULL_CONTROLLER_CRUDLER() для `CrudlerControllerDTO` (содержит sub-config для метода, e.g. $dto->createDTO для create).
- **CrudlerController**: Обеспечивает crudler-методы, которые используют DTO для action (execute)/resource (transform), возвращают JsonResponse с 'data'/'message'.
- **Ошибки**: Роуты наследуют Laravel errors (404 для не найденного, 403 от policy в action).
- **Интеграция**: Используйте с CrudlerController для CRUD. Нет fromConfig — прямые calls. Группируйте с Route::group для prefix/middleware/auth.
- **Гибкость**: ...$args в FULL_CONTROLLER_CRUDLER для params (e.g. dynamic config). Для кастом методов используйте DynamicDTO если нужно, но для crudler* — всегда CrudlerControllerDTO. Поддержка request()->id внутри fn() для DTO с ID.
