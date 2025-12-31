<?php

namespace Crudler\Repositories\Interfaces;

use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;
use Core\DTO\FormDTO;

interface IUniqueItemCallable {
    /**
     * Summary of __invoke
     *
     * @param FormDTO $formDTO
     *
     * @return array<string, RepositoryUniqueItemDTO>
     */
    public function __invoke(FormDTO $formDTO): array;
}
