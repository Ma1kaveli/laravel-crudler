<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;

use Illuminate\Database\Eloquent\Model;

interface IBeforeAction
{
    public function __invoke(AfterWithValidationResult $result, Model|array|null $data = null): BeforeActionResult;
}
