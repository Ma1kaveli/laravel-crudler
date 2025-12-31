<?php

namespace Crudler\Actions\Interfaces;

use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

use Core\DTO\FormDTO;

interface IBeforeWithValidation
{
    public function __invoke(FormDTO $dto): BeforeWithValidationResult;
}
