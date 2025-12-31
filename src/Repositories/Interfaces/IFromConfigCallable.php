<?php

namespace Crudler\Repositories\Interfaces;

use Core\DTO\FormDTO;

interface IFromConfigCallable {
    public function __invoke(FormDTO $formDTO): array;
}
