<?php

namespace Crudler\Controllers\Interfaces;

use Core\DTO\FormDTO;
use Illuminate\Http\Request;

interface ICreateCallableDTO {
    public function __invoke(Request $request): FormDTO;
}
