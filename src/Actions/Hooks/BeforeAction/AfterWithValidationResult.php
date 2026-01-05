<?php

namespace Crudler\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;

/**
 * @template TBeforeResult
 * @template TAfterResult
 * @template TResult
 */
class AfterWithValidationResult
{
    public function __construct(
        public readonly FormDTO $formDTO,

        /** @var BeforeWithValidationResult<TBeforeResult> */
        public readonly BeforeWithValidationResult $beforeWithValidationResult,

        /** @var AfterValidationResult<TAfterResult>|null */
        public readonly ?AfterValidationResult $afterValidationResult = null,

        /** @var TResult|null */
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
