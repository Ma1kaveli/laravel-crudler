<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

interface IBeforeValidation
{
    public function __invoke(BeforeWithValidationResult $result): BeforeValidationResult;
}
