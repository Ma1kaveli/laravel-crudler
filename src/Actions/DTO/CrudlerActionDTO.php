<?php

namespace Crudler\Actions\DTO;

use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Repositories\Interfaces\IRepositoryFunction;
use Crudler\Services\Interfaces\IServiceFunction;

class CrudlerActionDTO
{
    public function __construct(
        public readonly IRepositoryFunction $repositoryFunc,
        public readonly IServiceFunction $serviceFunc,
        public readonly ?ActionShowDTO $actionShowDTO = null,
        public readonly ?ActionCreateDTO $actionCreateDTO = null,
        public readonly ?ActionUpdateDTO $actionUpdateDTO = null,
        public readonly ?ActionDeleteDTO $actionDeleteDTO = null,
        public readonly ?ActionRestoreDTO $actionRestoreDTO = null,
    ) {}

    /**
     * Summary of start
     *
     * @param IRepositoryFunction $repositoryFunc
     * @param IServiceFunction $serviceFunc
     * @param ?ActionCreateDTO $actionCreateDTO = null
     * @param ?ActionShowDTO $actionShowDTO = null
     * @param ?ActionUpdateDTO $actionUpdateDTO = null
     * @param ?ActionDeleteDTO $actionDeleteDTO = null
     * @param ?ActionRestoreDTO $actionRestoreDTO = null
     *
     * @return static
     */
    public static function start(
        IRepositoryFunction $repositoryFunc,
        IServiceFunction $serviceFunc,
        ?ActionCreateDTO $actionCreateDTO = null,
        ?ActionShowDTO $actionShowDTO = null,
        ?ActionUpdateDTO $actionUpdateDTO = null,
        ?ActionDeleteDTO $actionDeleteDTO = null,
        ?ActionRestoreDTO $actionRestoreDTO = null
    ): static {
        return new self(
            repositoryFunc: $repositoryFunc,
            serviceFunc: $serviceFunc,
            actionCreateDTO: $actionCreateDTO,
            actionShowDTO: $actionShowDTO,
            actionUpdateDTO: $actionUpdateDTO,
            actionDeleteDTO: $actionDeleteDTO,
            actionRestoreDTO: $actionRestoreDTO
        );
    }
}
