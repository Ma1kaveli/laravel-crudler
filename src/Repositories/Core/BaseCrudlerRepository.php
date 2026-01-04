<?php

namespace Crudler\Repositories\Core;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;

use Core\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use LogicException;

abstract class BaseCrudlerRepository extends BaseRepository
{
    public function _isUnique(RepositoryUniqueDTO $dto): bool|Exception
    {
        throw new LogicException('isUnique not supported');
    }

    public function _showOnceById(RepositoryShowOnceDTO $dto): Exception|Model
    {
        throw new LogicException('showOnceById not supported');
    }
}
