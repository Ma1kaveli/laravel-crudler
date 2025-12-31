<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;

interface IBeforeAction
{
    public function __invoke(AfterWithValidationResult $result): BeforeActionResult;
}
