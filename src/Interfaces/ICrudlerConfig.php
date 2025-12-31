<?php

namespace Crudler\Core;

use Crudler\Actions\DTO\CrudlerActionDTO;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Controllers\DTO\CrudlerControllerDTO;
use Crudler\Controllers\DTO\Parts\ControllerCreateDTO;
use Crudler\Controllers\DTO\Parts\ControllerDeleteDTO;
use Crudler\Controllers\DTO\Parts\ControllerListDTO;
use Crudler\Controllers\DTO\Parts\ControllerRestoreDTO;
use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\DTO\Parts\ControllerUpdateDTO;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Requests\DTO\CrudlerRequestDTO;
use Crudler\Resources\DTO\CrudlerResourceDTO;
use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Crudler\Services\DTO\Parts\ServiceUpdateDTO;

use Core\DTO\FormDTO;
use Core\DTO\OnceDTO;
use Illuminate\Database\Eloquent\Model;

interface ICrudlerConfig {
    public static function BASE_RESOURCE_CRUDLER(...$args): CrudlerResourceDTO|null;

    public static function BASE_REQUEST_CRUDLER(...$args): CrudlerRequestDTO|null;

    public static function FULL_SERVICE_CRUDLER(FormDTO $dto, ?Model $data = null, ...$args): CrudlerServiceDTO|null;

    public static function BASE_SERVICE_CRUDLER(
        ?ServiceCreateDTO $serviceCreateDTO = null,
        ?ServiceUpdateDTO $serviceUpdateDTO = null,
        ?ServiceDeleteDTO $serviceDeleteDTO = null,
        ?ServiceRestoreDTO $serviceRestoreDTO = null,
        ...$args
    ): CrudlerServiceDTO|null;

    public static function BASE_SERVICE_CREATE_DTO(FormDTO $formDTO, ...$args): ServiceCreateDTO|null;

    public static function BASE_SERVICE_UPDATE_DTO(FormDTO $formDTO, Model $data, ...$args): ServiceUpdateDTO|null;

    public static function BASE_SERVICE_DELETE_DTO(FormDTO $formDTO, Model $data, ...$args): ServiceDeleteDTO|null;

    public static function BASE_SERVICE_RESTORE_DTO(FormDTO $formDTO, Model $data, ...$args): ServiceRestoreDTO|null;

    public static function FULL_REPOSITORY_CRUDLER(
        FormDTO $dto,
        ...$args
    ): CrudlerRepositoryDTO|null;

    public static function BASE_REPOSITORY_CRUDLER(
        ?RepositoryUniqueDTO $uniqueDTO = null,
        ?RepositoryShowOnceDTO $showOnceDTO = null,
        ...$args
    ): CrudlerRepositoryDTO|null;

    public static function BASE_REPOSITORY_UNIQUE_DTO(FormDTO $formDTO, ...$args): RepositoryUniqueDTO|null;

    public static function BASE_REPOSITORY_SHOW_ONCE_DTO(FormDTO $formDTO, ...$args): RepositoryShowOnceDTO|null;

    public static function BASE_POLICY_CRUDLER(...$args): CrudlerPolicyDTO|null;

    public static function FULL_ACTION_CRUDLER(FormDTO|OnceDTO $dto, ...$args): CrudlerActionDTO|null;

    public static function BASE_ACTION_CRUDLER(
        ?ActionShowDTO $actionShowDTO = null,
        ?ActionCreateDTO $actionCreateDTO = null,
        ?ActionUpdateDTO $actionUpdateDTO = null,
        ?ActionDeleteDTO $actionDeleteDTO = null,
        ?ActionRestoreDTO $actionRestoreDTO = null,
        ...$args
    ): CrudlerActionDTO|null;

    public static function BASE_ACTION_SHOW_DTO(OnceDTO $formDTO, ...$args): ActionShowDTO|null;

    public static function BASE_ACTION_CREATE_DTO(FormDTO $formDTO, ...$args): ActionCreateDTO|null;

    public static function BASE_ACTION_UPDATE_DTO(FormDTO $formDTO, ...$args): ActionUpdateDTO|null;

    public static function BASE_ACTION_DELETE_DTO(FormDTO $formDTO, ...$args): ActionDeleteDTO|null;

    public static function BASE_ACTION_RESTORE_DTO(FormDTO $formDTO, ...$args): ActionRestoreDTO|null;

    public static function FULL_CONTROLLER_CRUDLER(...$args): CrudlerControllerDTO|null;

    public static function BASE_CONTROLLER_CRUDLER(
        ?ControllerListDTO $controllerListDTO = null,
        ?ControllerShowDTO $controllerShowDTO = null,
        ?ControllerCreateDTO $controllerCreateDTO = null,
        ?ControllerUpdateDTO $controllerUpdateDTO = null,
        ?ControllerDeleteDTO $controllerDeleteDTO = null,
        ?ControllerRestoreDTO $controllerRestoreDTO = null,
        ...$args
    ): CrudlerControllerDTO|null;

    public static function BASE_CONTROLLER_LIST_DTO(...$args): ControllerListDTO|null;

    public static function BASE_CONTROLLER_SHOW_DTO(...$args): ControllerShowDTO|null;

    public static function BASE_CONTROLLER_CREATE_DTO(...$args): ControllerCreateDTO|null;

    public static function BASE_CONTROLLER_UPDATE_DTO(...$args): ControllerUpdateDTO|null;

    public static function BASE_CONTROLLER_DELETE_DTO(...$args): ControllerDeleteDTO|null;

    public static function BASE_CONTROLLER_RESTORE_DTO(...$args): ControllerRestoreDTO|null;
}
