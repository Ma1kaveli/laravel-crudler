<?php

namespace Crudler\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;

class BeforeValidationResult {
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly BeforeWithValidationResult $previous,
        public readonly mixed $result = null,
    ) {}

    public static function create(
        FormDTO $formDTO,
        BeforeWithValidationResult $previous,
        mixed $result = null
    ): self {
        return new self(
            formDTO: $formDTO,
            previous: $previous,
            result: $result
        );
    }
}
