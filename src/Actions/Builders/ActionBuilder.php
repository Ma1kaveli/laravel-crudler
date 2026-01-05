<?php

namespace Crudler\Actions\Builders;

use Crudler\Actions\DTO\CrudlerActionDTO;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Repositories\Interfaces\IRepositoryFunction;
use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\Interfaces\IServiceFunction;

use Core\DTO\FormDTO;
use Core\DTO\OnceDTO;
use Crudler\Adapters\ClosureHookAdapter;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class ActionBuilder
{
    public IRepositoryFunction|null $repositoryFunction = null;

    public IServiceFunction|null $serviceFunction = null;

    public ?ActionShowDTO $showDTO = null;

    public ?ActionCreateDTO $createDTO = null;

    public ?ActionUpdateDTO $updateDTO = null;

    public ?ActionDeleteDTO $deleteDTO = null;

    public ?ActionRestoreDTO $restoreDTO = null;

    public static function make(): self
    {
        return new self();
    }

    /**
     * Summary of repositoryFunction
     *
     * @param IRepositoryFunction|callable(FormDTO $dto, ...$args): CrudlerRepositoryDTO $repositoryFunction
     *
     * @return ActionBuilder
     */
    public function repositoryFunction(IRepositoryFunction|callable $repositoryFunction): self
    {
        $this->repositoryFunction = $repositoryFunction instanceof IRepositoryFunction
            ? $repositoryFunction
            : new class($repositoryFunction) extends ClosureHookAdapter implements IRepositoryFunction {
                public function __invoke(FormDTO $dto, ...$args): CrudlerRepositoryDTO {
                    return ($this->closure)($dto, ...$args);
                }
            };

        return $this;
    }

    /**
     * Summary of serviceFunction
     *
     * @param IServiceFunction|callable(FormDTO $dto, ?Model $data = null, ...$args): CrudlerServiceDTO $serviceFunction
     *
     * @return ActionBuilder
     */
    public function serviceFunction(IServiceFunction|callable $serviceFunction): self
    {
        $this->serviceFunction = $serviceFunction instanceof IServiceFunction
            ? $serviceFunction
            : new class($serviceFunction) extends ClosureHookAdapter implements IServiceFunction {
                public function __invoke(FormDTO $dto, ?Model $data = null, ...$args): CrudlerServiceDTO {
                    return ($this->closure)($dto, $data, ...$args);
                }
            };

        return $this;
    }

    public function showDTO(ActionShowDTO $showDTO): self
    {
        $this->showDTO = $showDTO;

        return $this;
    }

    public function createDTO(ActionCreateDTO $createDTO): self
    {
        $this->createDTO = $createDTO;

        return $this;
    }

    public function updateDTO(ActionUpdateDTO $updateDTO): self
    {
        $this->updateDTO = $updateDTO;

        return $this;
    }

    public function deleteDTO(ActionDeleteDTO $deleteDTO): self
    {
        $this->deleteDTO = $deleteDTO;

        return $this;
    }

    public function restoreDTO(ActionRestoreDTO $restoreDTO): self
    {
        $this->restoreDTO = $restoreDTO;

        return $this;
    }

    public function showBuilder(ActionShowBuilder $builder): self
    {
        return $this->showDTO($builder->build());
    }

    public function createBuilder(ActionItemBuilder $builder): self
    {
        return $this->createDTO($builder->buildCreate());
    }

    public function updateBuilder(ActionItemBuilder $builder): self
    {
        return $this->updateDTO($builder->buildUpdate());
    }

    public function deleteBuilder(ActionItemBuilder $builder): self
    {
        return $this->deleteDTO($builder->buildDelete());
    }

    public function restoreBuilder(ActionItemBuilder $builder): self
    {
        return $this->restoreDTO($builder->buildRestore());
    }

    public function fromConfig(
        array $config,
        ?OnceDTO $onceDTO = null,
        ?FormDTO $formDTO = null,
    ): self {
        if (isset($config['show']) && !empty($onceDTO)) {
            $this->showBuilder(
                ActionShowBuilder::make($onceDTO)->fromConfig($config['show'])
            );
        }

        if (isset($config['create']) && !empty($formDTO)) {
            $this->createBuilder(
                ActionItemBuilder::make($formDTO)->fromConfig($config['create'])
            );
        }

        if (isset($config['update']) && !empty($formDTO)) {
            $this->updateBuilder(
                ActionItemBuilder::make($formDTO)->fromConfig($config['update'])
            );
        }

        if (isset($config['delete']) && !empty($onceDTO)) {
            $this->deleteBuilder(
                ActionItemBuilder::make($onceDTO)->fromConfig($config['delete'])
            );
        }

        if (isset($config['restore']) && !empty($onceDTO)) {
            $this->restoreBuilder(
                ActionItemBuilder::make($onceDTO)->fromConfig($config['restore'])
            );
        }

        return $this;
    }

    public function build(): CrudlerActionDTO
    {
        if (empty($this->repositoryFunction)) {
            throw new LogicException('Repository function not found!');
        }

        if (empty($this->serviceFunction)) {
            throw new LogicException('Service function not found!');
        }

        return new CrudlerActionDTO(
            repositoryFunc: $this->repositoryFunction,
            serviceFunc: $this->serviceFunction,
            actionShowDTO: $this->showDTO,
            actionCreateDTO: $this->createDTO,
            actionUpdateDTO: $this->updateDTO,
            actionDeleteDTO: $this->deleteDTO,
            actionRestoreDTO: $this->restoreDTO
        );
    }
}
