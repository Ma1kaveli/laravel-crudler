<?php

namespace Crudler\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;

/**
 * @template TResult
 * @template TPreviousResult
 */
class AfterValidationResult {

    public function __construct(
        public readonly FormDTO $formDTO,

        /** @var BeforeValidationResult<TPreviousResult> */
        public readonly BeforeValidationResult $previous,
        
        /** @var TResult|null */
        public readonly mixed $result = null,
    ) {}

    public static function create(
        FormDTO $formDTO,
        BeforeValidationResult $previous,
        mixed $result = null
    ): self {
        return new self(
            formDTO: $formDTO,
            previous: $previous,
            result: $result
        );
    }
}
