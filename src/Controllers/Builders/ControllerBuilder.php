<?php

namespace Crudler\Controllers\Builders;

use Crudler\Controllers\DTO\CrudlerControllerDTO;
use Crudler\Controllers\DTO\Parts\ControllerCreateDTO;
use Crudler\Controllers\DTO\Parts\ControllerDeleteDTO;
use Crudler\Controllers\DTO\Parts\ControllerListDTO;
use Crudler\Controllers\DTO\Parts\ControllerRestoreDTO;
use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\DTO\Parts\ControllerUpdateDTO;
use Crudler\Controllers\Interfaces\ICreateCallableDTO;
use Crudler\Controllers\Interfaces\IUpdateCallableDTO;
use Crudler\Repositories\Interfaces\IActionFunction;
use Crudler\Resources\DTO\CrudlerResourceDTO;

use Core\DTO\FormDTO;
use Core\Resources\BaseResource;
use Crudler\Adapters\ClosureHookAdapter;
use Crudler\Actions\DTO\CrudlerActionDTO;
use Illuminate\Http\Request;
use LogicException;

class ControllerBuilder
{
    public CrudlerResourceDTO|BaseResource|null $resource = null;

    public ?IActionFunction $actionFunction = null;

    public ?ControllerShowDTO $showDTO = null;

    public ?ControllerListDTO $listDTO = null;

    public ?ControllerCreateDTO $createDTO = null;

    public ?ControllerUpdateDTO $updateDTO = null;

    public ?ControllerDeleteDTO $deleteDTO = null;

    public ?ControllerRestoreDTO $restoreDTO = null;

    public function make(): self {
        return new self();
    }

    public function resource(BaseResource|CrudlerResourceDTO $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Summary of action
     *
     * @param IActionFunction(FormDTO $dto, ...$args): CrudlerActionDTO $actionFunction
     *
     * @return ControllerBuilder
     */
    public function action(IActionFunction $actionFunction): self
    {
        $this->actionFunction = $actionFunction instanceof IActionFunction
            ? $actionFunction
            : new class($actionFunction) extends ClosureHookAdapter implements IActionFunction {
                public function __invoke(FormDTO $dto, ...$args): CrudlerActionDTO {
                    return ($this->closure)($dto, ...$args);
                }
            };

        return $this;
    }

    public function listDTO(ControllerListDTO $listDTO): self
    {
        $this->listDTO = $listDTO;

        return $this;
    }

    public function showDTO(ControllerShowDTO $showDTO): self
    {
        $this->showDTO = $showDTO;

        return $this;
    }

    public function createDTO(ControllerCreateDTO $createDTO): self
    {
        $this->createDTO = $createDTO;

        return $this;
    }

    public function updateDTO(ControllerUpdateDTO $updateDTO): self
    {
        $this->updateDTO = $updateDTO;

        return $this;
    }

    public function deleteDTO(ControllerDeleteDTO $deleteDTO): self
    {
        $this->deleteDTO = $deleteDTO;

        return $this;
    }

    public function restoreDTO(ControllerRestoreDTO $restoreDTO): self
    {
        $this->restoreDTO = $restoreDTO;

        return $this;
    }

    public function listBuilder(ControllerListBuilder $builder): self
    {
        return $this->listDTO($builder->build());
    }

    public function showBuilder(ControllerShowBuilder $builder): self
    {
        return $this->showDTO($builder->build());
    }

    public function createBuilder(ControllerCreateBuilder $builder): self
    {
        return $this->createDTO($builder->build());
    }

    public function updateBuilder(ControllerUpdateBuilder $builder): self
    {
        return $this->updateDTO($builder->build());
    }

    public function deleteBuilder(ControllerDeleteBuilder $builder): self
    {
        return $this->deleteDTO($builder->build());
    }

    public function restoreBuilder(ControllerRestoreBuilder $builder): self
    {
        return $this->restoreDTO($builder->build());
    }

    /**
     * Summary of fromConfig
     *
     * @param array $config
     *
     * @param FormDTO|ICreateCallableDTO|callable(Request $request): FormDTO $createDTO
     * @param FormDTO|IUpdateCallableDTO|callable(Request $request, int $id): FormDTO $updateDTO
     *
     * @return ControllerBuilder
     */
    public function fromConfig(
        array $config,
        ICreateCallableDTO|callable|FormDTO|null $createDTO = null,
        IUpdateCallableDTO|callable|FormDTO|null $updateDTO = null
    ): self {
        if (isset($config['list'])) {
            $this->listBuilder(
                ControllerListBuilder::make()->fromConfig($config['list'])
            );
        }

        if (isset($config['show'])) {
            $this->showBuilder(
                ControllerShowBuilder::make()->fromConfig($config['show'])
            );
        }

        if (isset($config['create']) && !empty($createDTO)) {
            $this->createBuilder(
                ControllerCreateBuilder::make($createDTO)->fromConfig($config['create'])
            );
        }

        if (isset($config['update']) && !empty($updateDTO)) {
            $this->updateBuilder(
                ControllerUpdateBuilder::make($updateDTO)->fromConfig($config['update'])
            );
        }

        if (isset($config['delete'])) {
            $this->deleteBuilder(
                ControllerDeleteBuilder::make()->fromConfig($config['delete'])
            );
        }

        if (isset($config['restore'])) {
            $this->restoreBuilder(
                ControllerRestoreBuilder::make()->fromConfig($config['restore'])
            );
        }

        return $this;
    }

    public function build(): CrudlerControllerDTO
    {
        if (empty($this->resource)) {
            throw new LogicException('Resource not found!');
        }

        if (empty($this->actionFunction)) {
            throw new LogicException('Action function not found!');
        }

        return new CrudlerControllerDTO(
            resource: $this->resource,
            actionFunction: $this->actionFunction,
            indexDTO: $this->listDTO,
            showDTO: $this->showDTO,
            createDTO: $this->createDTO,
            updateDTO: $this->updateDTO,
            deleteDTO: $this->deleteDTO,
            restoreDTO: $this->restoreDTO
        );
    }
}
