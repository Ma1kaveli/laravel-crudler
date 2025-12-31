### Request Layer

Request в Crudler — это слой для валидации входных данных HTTP-запросов. Он расширяет возможности Laravel FormRequest, добавляя поддержку тегов (для группировки правил), контекстов (CREATE, UPDATE, DELETE) и динамической генерации экземпляров FormRequest. Это позволяет создавать один объект `CrudlerRequest`, который генерирует разные FormRequest на основе тега (например, для разных эндпоинтов или действий).

Поддерживаемые варианты:
- **С контекстами** (по умолчанию): Правила комбинируются (общие + специфические для create/update/delete). Использует `BaseCrudlerFormRequest`.
- **Simple** (`isBase = true`): Без контекстов, только общие правила. Использует `SimpleCrudlerFormRequest`.
- **Existing class**: Прокидывает существующий класс FormRequest без дополнительных конфигов.

Request не требует ручной валидации — правила и сообщения определяются в Builder. Если тег не найден, бросается исключение. Конфигурация преобразуется в `CrudlerRequestDTO`, который используется для создания `CrudlerRequest`. Этот класс имеет метод `make(string $tag, ?Request $rawRequest = null)` для генерации FormRequest.

#### Использование через Builder
Настройка происходит в статическом методе конфигурации `BASE_REQUEST_CRUDLER` (определённом в интерфейсе `ICoreCrudler`). Используйте `RequestBuilder::make()` для цепочной настройки тегов. Builder поддерживает:
- `addCreateTag(string $name, array $rules, array $messages = [], array $createRules = [], array $createMessages = [], bool $isBase = false, bool $authorize = true)`: Тег для создания (фокус на create-контексте).
- `addUpdateTag(string $name, array $rules, array $messages = [], array $updateRules = [], array $updateMessages = [], bool $isBase = false, bool $authorize = true)`: Тег для обновления (фокус на update-контексте).
- `addDeleteTag(string $name, array $rules, array $messages = [], array $deleteRules = [], array $deleteMessages = [], bool $isBase = false, bool $authorize = true)`: Тег для удаления (фокус на delete-контексте).
- `addRequestTag(string $name, array $rules, array $messages = [], array $createRules = [], array $updateRules = [], array $deleteRules = [], array $createMessages = [], array $updateMessages = [], array $deleteMessages = [], bool $isBase = false, bool $authorize = true)`: Универсальный тег с поддержкой всех контекстов.
- `addExistingTag(string $name, string $class)`: Тег для существующего класса FormRequest.
- `fromConfig(array $config)`: Загружает конфигурацию из массива (опционально, для bulk-настройки). Массив вида `['tag_name' => ['rules' => [...], 'create_rules' => [...], 'class' => '...', 'is_base' => bool, 'authorize' => bool]]`.

После настройки вызовите `build()`, чтобы получить `CrudlerRequestDTO`. Если слой не нужен, верните `null`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use App\Modules\MyModule\Requests\ExistingFormRequest; // Пример существующего класса

use Crudler\Interfaces\ICrudlerConfig;
use Crudler\Requests\Builders\RequestBuilder;
use Crudler\Requests\DTO\CrudlerRequestDTO;

class MyModuleCrudler implements ICrudlerConfig {
    public static function BASE_REQUEST_CRUDLER(...$args): ?CrudlerRequestDTO
    {
        return RequestBuilder::make()
            ->addCreateTag(
                'tag_create',
                ['name' => ['required', 'string', 'max:255']], // Общие правила
                ['name.required' => 'Name is required'], // Общие сообщения
                ['age' => ['integer', 'min:18']], // Специфические для create
                ['age.min' => 'Must be at least 18'], // Сообщения для create
                false, // isBase
                true // authorize
            )
            ->addUpdateTag(
                'tag_update',
                ['name' => ['sometimes', 'string', 'max:255']],
                [],
                ['age' => ['sometimes', 'integer', 'min:0']],
                [],
                true, // isBase (simple режим)
                false // unauthorize
            )
            ->addDeleteTag(
                'tag_delete',
                ['reason' => ['required', 'string']],
                ['reason.required' => 'Reason is required'],
                ['force' => ['boolean']],
                ['force.boolean' => 'Force must be boolean'],
                false,
                true
            )
            // Указанные выше методы (addCreateTag, addUpdateTag, addDeleteTag) можно заменить на метод addRequestTag
            ->addRequestTag(
                'base',
                ['name' => ['required', 'string', 'max:255']], // Общие правила
                ['name.required' => 'Name is required'], // Общие сообщения
                ['age' => ['integer', 'min:18']], // Специфические для create
                ['age' => ['sometimes', 'integer', 'min:0']], // Специфические для update
                ['force' => ['boolean']], // Специфические для delete
                ['age.min' => 'Must be at least 18'], // Сообщения для create
                [], // Сообщения для update
                ['force.boolean' => 'Force must be boolean'], // Сообщения для delete
                false, // isBase
                true // authorize
            )
            ->addExistingTag('tag_existing', ExistingFormRequest::class)
            // Опционально: загрузка из массива конфига
            // ->fromConfig(['tag_export' => ['rules' => ['format' => 'required'], 'authorize' => false]])
            ->build();
    }
}
```

В этом примере:
- `tag_create`: С контекстами, фокус на create, с авторизацией.
- `tag_update`: Simple (isBase), без обязательной авторизации.
- `tag_delete`: С контекстами, требует причину удаления.
- `base`:  При передаче использование данного тэга Request сам определяет какие правила брать исходя из метода запроса  (POST, PUT, DELETE)
- `tag_existing`: Прокидывает существующий класс FormRequest.

#### Как использовать Request в коде
1. Получите DTO из конфига: `$requestDto = MyModuleCrudler::BASE_REQUEST_CRUDLER();`.
2. Создайте CrudlerRequest: `$crudlerRequest = new CrudlerRequest($requestDto);`.
3. Сгенерируйте FormRequest: `$formRequest = $crudlerRequest->make('tag_create', $rawRequest);` (опционально инжектируйте сырой Request для тестов).
   - В контроллере инжектируйте как зависимость: `public function store(FormRequest $request)` (Laravel автоматически валидирует).
   - Для контекстов: Правила применяются автоматически по HTTP-методу (POST → CREATE, PUT/PATCH → UPDATE, DELETE → DELETE).

**Пример в контроллере:**
```php
$requestDto = MyModuleCrudler::BASE_REQUEST_CRUDLER();
$crudlerRequest = new CrudlerRequest($requestDto)->make('tag_create')->validated();
```

#### Примечания
- **Правила и сообщения**: Массивы вида `['field' => ['rule1', 'rule2']]` или строки для простых правил. Поддержка Closure/объектов как правил.
- **Авторизация**: `authorize = false` отключает `authorize()` в FormRequest (возвращает `true` по умолчанию).
- **Ошибки**: Если тег не найден, бросается `InvalidArgumentException`.
- **Интеграция**: Используйте в роутах/контроллерах для валидации перед Actions.
- **fromConfig**: Автоматически определяет тип тега по полям в массиве (class, rules, is_base и т.д.).
