<?php

namespace Crudler\Controllers\Builders;

use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\Interfaces\IShowCallableDTO;

class ControllerShowBuilder
{
    public ?IShowCallableDTO $showCallableDTO;

    public array $additionalData = [];

    public static function make(): self {
        return new self();
    }

    public function showCallableDTO(IShowCallableDTO $showCallableDTO): self
    {
        $this->showCallableDTO = $showCallableDTO;

        return $this;
    }

    public function additionalData(array $additionalData): self
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    public function fromConfig(array $config): self {
        if (isset($config['additional_data'])) {
            $this->additionalData = $config['additional_data'];
        }

        if (isset($config['show_callable_dto'])) {
            $this->showCallableDTO = $config['show_callable_dto'];
        }

        return $this;
    }

    public function build(): ControllerShowDTO {
        return new ControllerShowDTO(
            showDTO: $this->showCallableDTO,
            additionalData: $this->additionalData,
        );
    }
}
