<?php

namespace Crudler\Controllers\Core;

use Crudler\Actions\Core\BaseCrudlerActions;
use Crudler\Controllers\DTO\Parts\ControllerCreateDTO;
use Crudler\Controllers\DTO\Parts\ControllerDeleteDTO;
use Crudler\Controllers\DTO\Parts\ControllerListDTO;
use Crudler\Controllers\DTO\Parts\ControllerRestoreDTO;
use Crudler\Controllers\DTO\Parts\ControllerShowDTO;
use Crudler\Controllers\DTO\Parts\ControllerUpdateDTO;
use Crudler\Controllers\DTO\Parts\List\ListConfigDTO;
use Crudler\Controllers\Interfaces\ICreateCallableDTO;
use Crudler\Controllers\Interfaces\IListCallableDTO;
use Crudler\Controllers\Interfaces\IUpdateCallableDTO;
use Crudler\Requests\CrudlerRequest;
use Crudler\Requests\DTO\CrudlerRequestDTO;
use Crudler\Resources\CrudlerResource;

use Exception;
use Core\DTO\ListDTO;
use Core\DTO\OnceDTO;
use Core\Interfaces\IHttpContext;
use Core\Interfaces\IListDTO;
use Core\Resources\BaseResource;
use Illuminate\Foundation\Http\FormRequest;
use LogicException;
use QueryBuilder\Resources\PaginatedCollection;

class CoreCrudlerController extends BaseCrudlerController
{
    public function __construct(
        BaseCrudlerActions $actions,
        IHttpContext $http
    ) {
        $this->http = $http;
        $this->actions = $actions;
    }

    /**
     * Summary of configureListDTO
     *
     * @param IListCallableDTO|IListDTO|ListConfigDTO $dto
     *
     * @return IListDTO
     */
    private function configureListDTO(IListCallableDTO|IListDTO|ListConfigDTO $dto): IListDTO
    {
        if ($dto instanceof IListCallableDTO) {
            return $dto($this->http->request());
        }

        if ($dto instanceof IListDTO) {
            return $dto;
        }

        return ListDTO::fromRequest(
            $this->http->request(),
            $dto->params,
            $dto->mapParams
        );
    }

    public function _list(ControllerListDTO $dto): mixed
    {
        $request = $this->http->request();
        $repository = $this->actions->repository;

        if (empty($repository)) {
            throw new LogicException('Repository not found');
        }

        $listDTO = !empty($dto->dto)
            ? $this->configureListDTO($dto->dto)
            : ListDTO::fromRequest($request);

        $data = $dto->isGetAll
            ? $repository->getAll()
            : $repository->getPaginatedList($listDTO);

        if ($this->resource instanceof BaseResource) {
            $collection = $this->resource->collection(
                $data,
                $dto->additionalData
            );

            return $dto->isGetAll
                ? $this->resource->collection($data, $dto->additionalData)
                : new PaginatedCollection($data, $collection);
        }

        $resource = (new CrudlerResource($this->resource->generator))
            ->collection($data, $dto->additionalData);

        return $dto->isGetAll
            ? $resource
            : new PaginatedCollection($data, $resource);
    }

    public function _show(ControllerShowDTO $dto): mixed
    {
        $request = $this->http->request();
        $id = $this->http->route('id');

        if (empty($id)) {
            throw new LogicException('ID not found');
        }

        $onceDto = empty($dto->showDTO)
            ? OnceDTO::make($id)
            : ($dto->showDTO)($request, $id);

        $actionShowDTO = ($this->actionFunction)($onceDto)->actionShowDTO;

        if (empty($actionShowDTO)) {
            throw new LogicException('ActionShowDTO not found');
        }

        try {
            $data = $this->actions->_show($actionShowDTO);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($this->resource instanceof BaseResource) {
            return new $this->resource($data, $dto->additionalData);
        }

        return (new CrudlerResource($this->resource->generator))
            ->resource($data, $dto->additionalData);
    }

    public function _create(ControllerCreateDTO $dto): mixed
    {
        $request = $dto->request;

        if ($request instanceof CrudlerRequestDTO) {
            $request = (new CrudlerRequest($request))->make(
                $dto->requestTag,
                $this->http->request()
            );
        }

        $request->validated();

        $formDTO = $dto->formDTO instanceof ICreateCallableDTO
            ? ($dto->formDTO)($request)
            : $dto->formDTO;

        $actionCreateDTO = ($this->actionFunction)($formDTO)->actionCreateDTO;

        if (empty($actionCreateDTO)) {
            throw new LogicException('ActionCreateDTO not found');
        }

        try {
            $data = $this->actions->_create($actionCreateDTO);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($this->resource instanceof BaseResource) {
            return new $this->resource($data, $dto->additionalData);
        }

        return (new CrudlerResource($this->resource->generator))
            ->resource($data, $dto->additionalData);
    }

    public function _update(ControllerUpdateDTO $dto): mixed
    {
        $request = $dto->request;

        if ($request instanceof CrudlerRequestDTO) {
            $request = (new CrudlerRequest($request))->make(
                $dto->requestTag,
                $this->http->request()
            );
        }

        $request->validated();

        $formDTO = $dto->formDTO instanceof IUpdateCallableDTO
            ? ($dto->formDTO)($request)
            : $dto->formDTO;

        $actionUpdateDTO = ($this->actionFunction)($formDTO)->actionUpdateDTO;

        if (empty($actionUpdateDTO)) {
            throw new LogicException('ActionUpdateDTO not found');
        }

        try {
            $data = $this->actions->_update($actionUpdateDTO);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        if ($this->resource instanceof BaseResource) {
            return new $this->resource($data, $dto->additionalData);
        }

        return (new CrudlerResource($this->resource->generator))
            ->resource($data, $dto->additionalData);
    }

    public function _delete(ControllerDeleteDTO $dto): mixed
    {
        $request = $dto->request;
        $id = $this->http->route('id');

        if ($request instanceof FormRequest) {
            $request->validated();
        }

        if ($request instanceof CrudlerRequestDTO) {
            $request = (new CrudlerRequest($request))->make(
                $dto->requestTag,
                $this->http->request()
            )->validated();
        }

        $onceDTO = empty($dto->onceDTO)
            ? OnceDTO::make($id)
            : ($dto->onceDTO)($request, $id);

        $actionDeleteDTO = ($this->actionFunction)($onceDTO)->actionDeleteDTO;

        if (empty($actionDeleteDTO)) {
            throw new LogicException('ActionDeleteDTO not found');
        }

        try {
            $data = $this->actions->_delete($actionDeleteDTO);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $data;
    }

    public function _restore(ControllerRestoreDTO $dto): mixed
    {
        $request = $dto->request;
        $id = $this->http->route('id');

        if ($request instanceof FormRequest) {
            $request->validated();
        }

        if ($request instanceof CrudlerRequestDTO) {
            $request = (new CrudlerRequest($request))->make(
                $dto->requestTag,
                $this->http->request()
            )->validated();
        }

        $onceDTO = empty($dto->onceDTO)
            ? OnceDTO::make($id)
            : ($dto->onceDTO)($request, $id);

        $actionRestoreDTO = ($this->actionFunction)($onceDTO)->actionRestoreDTO;

        if (empty($actionRestoreDTO)) {
            throw new LogicException('ActionRestoreDTO not found');
        }

        try {
            $data = $this->actions->_restore($actionRestoreDTO);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $data;
    }
}
