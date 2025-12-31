<?php

namespace Crudler\Actions\DTO\Parts\BeforeAction;

use Crudler\Actions\Interfaces\{
    IBeforeWithValidation,
    IBeforeValidation,
    IAfterValidation,
    IAfterWithValidation
};

class BeforeActionDTO
{
    public function __construct(
        public readonly ?IBeforeWithValidation $beforeWithValidation = null,
        public readonly ?IBeforeValidation $beforeValidation = null,
        public readonly ?IAfterValidation $afterValidation = null,
        public readonly ?IAfterWithValidation $afterWithValidation = null,
    ){}
}
