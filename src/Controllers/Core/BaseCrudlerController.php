<?php

namespace Crudler\Controllers\Core;

use Crudler\Actions\Core\BaseCrudlerActions;
use Crudler\Controllers\DTO\Parts\ControllerCreateDTO;
use Crudler\Controllers\DTO\Parts\ControllerDeleteDTO;
use Crudler\Controllers\DTO\Parts\ControllerListDTO;
use Crudler\Controllers\DTO\Parts\ControllerRestoreDTO;
use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\DTO\Parts\ControllerUpdateDTO;
use Crudler\Repositories\Interfaces\IActionFunction;
use Crudler\Resources\DTO\CrudlerResourceDTO;

use Core\Interfaces\IHttpContext;
use Core\Resources\BaseResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use LogicException;

abstract class BaseCrudlerController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public BaseResource|CrudlerResourceDTO|null $resource = null;

    public IActionFunction|null $actionFunction = null;

    public BaseCrudlerActions $actions;

    protected IHttpContext $http;

    public function _list(ControllerListDTO $dto): mixed
    {
        throw new LogicException('List not supported');
    }

    public function _show(ControllerShowDTO $dto): mixed
    {
        throw new LogicException('Show not supported');
    }

    public function _create(ControllerCreateDTO $dto): mixed
    {
        throw new LogicException('Create not supported');
    }

    public function _update(ControllerUpdateDTO $dto): mixed
    {
        throw new LogicException('Update not supported');
    }

    public function _delete(ControllerDeleteDTO $dto): mixed
    {
        throw new LogicException('Delete not supported');
    }

    public function _restore(ControllerRestoreDTO $dto): mixed
    {
        throw new LogicException('Restore not supported');
    }
}
