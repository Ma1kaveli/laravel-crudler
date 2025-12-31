<?php

namespace Crudler\Policies\Interfaces;

use Core\DTO\FormDTO;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface IRuleCallable
{
    public function __invoke(FormDTO $dto, ?Model $data): Exception|true;
}
