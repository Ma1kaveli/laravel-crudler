<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

interface IAfterWithValidation
{
    public function __invoke(
        BeforeWithValidationResult $beforeWithValidationResult,
        ?AfterValidationResult $afterValidationResult = null
    ): AfterWithValidationResult;
}
