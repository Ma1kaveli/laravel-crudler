<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\DTO\Parts\List\ListConfigDTO;
use Crudler\Controllers\Interfaces\IListCallableDTO;

use Core\Interfaces\IListDTO;

class ControllerListDTO
{
    /**
     * Summary of __construct
     *
     * @param IListCallableDTO|IListDTO|ListConfigDTO|null $dto
     * @param bool $isGetAll = false
     * @param bool $isPaginateResponse = true
     * @param array<mixed, string> $additionalData = []
     *
     * @throws \Exception
     */
    public function __construct(
        public readonly IListCallableDTO|IListDTO|ListConfigDTO|null $dto = null,
        public readonly bool $isGetAll = false,
        public readonly bool $isPaginateResponse = true,
        public readonly array $additionalData = [],
    ) {}
}
