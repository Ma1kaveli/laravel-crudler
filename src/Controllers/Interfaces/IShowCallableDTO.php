<?php

namespace Crudler\Controllers\Interfaces;

use Core\DTO\OnceDTO;
use Illuminate\Http\Request;

interface IShowCallableDTO {
    public function __invoke(Request $request, int $id): OnceDTO;
}
