<?php

namespace Crudler\Repositories\DTO\Parts\Unique;

use Closure;

class RepositoryUniqueConfigDTO
{
    public readonly mixed $column;

    public readonly Closure|null $modifier;

    public readonly bool $isOrWhere;

    /**
     * Summary of __construct
     *
     * @param mixed $column - for example \DB::raw('LOWER(name)')
     * @param callable|null $modifier - for example fn($v) => trim(strtolower($v))
     * @param bool $isOrWhere
     *
     * @return void
     */
    public function __construct(
        mixed $column,
        ?callable $modifier = null,
        bool $isOrWhere = false
    ) {
        $this->column = $column;
        $this->isOrWhere = $isOrWhere;

        if (is_callable($modifier)) {
            $this->modifier = $modifier instanceof Closure
                ? $modifier
                : Closure::fromCallable($modifier);
            return;
        }

        $this->modifier = $modifier;
    }
}
