# Laravel Crudler

[![Packagist Version](https://img.shields.io/packagist/v/makaveli/laravel-crudler.svg?style=flat-square)](https://packagist.org/packages/makaveli/laravel-crudler)
[![Packagist Downloads](https://img.shields.io/packagist/dt/makaveli/laravel-crudler.svg?style=flat-square)](https://packagist.org/packages/makaveli/laravel-crudler)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Advanced Crudler for Laravel — это модульный фреймворк для упрощения создания CRUD-операций в приложениях на Laravel. Он предоставляет унифицированный подход к настройке слоев: Policy (авторизация), Request (валидация), Resource (форматирование ответа), Repository (чтение данных), Service (мутации данных), Action (бизнес-действия), Controller (обработка запросов) и Router (регистрация маршрутов). Основной принцип — конфигурация через Builder-паттерн и статические методы в config-классе (наследнике `CrudlerConfig`), что делает код declarative, testable и reusable.

Crudler расширяет базовые компоненты Laravel (e.g. FormRequest, JsonResource, BaseService/BaseRepository), добавляя динамические hooks, маппинг полей (через `CrudlerMapper`), контексты (CREATE/UPDATE/DELETE) и интеграцию с soft deletes, транзакциями, логгингом (laravel-logger) и моделями (laravel-soft-model-base).

## Features
- **Builder-based config**: Цепочная настройка слоев через builders (e.g. `RequestBuilder::make()->addCreateTag(...)`).
- **DTO-oriented**: Все слои используют DTO для передачи config (e.g. `CrudlerRequestDTO`, `CrudlerServiceDTO`).
- **Modular layers**: Независимая настройка для каждого слоя с fallback на дефолты.
- **Hooks & Extensions**: Pre/post hooks в Action, unique checks в Repository, теги в Request.
- **Integration**: Автоматическая инъекция DTO в контроллеры через `CrudlerRoute`, поддержка пагинации, caching, joins.
- **Extensibility**: Наследуйте `CrudlerConfig` для модульных config (implements `ICoreCrudler` с статическими методами как `BASE_POLICY_CRUDLER`).

## Installation

### Requirements
- PHP ^8.2
- Laravel ^10.10 | ^11.0 | ^12.0
- Dependencies: makaveli/laravel-logger, makaveli/laravel-soft-model-base, makaveli/laravel-core (устанавливаются автоматически)

### Steps
1. Установите пакет через Composer:
   ```
   composer require makaveli/laravel-crudler
   ```

2. Провайдер (`CrudlerServiceProvider`) регистрируется автоматически (через `extra.laravel.providers` в composer.json).

3. Опубликуйте конфиг (если нужно кастомизировать, e.g. crudler.php с настройками):
   ```
   php artisan vendor:publish --tag=crudler-config
   ```

## Configuration
После установки проверьте `config/crudler.php` (если опубликован) — там дефолтные настройки (e.g. paths для builders/DTO). Основная config — в ваших модульных классах (e.g. MyModuleCrudler extends CrudlerConfig), где переопределяете статические методы из `ICoreCrudler` (e.g. `BASE_POLICY_CRUDLER`, `FULL_SERVICE_CRUDLER`).

Для моделей используйте `SoftModel` trait из laravel-soft-model-base для soft deletes/support.

## Usage
1. **Создайте config-класс**: Реализует интерфейс `ICrudlerConfig` через builders.
   ```php
   <?php

   namespace App\Modules\MyModule\Crudler;

   use Crudler\Interfaces\ICrudlerConfig;
   use Crudler\Requests\Builders\RequestBuilder;
   use Crudler\Requests\DTO\CrudlerRequestDTO;

   class MyModuleCrudler implements ICrudlerConfig {
       public static function BASE_REQUEST_CRUDLER(...$args): ?CrudlerRequestDTO
       {
           return RequestBuilder::make()
               ->addCreateTag('create', ['name' => 'required'])
               ->build();
       }

       // Другие слои: BASE_POLICY_CRUDLER, FULL_SERVICE_CRUDLER и т.д.
   }
   ```

2. **Зарегистрируйте роуты**: Используйте `CrudlerRoute` с fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER() для инъекции `CrudlerControllerDTO`.
   ```php
   use App\Controllers\API\MyModuleController;
   use App\Modules\MyModule\Crudler\MyModuleCrudler;
   
   use Crudler\Routing\CrudlerRoute;

   CrudlerRoute::post('/items', [MyModuleController::class, 'crudlerCreate', fn() => MyModuleCrudler::FULL_CONTROLLER_CRUDLER()]);
   ```

3. **Создайте контроллер**: Наследуйте от `CrudlerController`.
   ```php
   <?php

   namespace App\Controllers\API;

   use Crudler\Controllers\Core\CrudlerController;

   class MyModuleController extends CrudlerController {
       // Методы как crudlerCreate(CrudlerControllerDTO $dto) уже готовы
   }
   ```

4. **Вызов в коде**: В контроллере crudler-методы автоматически используют DTO для action/resource (e.g. crudlerCreate вызывает action->crudlerCreate и resource->resource).

Подробные примеры в документации слоев ниже.

## Layers
Crudler разделён на слои, каждый с собственной документацией (см. docs/ или отдельные файлы в repo):
- **[Policy](./src/Policies/README.md)**: Дополнительная проверка разрешений на действия с моделью (abilities как 'can_view') через `PolicyBuilder` и `CrudlerPolicyResolver`.
- **[Request](./src/Requests/README.md)**: Валидация с тегами/контекстами через `RequestBuilder` и `CrudlerRequest`.
- **[Resource](./src/Resources/README.md)**: Форматирование JSON через `ResourceBuilder` и `CrudlerResource`.
- **[Repository](./src/Repositories/README.md)**: Чтение данных (getAll/find/paginate) через `RepositoryBuilder` и `CrudlerRepository`.
- **[Service](./src/Services/README.md)**: Мутации (create/update/delete) через `ServiceBuilder` и `CrudlerService`.
- **[Action](./src/Actions/README.md)**: Бизнес-действия с hooks через `ActionBuilder` и `CrudlerAction`.
- **[Controller](./src/Controllers/README.md)**: Обработка запросов через `ControllerBuilder` и `CrudlerController`.
- **[Router](./src/Router/README.md)**: Регистрация роутов с DTO-инъекцией через `CrudlerRoute`.

Каждый слой настраивается в config-классе (e.g. BASE_POLICY_CRUDLER возвращает DTO из builder->build()).

## License
MIT License. See [LICENSE](LICENSE) for details.
