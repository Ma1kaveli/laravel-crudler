<?php

namespace Crudler\Repositories\DTO;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;

class CrudlerRepositoryDTO
{
    public function __construct(
        public readonly ?RepositoryUniqueDTO $uniqueDTO = null,
        public readonly ?RepositoryShowOnceDTO $showOnceDTO = null
    ) {}

    /**
     * Summary of start
     *
     * @param ?RepositoryUniqueDTO $uniqueDTO = null
     * @param ?RepositoryShowOnceDTO $showOnceDTO = null
     *
     * @return static
     */
    public static function start(
        ?RepositoryUniqueDTO $uniqueDTO = null,
        ?RepositoryShowOnceDTO $showOnceDTO = null,
    ): static {
        return new self(
            uniqueDTO: $uniqueDTO,
            showOnceDTO: $showOnceDTO,
        );
    }
}
