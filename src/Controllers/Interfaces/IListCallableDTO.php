<?php

namespace Crudler\Controllers\Interfaces;

use Core\Interfaces\IListDTO;
use Illuminate\Http\Request;

interface IListCallableDTO {
    public function __invoke(Request $request): IListDTO;
}
