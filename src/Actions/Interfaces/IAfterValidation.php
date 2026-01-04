<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;

use Illuminate\Database\Eloquent\Model;

interface IAfterValidation
{
    public function __invoke(
        BeforeValidationResult $result,
        Model|array|null $data = null
    ): AfterValidationResult;
}
