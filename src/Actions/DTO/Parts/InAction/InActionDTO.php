<?php

namespace Crudler\Actions\DTO\Parts\InAction;

use Crudler\Actions\Interfaces\{
    IBeforeAction,
    IAfterAction,
    IReturn
};

class InActionDTO
{
    public function __construct(
        public readonly ?IBeforeAction $beforeAction = null,
        public readonly ?IAfterAction $afterAction = null,
        public readonly ?IReturn $return = null,
    ) {}
}
