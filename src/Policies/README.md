### Policy Layer

Policy в Crudler — это слой для управления (проверкой прав доступа) на основе способностей (abilities), таких как просмотр, обновление или удаление сущностей. Он поддерживает два типа правил:
- **Callable**: Прямые функции (closures или callable), которые принимают `FormDTO $dto` и опционально `?Model $data` и возвращают `bool` (разрешено/запрещено).
- **Алиасы**: Ссылки на другие способности для повторного использования (например, 'update' может ссылаться на 'view', если права совпадают).

Policy не требует Gate или встроенных политик Laravel — это независимый слой. Если правило не определено, проверка по умолчанию проходит (`true`). Правила настраиваются через `PolicyBuilder`, который генерирует `CrudlerPolicyDTO`. Этот DTO используется для создания экземпляра `CrudlerPolicyResolver`, который выполняет проверки через метод `resolve(string $ability, FormDTO $dto, ?Model $data)`.

#### Использование через Builder
Настройка происходит в статическом методе конфигурации `BASE_POLICY_CRUDLER` (определённом в интерфейсе `ICoreCrudler`). Используйте `PolicyBuilder::make()` для цепочной настройки правил. Builder поддерживает:
- `canView(IRuleCallable|string $rule)`: Добавляет callable-правило или алиас на другое правило для правила `can_view`.
- `canUpdate(IRuleCallable|string $rule)`: Добавляет callable-правило или алиас на другое правило для правила `can_update`.
- `canDelete(IRuleCallable|string $rule)`: Добавляет callable-правило или алиас на другое правило для правила `can_delete`.
- `canRestore(IRuleCallable|string $rule)`: Добавляет callable-правило или алиас на другое правило для правила `can_restore`.
- `add(string $ability, IRuleCallable|string $rule)`: Добавляет callable-правило или алиас на другое правило.

После настройки вызовите `build()`, чтобы получить `CrudlerPolicyDTO`. Если слой не нужен, верните `null`.

**Пример в конфиге (реализация интерфейса `ICrudlerConfig`):**
```php
<?php

namespace App\Modules\MyModule\Crudler;

use Crudler\Policies\Builders\PolicyBuilder;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\Interfaces\IRuleCallable;
use Crudler\Interfaces\ICrudlerConfig;

use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Exception;

class MyModuleCrudler implements ICrudlerConfig {
    public static function BASE_POLICY_CRUDLER(...$args): ?CrudlerPolicyDTO
    {
        return PolicyBuilder::make()
            ->canView(
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        if ($dto->user->hasPermission('view_my_module')) {
                            return true;
                        }

                        throw new Exception("You don't have permission to do this!", 403);
                    }
                }
            )
            ->canUpdate(
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        if ($data && $data->user_id === $dto->user->id) {
                            return true;
                        }

                        throw new Exception("You don't have permission to do this!", 403);
                    }
                }
            )
            ->canDelete(
                'can_delete',
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        if ($dto->user->isAdmin()) {
                            return true;
                        }

                        throw new Exception("You don't have permission to do this!", 403);
                    }
                }
            )
            ->canRestore('can_delete')
            ->build();
    }

    ....
}
```

В этом примере:
- `can_view`: Проверяет наличие разрешения у пользователя.
- `can_update`: Разрешает обновление только владельцу.
- `can_delete` и `can_restore`: Только для админов.

Если нужно использовать алиасы:
```php
->add('can_export', 'can_view') // 'can_export' наследует логику от 'can_view'
```

#### Как использовать Policy в коде
1. Получите DTO из конфига: `$policyDto = MyModuleCrudler::BASE_POLICY_CRUDLER();`.
2. Создайте резолвер: `$resolver = new CrudlerPolicyResolver();`.
3. Выполните проверку: `$allowed = $resolver->resolve($policyDto, 'can_update', $dto, $model);`.
   - Если `$allowed === false`, бросьте исключение (например, `Exception(403)`).
   - Если способность не определена, возвращается `true`.

**Пример в Action или контроллере:**
```php
$policyDto = MyModuleCrudler::BASE_POLICY_CRUDLER();
$resolver = new CrudlerPolicyResolver();

if (!$resolver->resolve($policyDto, 'can_update', $dto, $model)) {
    throw new Exception('You can not do it', 403);
}

// Продолжить обновление...
```

#### Примечания
- **Ошибки**: Если алиас ссылается на несуществующее правило, бросается исключение.
- **Интеграция**: Используйте в Actions или Services перед выполнением операций (например, перед `update()`).
- **Гибкость**: Callable могут использовать любые данные из `$dto` (user, organizationId) или модели (`$data`).
- **fromConfig**: Массив вида `['ability' => callable| 'alias_target']`.
