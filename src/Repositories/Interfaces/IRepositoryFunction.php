<?php

namespace Crudler\Repositories\Interfaces;

use Crudler\Repositories\DTO\CrudlerRepositoryDTO;

use Core\DTO\FormDTO;

interface IRepositoryFunction
{
    public function __invoke(FormDTO $dto, ...$args): CrudlerRepositoryDTO;
}
