<?php

namespace Crudler\Services\DTO\Parts;

use Closure;

class ServiceMapperFieldDTO
{
    /** @var Closure|string */
    public readonly Closure|string $value;

    /** @var string|int */
    public readonly string|int $key;

    public function __construct(string|callable $raw, string|int $key)
    {
        $this->key = $key;

        if ($raw instanceof Closure) {
            $this->value = $raw;
            return;
        }

        if (is_callable($raw)) {
            $this->value = Closure::fromCallable($raw);
            return;
        }

        if (is_string($raw)) {
            $this->value = $raw;
            return;
        }

        throw new \InvalidArgumentException('Invalid type for _CrudlerServiceMapperFieldDTO value');
    }

    /**
     * Summary of isSimple
     *
     * @return bool
     */
    public function isSimple(): bool {
        return is_int($this->key) && is_string($this->value);
    }

    /**
     * Summary of isCallable
     *
     * @return bool
     */
    public function isCallable(): bool {
        return is_string($this->key) && (is_callable($this->value) || $this->value instanceof Closure);
    }
}
