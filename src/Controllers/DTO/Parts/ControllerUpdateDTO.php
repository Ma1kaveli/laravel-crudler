<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\Interfaces\IUpdateCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Core\DTO\FormDTO;
use Illuminate\Foundation\Http\FormRequest;

class ControllerUpdateDTO
{
    /**
     * Summary of __construct
     *
     * @param IUpdateCallableDTO|FormDTO $formDTO
     * @param FormRequest|CrudlerRequestDTO $request
     * @param ?string $requestTag = 'update'
     * @param array $additionalData = []
     * @param ?FormRequest $request = null
     * @param ?string $successMessage = null
     */
    public function __construct(
        public readonly IUpdateCallableDTO|FormDTO $formDTO,
        public readonly FormRequest|CrudlerRequestDTO $request,
        public readonly ?string $requestTag = 'update',
        public readonly array $additionalData = [],
        public readonly ?string $successMessage = null,
    ) {
        $this->successMessage ??= config('crudler.controllers.success_update_message');
    }
}
