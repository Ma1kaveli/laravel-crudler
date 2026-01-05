<?php

namespace Crudler\Adapters;

use Closure;

abstract class ClosureHookAdapter
{
    protected Closure $closure;

    public function __construct(callable $closure)
    {
        $this->closure = $closure instanceof Closure
            ? $closure
            : Closure::fromCallable($closure);
    }
}
