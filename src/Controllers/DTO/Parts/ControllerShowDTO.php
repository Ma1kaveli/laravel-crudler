<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\Interfaces\IShowCallableDTO;

class ControllerShowDTO
{
    /**
     * Summary of __construct
     *
     * @param ?IShowCallableDTO $showDTO = null
     * @param array<mixed, string> $additionalData = []
     */
    public function __construct(
        public readonly ?IShowCallableDTO $showDTO = null,
        public readonly array $additionalData = [],
    ) {}

    /**
     * Summary of start
     *
     * @param ?IShowCallableDTO $showDTO = null
     * @param array $additionalData = []
     *
     * @return ControllerShowDTO
     */
    public static function start(
        ?IShowCallableDTO $showDTO = null,
        array $additionalData = [],
    ): self {
        return new self(
            showDTO: $showDTO,
            additionalData: $additionalData
        );
    }
}
