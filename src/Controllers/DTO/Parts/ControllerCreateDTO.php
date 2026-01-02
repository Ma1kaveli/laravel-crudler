<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\Interfaces\ICreateCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Core\DTO\FormDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

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
    ) {}

    /**
     * Summary of start
     *
     * @param ICreateCallableDTO|FormDTO $formDTO
     * @param FormRequest|CrudlerRequestDTO $request
     * @param ?string $requestTag = 'create'
     * @param array $additionalData = []
     * @param ?string $successMessage = null
     *
     * @return ControllerCreateDTO
     */
    public static function start(
        ICreateCallableDTO|FormDTO $formDTO,
        FormRequest|CrudlerRequestDTO $request,
        ?string $requestTag = 'create',
        array $additionalData = [],
        ?string $successMessage = null,
    ): self {
        return new self(
            formDTO: $formDTO,
            request: $request,
            requestTag: $requestTag,
            additionalData: $additionalData,
            successMessage: $successMessage ??= Config::get('crudler.controllers.success_create_message')
        );
    }
}
