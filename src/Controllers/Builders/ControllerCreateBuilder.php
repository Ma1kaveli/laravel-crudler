<?php

namespace Crudler\Controllers\Builders;

use Crudler\Controllers\DTO\Parts\ControllerCreateDTO;
use Crudler\Controllers\Interfaces\ICreateCallableDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;

use Core\DTO\FormDTO;
use Crudler\Adapters\ClosureHookAdapter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use LogicException;

class ControllerCreateBuilder
{
    public ICreateCallableDTO|FormDTO $formDTO;

    public array $additionalData = [];

    public ?string $requestTag = 'create';

    public FormRequest|CrudlerRequestDTO|null $request = null;

    public ?string $successMessage = null;

    /**
     * Summary of make
     *
     * @param FormDTO|ICreateCallableDTO|callable(Request $request): FormDTO $formDTO
     *
     * @return ControllerCreateBuilder
     */
    public static function make(ICreateCallableDTO|callable|FormDTO $formDTO): self
    {
        $builder = new self();

        $builder->formDTO = ($formDTO instanceof ICreateCallableDTO || $formDTO instanceof FormDTO)
            ? $formDTO
            : new class($formDTO) extends ClosureHookAdapter implements ICreateCallableDTO {
                public function __invoke(Request $request): FormDTO {
                    return ($this->closure)($request);
                }
            };

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

    public function request(FormRequest|CrudlerRequestDTO $request): self
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

    public function build(): ControllerCreateDTO {
        if (empty($this->request)) {
            throw new LogicException('Request not found');
        }

        return ControllerCreateDTO::start(
            formDTO: $this->formDTO,
            requestTag: $this->requestTag,
            additionalData: $this->additionalData,
            request: $this->request,
            successMessage: $this->successMessage,
        );
    }
}
