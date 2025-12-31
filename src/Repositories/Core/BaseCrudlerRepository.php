<?php

namespace Crudler\Repositories\Core;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;

use Core\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use LogicException;

abstract class BaseCrudlerRepository extends BaseRepository
{
    public function _isUnique(RepositoryUniqueDTO $dto): bool|LogicException
    {
        throw new LogicException('isUnique not supported');
    }

    public function _showOnceById(RepositoryShowOnceDTO $dto): LogicException|Model
    {
        throw new LogicException('showOnceById not supported');
    }
}
