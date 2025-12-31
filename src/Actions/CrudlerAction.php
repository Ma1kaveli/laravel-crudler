<?php

namespace Crudler\Actions;

use Crudler\Actions\Core\CoreCrudlerActions;
use Crudler\Actions\DTO\CrudlerActionDTO;

use Exception;

class CrudlerAction extends CoreCrudlerActions
{
    private function setFunctions(CrudlerActionDTO $dto)
    {
        $this->repositoryFunction = $dto->repositoryFunc;
        $this->serviceFunction = $dto->serviceFunc;
    }

    public function crudlerShow(CrudlerActionDTO $dto)
    {
        if (!$dto->actionShowDTO) {
            throw new Exception('You need to set config to show action');
        }

        $this->setFunctions($dto);

        return $this->_show($dto->actionShowDTO);
    }

    public function crudlerCreate(CrudlerActionDTO $dto)
    {
        if (!$dto->actionCreateDTO) {
            throw new Exception('You need to set config to create action');
        }

        $this->setFunctions($dto);

        return $this->_create($dto->actionCreateDTO);
    }

    public function crudlerUpdate(CrudlerActionDTO $dto)
    {
        if (!$dto->actionUpdateDTO) {
            throw new Exception('You need to set config to update action');
        }

        $this->setFunctions($dto);

        return $this->_update($dto->actionUpdateDTO);
    }

    public function crudlerDelete(CrudlerActionDTO $dto)
    {
        if (!$dto->actionDeleteDTO) {
            throw new Exception('You need to set config to delete action');
        }

        $this->setFunctions($dto);

        return $this->_delete($dto->actionDeleteDTO);
    }

    public function crudlerRestore(CrudlerActionDTO $dto)
    {
        if (!$dto->actionRestoreDTO) {
            throw new Exception('You need to set config to restore action');
        }

        $this->setFunctions($dto);

        return $this->_restore($dto->actionRestoreDTO);
    }
}
