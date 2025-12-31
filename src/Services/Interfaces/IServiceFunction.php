<?php

namespace Crudler\Services\Interfaces;

use Crudler\Services\DTO\CrudlerServiceDTO;

use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;

interface IServiceFunction
{
    public function __invoke(FormDTO $dto, ?Model $data = null, ...$args): CrudlerServiceDTO;
}
