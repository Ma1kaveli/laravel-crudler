<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

use Illuminate\Database\Eloquent\Model;

interface IBeforeValidation
{
    public function __invoke(
        BeforeWithValidationResult $result,
        Model|array|null $data = null
    ): BeforeValidationResult;
}
