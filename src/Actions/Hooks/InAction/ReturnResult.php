<?php

namespace Crudler\Actions\Hooks\InAction;

use Core\DTO\FormDTO;

class ReturnResult
{
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly BeforeActionResult $previous,
        public readonly mixed $result = null
    ) {}

    public static function create(
        FormDTO $formDTO,
        BeforeActionResult $previous,
        mixed $result = null
    ): self {
        return new self(
            formDTO: $formDTO,
            previous: $previous,
            result: $result
        );
    }
}
