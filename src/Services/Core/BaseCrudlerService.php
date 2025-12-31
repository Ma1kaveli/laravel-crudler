<?php

namespace Crudler\Services\Core;

use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Crudler\Services\DTO\Parts\ServiceUpdateDTO;

use Core\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use LogicException;

abstract class BaseCrudlerService extends BaseService
{
    public function _create(ServiceCreateDTO $dto): Model|LogicException
    {
        throw new LogicException('Create not supported');
    }

    public function _update(ServiceUpdateDTO $dto): Model|LogicException
    {
        throw new LogicException('Update not supported');
    }

    public function _restore(ServiceRestoreDTO $dto): array
    {
        throw new LogicException('Restore not supported');
    }

    public function _delete(ServiceDeleteDTO $dto): array
    {
        throw new LogicException('Delete not supported');
    }

    public function _forceDelete(ServiceDeleteDTO $dto): void
    {
        throw new LogicException('Force delete not supported');
    }
}
