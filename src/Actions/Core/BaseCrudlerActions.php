<?php

namespace Crudler\Actions\Core;

use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Repositories\Core\BaseCrudlerRepository;
use Crudler\Repositories\Interfaces\IRepositoryFunction;
use Crudler\Services\Core\BaseCrudlerService;
use Crudler\Services\Interfaces\IServiceFunction;
use LogicException;

abstract class BaseCrudlerActions
{
    public ?BaseCrudlerService $service = null;

    public ?BaseCrudlerRepository $repository = null;

    public ?CrudlerPolicyDTO $crudlerPolicyDTO = null;

    public ?IRepositoryFunction $repositoryFunction = null;

    public ?IServiceFunction $serviceFunction = null;

    public function _show(ActionShowDTO $dto): mixed
    {
        throw new LogicException('Show not supported');
    }

    public function _create(ActionCreateDTO $dto): mixed
    {
        throw new LogicException('Create not supported');
    }

    public function _update(ActionUpdateDTO $dto): mixed
    {
        throw new LogicException('Update not supported');
    }

    public function _delete(ActionDeleteDTO $dto): mixed
    {
        throw new LogicException('Delete not supported');
    }

    public function _restore(ActionRestoreDTO $dto): mixed
    {
        throw new LogicException('Restore not supported');
    }
}
