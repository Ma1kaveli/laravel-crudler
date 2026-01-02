<?php

namespace Crudler\Controllers\DTO\Parts;

use Crudler\Controllers\Interfaces\IOnceFormCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ControllerRestoreDTO
{
    /**
     * Summary of __construct
     *
     * @param ?IOnceFormCallableDTO $onceDTO = null
     * @param ?string $requestTag = 'restore'
     * @param Request|FormRequest|CrudlerRequestDTO|null $request = null
     * @param ?string $successMessage = null
     */
    public function __construct(
        public readonly ?IOnceFormCallableDTO $onceDTO = null,
        public readonly ?string $requestTag = 'restore',
        public readonly Request|FormRequest|CrudlerRequestDTO|null $request = null,
        public readonly ?string $successMessage = null,
    ) {}

    /**
     * Summary of start
     *
     * @param ?IOnceFormCallableDTO $onceDTO = null
     * @param ?string $requestTag = 'restore'
     * @param Request|FormRequest|CrudlerRequestDTO|null $request = null
     * @param ?string $successMessage = null
     *
     * @return ControllerRestoreDTO
     */
    public static function start(
        ?IOnceFormCallableDTO $onceDTO = null,
        ?string $requestTag = 'restore',
        Request|FormRequest|CrudlerRequestDTO|null $request = null,
        ?string $successMessage = null,
    ): self {
        return new self(
            onceDTO: $onceDTO,
            requestTag: $requestTag,
            request: $request,
            successMessage: $successMessage ??= Config::get('crudler.controllers.success_restore_message')
        );
    }
}
