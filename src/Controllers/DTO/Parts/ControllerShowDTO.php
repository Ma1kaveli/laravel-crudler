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
}
