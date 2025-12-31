<?php

namespace Crudler\Repositories\Interfaces;

use Crudler\Actions\DTO\CrudlerActionDTO;

use Core\DTO\FormDTO;

interface IActionFunction
{
    public function __invoke(FormDTO $dto, ...$args): CrudlerActionDTO;
}
