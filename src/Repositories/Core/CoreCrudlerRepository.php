<?php

namespace Crudler\Repositories\Core;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Logger\Facades\LaravelLog;

class CoreCrudlerRepository extends BaseCrudlerRepository
{
    public function __construct(string $modelClass)
    {
        parent::__construct($modelClass);
    }

    /**
     * Summary of _isUnique
     *
     * @param RepositoryUniqueDTO $dto
     *
     * @return bool|Exception
     */
    public function _isUnique(RepositoryUniqueDTO $dto): bool|Exception
    {
        return $this->isUnique(
            $dto->formDTO,
            $dto->toArray(),
            true,
            $dto->message
        );
    }

    /**
     * Summary of _showOnceById
     *
     * @param RepositoryShowOnceDTO $dto
     *
     * @return Model|Exception
     */
    public function _showOnceById(RepositoryShowOnceDTO $dto): Model|Exception
    {
        $config = $dto->isCallable() ? ($dto->config)($dto->formDTO) : $dto->config;

        $q = $this->query();

        if (!empty($config->with)) {
            $q = $q->with($config->with);
        }

        if (!empty($config->withCount)) {
            $q = $q->withCount($config->withCount);
        }

        if (!empty($config->query)) {
            $q = $config->query($q, $dto->formDTO);
        }

        if ($config->withTrashed) {
            $q = $q->withTrashed();
        }

        try {
            $data = $q->findOrFail($dto->formDTO->id);
        } catch (Exception $e) {
            LaravelLog::error($e->getMessage());
            throw new Exception($config->message, 404);
        }

        return $data;
    }
}
