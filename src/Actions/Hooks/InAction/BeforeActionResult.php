<?php

namespace Crudler\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;

class BeforeActionResult {
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly AfterWithValidationResult $previous,
        public readonly mixed $result = null
    ) {}

    public static function create(
        FormDTO $formDTO,
        AfterWithValidationResult $previous,
        mixed $result = null
    ): self {
        return new self(
            formDTO: $formDTO,
            previous: $previous,
            result: $result
        );
    }
}
