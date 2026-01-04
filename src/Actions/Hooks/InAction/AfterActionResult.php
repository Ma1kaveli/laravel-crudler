<?php

namespace Crudler\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;

class AfterActionResult
{
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly BeforeActionResult $previous,
        public readonly Model|array|null $data = null,
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
