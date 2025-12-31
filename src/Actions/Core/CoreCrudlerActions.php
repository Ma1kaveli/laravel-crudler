<?php

namespace Crudler\Actions\Core;

use Core\DTO\ExecutionOptionsDTO;
use Core\DTO\FormDTO;
use Crudler\Actions\Constants\CrudlerPlaceUniqueEnum;
use Crudler\Actions\Context\ActionContext;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Actions\Executors\BeforeActionPipeline;
use Crudler\Actions\Executors\InActionPipeline;
use Crudler\Actions\Runners\ActionExecutionRunner;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\Resolvers\CrudlerPolicyResolver;
use Crudler\Repositories\Core\BaseCrudlerRepository;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Services\Core\BaseCrudlerService;

use Crudler\Traits\DBTransaction;
use Logger\Traits\AsyncLogger;
use LogicException;

class CoreCrudlerActions extends BaseCrudlerActions
{
    use DBTransaction, AsyncLogger;

    protected ActionExecutionRunner $runner;

    protected BeforeActionPipeline $beforePipeline;

    protected InActionPipeline $inPipeline;

    public function __construct(
        ?BaseCrudlerService $service = null,
        ?BaseCrudlerRepository $repository = null,
        ?CrudlerPolicyDTO $crudlerPolicyDTO = null
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->crudlerPolicyDTO = $crudlerPolicyDTO;
        $this->runner = new ActionExecutionRunner();
        $this->beforePipeline = new BeforeActionPipeline();
        $this->inPipeline = new InActionPipeline();
    }

    public function _show(ActionShowDTO $dto): mixed
    {
        if (empty($this->repository)) {
            throw new \Exception('Repository not found!', 404);
        }

        if (empty($this->repositoryFunction)) {
            throw new LogicException('Repository function not found!');
        }

        $repositoryDTO = ($this->repositoryFunction)($dto->onceDTO);

        if (empty($repositoryDTO)) {
            $repositoryDTO = CrudlerRepositoryDTO::start(
                uniqueDTO: null,
                showOnceDTO: new RepositoryShowOnceDTO($dto->onceDTO)
            );
        }

        $data = $this->repository->_showOnceById($repositoryDTO->showOnceDTO);

        if (!empty($this->crudlerPolicyDTO)) {
            (new CrudlerPolicyResolver)->resolve(
                $this->crudlerPolicyDTO,
                'can_view',
                $dto->onceDTO,
                $data
            );
        }

        $result = $data;
        if (!empty($dto->after)) {
            $result = ($dto->after)($dto->onceDTO, $data);
        }

        if (!empty($dto->return)) {
            return ($dto->return)($dto->onceDTO, $data, $result);
        }

        return $result;
    }

    public function _create(ActionCreateDTO $dto): mixed{
        $config = $dto->config;
        $config ??= ExecutionOptionsDTO::make();

        if (empty($this->repositoryFunction)) {
            throw new LogicException('Repository function not found!');
        }

        if (empty($this->serviceFunction)) {
            throw new LogicException('Service function not found!');
        }

        $repositoryDTO = fn ($formDTO) => ($this->repositoryFunction)($formDTO);

        $uniqueCheck = function (FormDTO $formDTO) use ($repositoryDTO, $dto) {
            if (($repositoryDTO($formDTO) && $dto->placeUnique !== CrudlerPlaceUniqueEnum::none)) {
                return fn () => $this->repository->_isUnique($repositoryDTO($formDTO)->uniqueDTO);
            }

            return null;
        };

        $action = fn () =>
            $this->beforePipeline->execute(
                dto: $dto,
                serviceAction: fn (ActionCreateDTO $dto, ActionContext $ctx) =>
                    $this->inPipeline->execute(
                        dto: $dto,
                        ctx: $ctx,
                        serviceCall: function (ActionCreateDTO $dto) {
                            $serviceDTO = ($this->serviceFunction)($dto->formDTO);

                            return $this->service->_create($serviceDTO->createDTO);
                        }
                    ),
                uniqueCheck: $uniqueCheck,
                config: $config
            );

        return $this->runner->run(
            $action,
            $config,
            $dto->errorMessage,
            $dto->successLog,
            $dto->errorLog
        );
    }


