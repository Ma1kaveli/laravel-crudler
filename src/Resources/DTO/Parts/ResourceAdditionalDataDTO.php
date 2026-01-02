<?php

namespace Crudler\Resources\DTO\Parts;

use Closure;

class ResourceAdditionalDataDTO
{
    /** @var Closure|string */
    public readonly Closure|string $value;

    /** @var string|int|null */
    public readonly string|int|null $key;

    public function __construct(string|callable $raw, string|int|null $key = null)
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

        throw new \InvalidArgumentException('Invalid type for ResourceAdditionalDataDTO value');
    }

    /**
     * Summary of start
     *
     * @param string|callable $value
     * @param string|int|null $key = null
     *
     * @return ResourceAdditionalDataDTO
     */
    public static function start(string|callable $value, string|int|null $key = null): self {
        return new self(
            raw: $value,
            key: $key
        );
    }
}
