<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\InAction\AfterActionResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;
use Illuminate\Database\Eloquent\Model;

interface IAfterAction
{
    public function __invoke(BeforeActionResult $result, Model|array $data): AfterActionResult;
}
