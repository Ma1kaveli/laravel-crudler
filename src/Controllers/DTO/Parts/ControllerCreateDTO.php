<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\Interfaces\ICreateCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Core\DTO\FormDTO;
use Illuminate\Foundation\Http\FormRequest;

class ControllerCreateDTO
{
    /**
     * Summary of __construct
     *
     * @param ICreateCallableDTO|FormDTO $formDTO
     * @param FormRequest|CrudlerRequestDTO $request
     * @param ?string $requestTag = 'create
     * @param array $additionalData = []
     * @param ?string $successMessage = null
     */
    public function __construct(
        public readonly ICreateCallableDTO|FormDTO $formDTO,
        public readonly FormRequest|CrudlerRequestDTO $request,
        public readonly ?string $requestTag = 'create',
        public readonly array $additionalData = [],
        public readonly ?string $successMessage = null,
    ) {
        $this->successMessage ??= config('crudler.controllers.success_create_message');
    }
}
