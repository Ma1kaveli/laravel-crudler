<?php

namespace Crudler\Controllers\Builders;

use Crudler\Controllers\DTO\Parts\ControllerUpdateDTO;
use Crudler\Controllers\Interfaces\IUpdateCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Core\DTO\FormDTO;
use Illuminate\Foundation\Http\FormRequest;
use LogicException;

class ControllerUpdateBuilder
{
    public IUpdateCallableDTO|FormDTO $formDTO;

    public array $additionalData = [];

    public ?string $requestTag = 'update';

    public FormRequest|CrudlerRequestDTO|null $request = null;

    public ?string $successMessage = null;

    public static function make(IUpdateCallableDTO|FormDTO $formDTO): self
    {
        $builder = new self();
        $builder->formDTO = $formDTO;

        return $builder;
    }

    public function additionalData(array $additionalData): self
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    public function tag(string $tag): self
    {
        $this->requestTag = $tag;

        return $this;
    }

    public function request(FormRequest $request): self
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
        if (isset($config['additional_data'])) {
            $this->additionalData = $config['additional_data'];
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

    public function build(): ControllerUpdateDTO {
        if (empty($this->request)) {
            throw new LogicException('Request not found');
        }

        return ControllerUpdateDTO::start(
            formDTO: $this->formDTO,
            requestTag: $this->requestTag,
            additionalData: $this->additionalData,
            request: $this->request,
            successMessage: $this->successMessage,
        );
    }
}
