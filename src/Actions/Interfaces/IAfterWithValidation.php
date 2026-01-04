<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

use Illuminate\Database\Eloquent\Model;

interface IAfterWithValidation
{
    public function __invoke(
        BeforeWithValidationResult $beforeWithValidationResult,
        ?AfterValidationResult $afterValidationResult = null,
        Model|array|null $data = null
    ): AfterWithValidationResult;
}
