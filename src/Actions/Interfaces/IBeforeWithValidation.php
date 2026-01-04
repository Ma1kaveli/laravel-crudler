<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;

interface IBeforeWithValidation
{
    public function __invoke(
        FormDTO $dto,
        Model|array|null $data = null
    ): BeforeWithValidationResult;
}
