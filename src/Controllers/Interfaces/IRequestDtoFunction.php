<?php

namespace Crudler\Controllers\Interfaces;

use Core\DTO\FormDTO;
use Core\DTO\OnceDTO;
use Core\Interfaces\IListDTO;
use Illuminate\Http\Request;

interface IListCallableDTO {
    public function __invoke(Request $request): IListDTO;
}

interface IShowCallableDTO {
    public function __invoke(Request $request, int $id): OnceDTO;
}

interface ICreateCallableDTO {
    public function __invoke(Request $request): FormDTO;
}

interface IUpdateCallableDTO {
    public function __invoke(Request $request, int $id): FormDTO;
}

interface IOnceFormCallableDTO {
    public function __invoke(Request $request, int $id): OnceDTO;
}
