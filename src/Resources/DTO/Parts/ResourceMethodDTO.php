<?php

namespace Crudler\Resources\DTO\Parts;

use Closure;

class ResourceMethodDTO
{
    public readonly Closure $callback;

    public function __construct(Closure|callable $callback)
    {
        $this->callback = $callback instanceof Closure
            ? $callback
            : Closure::fromCallable($callback);
    }

    /**
     * Summary of start
     *
     * @param Closure|callable $callback
     * 
     * @return ResourceMethodDTO
     */
    public static function start(Closure|callable $callback): self {
        return new self(
            callback: $callback
        );
    }
}
