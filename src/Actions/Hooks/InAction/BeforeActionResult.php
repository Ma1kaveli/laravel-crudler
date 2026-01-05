<?php

namespace Crudler\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;

/**
 * @template TResult
 * @template TPreviousResult
 */
class BeforeActionResult {
    public function __construct(
        public readonly FormDTO $formDTO,

        /** @var AfterWithValidationResult<TPreviousResult, mixed, mixed> */
        public readonly AfterWithValidationResult $previous,

        /** @var TResult|null */
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
