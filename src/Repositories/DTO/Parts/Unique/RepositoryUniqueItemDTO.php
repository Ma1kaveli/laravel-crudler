<?php

namespace Crudler\Repositories\DTO\Parts\Unique;

class RepositoryUniqueItemDTO
{
    public function __construct(
        public readonly string $field,
        public readonly RepositoryUniqueConfigDTO|string $config
    ) {}
}
