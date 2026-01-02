<?php

namespace Crudler\Controllers\Interfaces;

use Core\DTO\FormDTO;
use Illuminate\Http\Request;

interface IUpdateCallableDTO {
    public function __invoke(Request $request, int $id): FormDTO;
}