    public function _update(ActionUpdateDTO $dto): mixed {
        $config = $dto->config;
        $config ??= ExecutionOptionsDTO::make();

        if (empty($this->repositoryFunction)) {
            throw new LogicException('Repository function not found!');
        }

        if (empty($this->serviceFunction)) {
            throw new LogicException('Service function not found!');
        }

        $repositoryDTO = fn (FormDTO $formDTO): CrudlerRepositoryDTO => ($this->repositoryFunction)($formDTO);

        if (empty($repositoryDTO->showOnceDTO)) {
            $repositoryDTO = fn (FormDTO $formDTO) => CrudlerRepositoryDTO::start(
                uniqueDTO: null,
                showOnceDTO: new RepositoryShowOnceDTO($formDTO)
            );
        }

        $data = $this->repository->_showOnceById($repositoryDTO($dto->formDTO)->showOnceDTO);

        if (!empty($this->crudlerPolicyDTO)) {
            (new CrudlerPolicyResolver)->resolve(
                $this->crudlerPolicyDTO,
                'can_update',
                $dto->formDTO,
                $data
            );
        }

        $uniqueCheck = function (FormDTO $formDTO) use ($repositoryDTO, $dto) {
            if (($repositoryDTO($formDTO) && $dto->placeUnique !== CrudlerPlaceUniqueEnum::none)) {
                return fn () => $this->repository->_isUnique($repositoryDTO($formDTO)->uniqueDTO);
            }

            return null;
        };

        $action = fn () =>
            $this->beforePipeline->execute(
                dto: $dto,
                serviceAction: fn (ActionUpdateDTO $dto, ActionContext $ctx) =>
                    $this->inPipeline->execute(
                        dto: $dto,
                        ctx: $ctx,
                        serviceCall: function (ActionUpdateDTO $dto) use ($data) {
                            $serviceDTO = ($this->serviceFunction)($dto->formDTO, $data);

                            return $this->service->_update($serviceDTO->updateDTO);
                        }
                    ),
                uniqueCheck: $uniqueCheck,
                config: $config
            );

        return $this->runner->run(
            $action,
            $config,
            $dto->errorMessage,
            $dto->successLog,
            $dto->errorLog
        );
    }

    public function _delete(ActionDeleteDTO $dto): mixed {
        $config = $dto->config;
        $config ??= ExecutionOptionsDTO::make();

        if (empty($this->repositoryFunction)) {
            throw new LogicException('Repository function not found!');
        }

        if (empty($this->serviceFunction)) {
            throw new LogicException('Service function not found!');
        }

        $repositoryDTO = ($this->repositoryFunction)($dto->formDTO);

        if (empty($repositoryDTO->showOnceDTO)) {
            $repositoryDTO = CrudlerRepositoryDTO::start(
                uniqueDTO: null,
                showOnceDTO: new RepositoryShowOnceDTO($dto->formDTO)
            );
        }

        $data = $this->repository->_showOnceById($repositoryDTO->showOnceDTO);

        if (!empty($this->crudlerPolicyDTO)) {
            (new CrudlerPolicyResolver)->resolve(
                $this->crudlerPolicyDTO,
                'can_update',
                $dto->formDTO,
                $data
            );
        }

        $action = fn () =>
            $this->beforePipeline->execute(
                dto: $dto,
                serviceAction: fn (ActionDeleteDTO $dto, ActionContext $ctx) =>
                    $this->inPipeline->execute(
                        dto: $dto,
                        ctx: $ctx,
                        serviceCall: function (ActionDeleteDTO $dto) use ($data) {
                            $serviceDTO = ($this->serviceFunction)($dto->formDTO, $data);
                            $isSoftDelete = $serviceDTO->deleteDTO->modelWithSoft;

                            $data = null;
                            if ($isSoftDelete) {
                                $data = $this->service->_delete($serviceDTO->deleteDTO);
                            } else {
                                $this->service->_forceDelete($serviceDTO->deleteDTO);
                            }

                            return $data;
                        }
                    ),
                uniqueCheck: null,
                config: $config
            );

        return $this->runner->run(
            $action,
            $config,
            $dto->errorMessage,
            $dto->successLog,
            $dto->errorLog
        );
    }

    public function _restore(ActionRestoreDTO $dto): mixed {
        $config = $dto->config;
        $config ??= ExecutionOptionsDTO::make();

        if (empty($this->repositoryFunction)) {
            throw new LogicException('Repository function not found!');
        }

        if (empty($this->serviceFunction)) {
            throw new LogicException('Service function not found!');
        }

        $repositoryDTO = ($this->repositoryFunction)($dto->formDTO);

        if (empty($repositoryDTO->showOnceDTO)) {
            $repositoryDTO = CrudlerRepositoryDTO::start(
                uniqueDTO: null,
                showOnceDTO: new RepositoryShowOnceDTO($dto->formDTO)
            );
        }

        $data = $this->repository->_showOnceById($repositoryDTO->showOnceDTO);

        if (!empty($this->crudlerPolicyDTO)) {
            (new CrudlerPolicyResolver)->resolve(
                $this->crudlerPolicyDTO,
                'can_update',
                $dto->formDTO,
                $data
            );
        }

        $action = fn () =>
            $this->beforePipeline->execute(
                dto: $dto,
                serviceAction: fn (ActionRestoreDTO $dto, ActionContext $ctx) =>
                    $this->inPipeline->execute(
                        dto: $dto,
                        ctx: $ctx,
                        serviceCall: function (ActionRestoreDTO $dto) use ($data) {
                            $serviceDTO = ($this->serviceFunction)($dto->formDTO, $data);

                            return $this->service->_restore($serviceDTO->restoreDTO);
                        }
                    ),
                uniqueCheck: null,
                config: $config
            );

        return $this->runner->run(
            $action,
            $config,
            $dto->errorMessage,
            $dto->successLog,
            $dto->errorLog
        );
    }
}
