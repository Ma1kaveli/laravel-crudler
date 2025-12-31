<?php

namespace Crudler\Repositories\Interfaces;

use Core\DTO\FormDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceConfigDTO;

interface IShowOnceByIdCallable {
    public function __invoke(FormDTO $formDTO): RepositoryShowOnceConfigDTO;
}
