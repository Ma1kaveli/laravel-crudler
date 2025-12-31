<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\InAction\AfterActionResult;
use Crudler\Actions\Hooks\InAction\ReturnResult;

interface IReturn
{
    public function __invoke(AfterActionResult $result): ReturnResult;
}
