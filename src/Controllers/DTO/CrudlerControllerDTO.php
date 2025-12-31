<?php

namespace Crudler\Controllers\DTO;

use Crudler\Controllers\DTO\Parts\ControllerCreateDTO;
use Crudler\Controllers\DTO\Parts\ControllerDeleteDTO;
use Crudler\Controllers\DTO\Parts\ControllerListDTO;
use Crudler\Controllers\DTO\Parts\ControllerRestoreDTO;
use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\DTO\Parts\ControllerUpdateDTO;
use Crudler\Repositories\Interfaces\IActionFunction;
use Crudler\Resources\DTO\CrudlerResourceDTO;

use Core\Resources\BaseResource;

class CrudlerControllerDTO
{
    public function __construct(
        public readonly BaseResource|CrudlerResourceDTO $resource,
        public readonly IActionFunction $actionFunction,
        public readonly ?ControllerListDTO $indexDTO = null,
        public readonly ?ControllerShowDTO $showDTO = null,
        public readonly ?ControllerCreateDTO $createDTO = null,
        public readonly ?ControllerUpdateDTO $updateDTO = null,
        public readonly ?ControllerDeleteDTO $deleteDTO = null,
        public readonly ?ControllerRestoreDTO $restoreDTO = null,
    ) {}

    /**
     * Summary of start
     *
     * @param BaseResource|CrudlerResourceDTO $resource
     * @param IActionFunction $actionFunction
     * @param ?ControllerListDTO $indexDTO = null
     * @param ?ControllerShowDTO $showDTO = null
     * @param ?ControllerCreateDTO $createDTO = null
     * @param ?ControllerUpdateDTO $updateDTO = null
     * @param ?ControllerDeleteDTO $deleteDTO = null
     * @param ?ControllerRestoreDTO $restoreDTO = null
     *
     * @return static
     */
    public static function start(
        BaseResource|CrudlerResourceDTO $resource,
        IActionFunction $actionFunction,
        ?ControllerListDTO $indexDTO = null,
        ?ControllerShowDTO $showDTO = null,
        ?ControllerCreateDTO $createDTO = null,
        ?ControllerUpdateDTO $updateDTO = null,
        ?ControllerDeleteDTO $deleteDTO = null,
        ?ControllerRestoreDTO $restoreDTO = null
    ): static {
        return new self(
            resource: $resource,
            actionFunction: $actionFunction,
            indexDTO: $indexDTO,
            showDTO: $showDTO,
            createDTO: $createDTO,
            updateDTO: $updateDTO,
            deleteDTO: $deleteDTO,
            restoreDTO: $restoreDTO,
        );
    }
}
