<?php

namespace Crudler\Controllers\Builders;

use Crudler\Controllers\DTO\Parts\ControllerDeleteDTO;
use Crudler\Controllers\Interfaces\IOnceFormCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ControllerDeleteBuilder
{
    public ?IOnceFormCallableDTO $onceCallableDTO = null;

    public ?string $requestTag = 'create';

    public Request|FormRequest|CrudlerRequestDTO|null $request = null;

    public ?string $successMessage = null;

    public static function make(): self
    {
        return new self();
    }

    public function onceCallableDTO(IOnceFormCallableDTO $onceCallableDTO): self
    {
        $this->onceCallableDTO = $onceCallableDTO;

        return $this;
    }

    public function tag(string $tag): self
    {
        $this->requestTag = $tag;

        return $this;
    }

    public function request(Request|FormRequest|CrudlerRequestDTO $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function successMessage(string $message): self
    {
        $this->successMessage = $message;

        return $this;
    }

    public function fromConfig(array $config): self {
        if (isset($config['once_callable_dto'])) {
            $this->onceCallableDTO = $config['once_callable_dto'];
        }

        if (isset($config['request_tag'])) {
            $this->requestTag = $config['request_tag'];
        }

        if (isset($config['request'])) {
            $this->request = $config['request'];
        }

        if (isset($config['success_message'])) {
            $this->successMessage = $config['success_message'];
        }

        return $this;
    }

    public function build(): ControllerDeleteDTO {
        return new ControllerDeleteDTO(
            onceDTO: $this->onceCallableDTO,
            requestTag: $this->requestTag,
            request: $this->request,
            successMessage: $this->successMessage,
        );
    }
}
