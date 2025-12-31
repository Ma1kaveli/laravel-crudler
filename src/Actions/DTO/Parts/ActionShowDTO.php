<?php

namespace Crudler\Actions\DTO\Parts;

use Core\DTO\OnceDTO;
use Crudler\Actions\Interfaces\{IShowAfterAction, IShowReturnAction};

class ActionShowDTO
{
    public function __construct(
        public readonly OnceDTO $onceDTO,
        public readonly ?IShowAfterAction $after = null,
        public readonly ?IShowReturnAction $return = null,
    ) {}

    /**
     * Summary of start
     *
     * @param OnceDTO $onceDTO
     * @param ?IShowAfterAction $after = null
     * @param ?IShowReturnAction $return = null
     *
     * @return self
     */
    public static function start(
        OnceDTO $onceDTO,
        ?IShowAfterAction $after = null,
        ?IShowReturnAction $return = null,
    ): self {
        return new self(
            onceDTO: $onceDTO,
            after: $after,
            return: $return
        );
    }
}
