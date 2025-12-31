<?php

namespace Crudler\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;

class BeforeWithValidationResult
{
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly mixed $result = null,
    ) {}

    public static function create(FormDTO $formDTO, mixed $result = null): self
    {
        return new self(
            formDTO: $formDTO,
            result: $result
        );
    }
}
