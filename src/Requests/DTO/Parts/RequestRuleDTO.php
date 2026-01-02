<?php

namespace Crudler\Requests\DTO\Parts;

use Closure;

class RequestRuleDTO
{
    public readonly string|object $value;

    public function __construct(string|object|callable $raw)
    {
        if (is_callable($raw)) {
            $this->value = $raw instanceof Closure
                ? $raw
                : Closure::fromCallable($raw);
            return;
        }

        if (is_string($raw)) {
            $this->value = $raw;
            return;
        }

        if (is_object($raw)) {
            $this->value = $raw;
            return;
        }

        throw new \InvalidArgumentException('Invalid type for _CrudlerRequestRuleDTO value');
    }

    /**
     * Summary of start
     *
     * @param string|object|callable $raw
     * 
     * @return RequestRuleDTO
     */
    public static function start(string|object|callable $raw): self
    {
        return new self(
            raw: $raw
        );
    }
}
