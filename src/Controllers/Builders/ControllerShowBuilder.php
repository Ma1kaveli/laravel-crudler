<?php

namespace Crudler\Controllers\Builders;

use Core\DTO\OnceDTO;
use Crudler\Adapters\ClosureHookAdapter;
use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\Interfaces\IShowCallableDTO;
use Illuminate\Http\Request;

class ControllerShowBuilder
{
    public ?IShowCallableDTO $showCallableDTO;

    public array $additionalData = [];

    public static function make(): self {
        return new self();
    }

    /**
     * Summary of showCallableDTO
     *
     * @param IShowCallableDTO|callable(Request $request, int $id): OnceDTO $showCallableDTO
     *
     * @return ControllerShowBuilder
     */
    public function showCallableDTO(IShowCallableDTO $showCallableDTO): self
    {
        $this->showCallableDTO = ($showCallableDTO instanceof IShowCallableDTO)
            ? $showCallableDTO
            : new class($showCallableDTO) extends ClosureHookAdapter implements IShowCallableDTO {
                public function __invoke(Request $request, int $id): OnceDTO {
                    return ($this->closure)($request, $id);
                }
            };

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
        return ControllerShowDTO::start(
            showDTO: $this->showCallableDTO,
            additionalData: $this->additionalData,
        );
    }
}
