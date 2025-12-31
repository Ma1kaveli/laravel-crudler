<?php

namespace Crudler\Resources\Core;

use Core\Resources\BaseResource;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;

use Closure;
use Exception;

/**
 * Class CoreCrudlerResource
 *
 * Расширяет базовый Laravel Resource и добавляет динамическую генерацию полей
 * на основе DTO-конфигурации из Crudler.
 *
 * Основные возможности:
 * ---------------------
 * • Динамические методы (DTO->methods)
 * • Дополнительные поля (DTO->additionalData) — вычисляются лениво (через Closure)
 * • Основные поля (DTO->data) — вычисляются один раз в конструкторе
 * • Поддержка различных типов значений:
 *      - string  → берётся поле из $this[$string]
 *      - Closure → выполняется с аргументом ($this)
 *      - callable → конвертируется в Closure и выполняется
 *      - array → рекурсивно разбирается (структурные поля / вложенность)
 *
 * Пример работы:
 * --------------
 * DTO может определить поле так:
 *     'full_name' => fn(Model $m) => $m->first.' '.$m->last
 *
 * Или:
 *     ['id', 'name']  // превращается в ['id' => ..., 'name' => ...]
 *
 * Или:
 *     'settings' => ['a' => 'field1', 'b' => fn($r) => ... ]
 *
 *
 * Как работает итоговый toArray():
 * --------------------------------
 * Возвращается:
 *      1) Сгенерированный набор $this->data
 *      2) Обычные поля BaseResource
 *      3) Дополнительные динамические поля ($extraParams) — BaseResource их вычислит сам
 *
 */
class CoreCrudlerResource extends BaseResource {

    /** @var array<string, Closure> */
    private array $methods = [];

    /** @var array<string, Closure> */
    private array $extraParams;

    /** @var array */
    private array $data;

    /**
     * CoreCrudlerResource constructor.
     *
     * Подготавливает ресурс к работе:
     *   - Регистрирует динамические методы
     *   - Генерирует значения основных полей (DTO->data)
     *   - Регистрирует дополнительные ленивые поля (DTO->additionalData)
     *
     * @param mixed $resource  Исходная модель / сущность
     * @param CrudlerResourceGeneratorDTO $dto  Конфигурация Crudler
     */
    public function __construct($resource, CrudlerResourceGeneratorDTO $dto) {
        $this->setMethods($dto);

        $additionalFields = $this->getAdditionalFields($dto);
        $additionalFieldNames = array_keys($additionalFields);

        // Сначала вызываем parent, чтобы установить $this->resource
        parent::__construct($resource, $additionalFieldNames);

        // Теперь вычисляем основные данные, когда resource доступен
        $data = $this->getToArrayData($dto);

        $this->extraParams = $additionalFields;
        $this->data = $data;
    }

    /**
     * Вызывает динамический метод, определённый в DTO.
     *
     * @param string $name  Имя метода
     * @param mixed ...$args  Аргументы вызова
     *
     * @throws \Exception Если метода нет
     * @return mixed
     */
    public function call(string $name, ...$args): mixed {
        if (!isset($this->methods[$name])) {
            throw new \Exception("CrudlerResource: method '$name' not found");
        }

        return ($this->methods[$name])($this, ...$args);
    }

    /**
     * Универсальное разрешение значения поля.
     *
     * Поддерживает 4 типа данных:
     *   - Closure: выполняется с аргументом ($this)
     *   - callable: преобразуется в Closure и выполняется
     *   - string: интерпретируется как имя поля модели → $this[$string]
     *   - array: разбирается рекурсивно (см. resolveArrayValue)
     *
     * @param Closure|string|array|callable $value
     * @return mixed
     */
    private function resolveValue(Closure|string|array|callable $value): mixed
    {
        return match (true) {
            $value instanceof Closure        => $value($this),
            is_callable($value)       => Closure::fromCallable($value)($this),
            is_string($value)         => $this->resource
                                                    ? ($this->resource[$value] ?? $this->resource->$value ?? null)
                                                    : null,
            is_array($value)          => $this->resolveArrayValue($value),
            default                          => $value,
        };
    }

    /**
     * Разрешение вложенной структуры массива.
     *
     * Особые случаи:
     *   - [ "id", "name" ]
     *       → ["id" => $this["id"], "name" => $this["name"]]
     *
     *   - ассоциативные массивы
     *       → значения обрабатываются через resolveValue()
     *
     * Работает рекурсивно.
     *
     * @param array $value
     * @return array
     */
    private function resolveArrayValue(array $value): array
    {
        $out = [];

        foreach ($value as $k => $v) {
            // [ "id", "name" ] → ["id" => $this["id"], "name" => $this["name"] ]
            if (is_int($k) && is_string($v)) {
                $out[$v] = $this[$v];
                continue;
            }

            $out[$k] = $this->resolveValue($v);
        }

        return $out;
    }


    /**
     * Генерирует основной набор данных (DTO->data),
     * который будет добавлен в финальный toArray() один раз.
     *
     * Поля вычисляются немедленно и их значение фиксируется.
     *
     * Особенность:
     *   — Если ключ в DTO числовой, то значение интерпретируется как string-поле модели.
     *
     * @param CrudlerResourceGeneratorDTO $dto
     * @return array
     */
    private function getToArrayData(CrudlerResourceGeneratorDTO $dto): array {
        $result = [];

        foreach ($dto->data as $e) {
            $value = $e->value;

            // We can't add callable with int key
            //  so if the key is integer = value is string
            if (is_int($e->key)) {
                $result[$value] = $this->resolveValue($value);
                continue;
            }

            $result[$e->key] = $this->resolveValue($value);
        }

        return $result;
    }

    /**
     * Создаёт дополнительные поля для BaseResource,
     * возвращая замыкания, которые будут вычисляться лениво.
     *
     * То есть каждое поле — это fn() => resolveValue(...)
     *
     * Поддерживает тот же формат, что и getToArrayData(),
     * но не вычисляет значения сразу.
     *
     * @param CrudlerResourceGeneratorDTO $dto
     * @return array<string, Closure>
     */
    private function getAdditionalFields(CrudlerResourceGeneratorDTO $dto): array {
        $result = [];

        foreach ($dto->additionalData as $e) {
            $value = $e->value;

            // We can't add callable with int key
            //  so if the key is integer = value is string
            if (is_int($e->key)) {
                $result[$value] = fn () => $this->resolveValue($value);
                continue;
            }

            $result[$e->key] = fn () => $this->resolveValue($value);
        }

        return $result;
    }

    /**
     * Регистрирует пользовательские методы, определённые в DTO.
     * Метод может быть вызван через $this->call().
     *
     * @param CrudlerResourceGeneratorDTO $dto
     * @return void
     */
    private function setMethods(CrudlerResourceGeneratorDTO $dto): void {
        foreach ($dto->methods as $name => $method) {
            $this->methods[$name] = $method->callback;
        }
    }

    /**
     * Возвращает дополнительные ленивые поля для BaseResource.
     *
     * @return array<string, Closure>
     */
    protected function getAdditionalData(): array
    {
        return $this->extraParams;
    }

    /**
     * Формирует полный итоговый массив данных ресурса.
     *
     * Порядок:
     *   1) Данные, вычисленные в getToArrayData()
     *   2) Поля из BaseResource (включая дополнительные ленивые)
     *
     * @param mixed $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            ...$this->data,
            ...parent::toArray($request)
        ];
    }
}
