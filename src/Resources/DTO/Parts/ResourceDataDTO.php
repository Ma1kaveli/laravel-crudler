<?php

namespace Crudler\Resources\DTO\Parts;

use Closure;

class ResourceDataDTO
{
    /** @var Closure|string|array|null */
    public readonly Closure|string|array|null $value;

    /** @var string|int|null */
    public readonly string|int|null $key;

    public function __construct(string|array|callable $raw, string|int|null $key = null)
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

        if (is_array($raw)) {
            $this->value = $raw;
            return;
        }

        throw new \InvalidArgumentException('Invalid type for ResourceDataDTO value');
    }

    /**
     * Summary of start
     *
     * @param string|array|callable $value
     * @param string|int|null $key = null
     *
     * @return ResourceDataDTO
     */
    public static function start(string|array|callable $value, string|int|null $key = null): self {
        return new self(
            raw: $value,
            key: $key
        );
    }
}
