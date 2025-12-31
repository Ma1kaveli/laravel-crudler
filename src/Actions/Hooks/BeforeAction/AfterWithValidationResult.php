<?php

namespace Crudler\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;

class AfterWithValidationResult
{
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly BeforeWithValidationResult $beforeWithValidationResult,
        public readonly ?AfterValidationResult $afterValidationResult = null,
        public readonly mixed $result = null,
    ) {}

    public static function create(
        FormDTO $formDTO,
        BeforeWithValidationResult $beforeWithValidationResult,
        ?AfterValidationResult $afterValidationResult = null,
        mixed $result = null
    ): self {
        return new self(
            formDTO: $formDTO,
            beforeWithValidationResult: $beforeWithValidationResult,
            afterValidationResult: $afterValidationResult,
            result: $result
        );
    }
}
