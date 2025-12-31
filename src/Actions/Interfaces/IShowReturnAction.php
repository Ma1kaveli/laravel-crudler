<?php

namespace Crudler\Actions\Interfaces;

use Core\DTO\OnceDTO;
use Illuminate\Database\Eloquent\Model;

interface IShowReturnAction
{
    public function __invoke(OnceDTO $dto, Model $data, mixed $afterResult = null): mixed;
}
