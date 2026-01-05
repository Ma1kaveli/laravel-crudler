<?php

namespace Crudler\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TResult
 * @template TData of Model|array|null
 */
class AfterActionResult
{
    /**
     * @param TData $data
     */
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly BeforeActionResult $previous,
        public readonly Model|array|null $data = null,

        /** @var TResult|null */
        public readonly mixed $result = null,
    ) {}

    public static function create(
        FormDTO $formDTO,
        BeforeActionResult $previous,
        Model|array|null $data = null,
        mixed $result = null
    ): self {
        return new self(
            formDTO: $formDTO,
            previous: $previous,
            data: $data,
            result: $result
        );
    }
}
