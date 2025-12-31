<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\Interfaces\IOnceFormCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ControllerDeleteDTO
{
    /**
     * Summary of __construct
     *
     * @param ?IOnceFormCallableDTO $onceDTO = null
     * @param ?string $requestTag = 'delete'
     * @param Request|FormRequest|CrudlerRequestDTO|null $request = null
     * @param ?string $successMessage = null
     */
    public function __construct(
        public readonly ?IOnceFormCallableDTO $onceDTO = null,
        public readonly ?string $requestTag = 'delete',
        public readonly Request|FormRequest|CrudlerRequestDTO|null $request = null,
        public readonly ?string $successMessage = null,
    ) {
        $this->successMessage ??= config('crudler.controllers.success_delete_message');
    }
}
