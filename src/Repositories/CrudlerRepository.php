<?php

namespace Crudler\Repositories;

use Crudler\Repositories\Core\CoreCrudlerRepository;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CrudlerRepository extends CoreCrudlerRepository
{
    public function __construct(string $modelClass)
    {
        parent::__construct($modelClass);
    }

    /**
     * Summary of crudlerIsUnique
     *
     * @param CrudlerRepositoryDTO $dto
     *
     * @return bool|Exception
     */
    public function crudlerIsUnique(CrudlerRepositoryDTO $dto): bool|Exception
    {
        return $this->_isUnique($dto->uniqueDTO);
    }

    /**
     * Summary of crudlerShowOnceById
     *
     * @param CrudlerRepositoryDTO $dto
     *
     * @return Exception|Model
     */
    public function crudlerShowOnceById(CrudlerRepositoryDTO $dto): Exception|Model
    {
        try {
            $data = $this->_showOnceById($dto->showOnceDTO);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $data;
    }
}
