<?php

namespace Crudler\Services;

use Crudler\Services\Core\CoreCrudlerService;
use Crudler\Services\DTO\CrudlerServiceDTO;

use Exception;
use Illuminate\Database\Eloquent\Model;

class CrudlerService extends CoreCrudlerService
{
    public function __construct(string $modelClass)
    {
        parent::__construct($modelClass);
    }

    /**
     * Summary of crudlerCreate
     *
     * @param CrudlerServiceDTO $dto
     *
     * @return Exception|Model
     */
    public function crudlerCreate(CrudlerServiceDTO $dto): Model
    {
        if (!$dto->createDTO) {
            throw new Exception('You need to set config to create service');
        }

        return $this->_create($dto->createDTO);
    }

    /**
     * Summary of crudlerUpdate
     *
     * @param CrudlerServiceDTO $dto
     *
     * @return Exception|Model
     */
    public function crudlerUpdate(CrudlerServiceDTO $dto): Model
    {
        if (!$dto->updateDTO) {
            throw new Exception('You need to set config to update service');
        }

        return $this->_update($dto->updateDTO);
    }

    /**
     * Summary of crudlerDestroy
     *
     * @param CrudlerServiceDTO $dto
     *
     * @return array
     */
    public function crudlerDestroy(CrudlerServiceDTO $dto): array
    {
        if (!$dto->deleteDTO) {
            throw new Exception('You need to set config to delete service');
        }

        if ($dto->deleteDTO->modelWithSoft) {
            return $this->_destroy($dto->deleteDTO);
        }

        $this->_forceDelete($dto->deleteDTO);

        return [];
    }

    /**
     * Summary of crudlerRestore
     *
     * @param CrudlerServiceDTO $dto
     *
     * @return array
     */
    public function crudlerRestore(CrudlerServiceDTO $dto): array
    {
        if (!$dto->restoreDTO) {
            throw new Exception('You need to set config to restore service');
        }

        return $this->_restore($dto->restoreDTO);
    }
}

