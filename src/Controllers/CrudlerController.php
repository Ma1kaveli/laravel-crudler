<?php

namespace Crudler\Controllers;

use Crudler\Controllers\Core\CoreCrudlerController;
use Crudler\Controllers\DTO\CrudlerControllerDTO;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class CrudlerController extends CoreCrudlerController
{
    private function setFunctions(CrudlerControllerDTO $dto)
    {
        $this->resource = $dto->resource;
        $this->actionFunction = $dto->actionFunction;
    }

    public function crudlerList(CrudlerControllerDTO $dto): JsonResponse
    {
        if (empty($dto->indexDTO)) {
            throw new Exception('Index not found!', 404);
        }

        $this->setFunctions($dto);

        $data = $this->_list($dto->indexDTO);

        return Response::json([
            'data' => $data
        ], 200);
    }

    public function crudlerShow(CrudlerControllerDTO $dto): JsonResponse
    {
        if (empty($dto->showDTO)) {
            throw new Exception('Show not found!', 404);
        }

        $this->setFunctions($dto);

        try {
            $data = $this->_show($dto->showDTO);
        } catch (Exception $e) {
            return Response::json(
                $e->getMessage(),
                $e->getCode() ?? 400
            );
        }

        return Response::json([
            'data' => $data
        ], 200);
    }

    public function crudlerCreate(CrudlerControllerDTO $dto): JsonResponse
    {
        if (empty($dto->createDTO)) {
            throw new Exception('Create not found!', 404);
        }

        $this->setFunctions($dto);

        try {
            $data = $this->_create($dto->createDTO);
        } catch (Exception $e) {
            return Response::json(
                $e->getMessage(),
                $e->getCode() ?? 400
            );
        }

        return Response::json([
            'data' => $data,
            'message' => $dto->createDTO->successMessage
        ], 200);
    }

    public function crudlerUpdate(CrudlerControllerDTO $dto): JsonResponse
    {
        if (empty($dto->updateDTO)) {
            throw new Exception('Update not found!', 404);
        }

        $this->setFunctions($dto);

        try {
            $data = $this->_update($dto->updateDTO);
        } catch (Exception $e) {
            return Response::json(
                $e->getMessage(),
                $e->getCode() ?? 400
            );
        }

        return Response::json([
            'data' => $data,
            'message' => $dto->updateDTO->successMessage
        ], 200);
    }

    public function crudlerDelete(CrudlerControllerDTO $dto): JsonResponse
    {
        if (empty($dto->deleteDTO)) {
            throw new Exception('Delete not found!', 404);
        }

        $this->setFunctions($dto);

        try {
            $data = $this->_delete($dto->deleteDTO);
        } catch (Exception $e) {
            return Response::json(
                $e->getMessage(),
                $e->getCode() ?? 400
            );
        }

        if (empty($data) || !isset($data['message'])) {
            return Response::json([
                'message' => $dto->deleteDTO->successMessage
            ], 200);
        }

        return Response::json([
            'message' => $data['message']
        ], $data['code']);
    }

    public function crudlerRestore(CrudlerControllerDTO $dto): JsonResponse
    {
        if (empty($dto->restoreDTO)) {
            throw new Exception('Restore not found!', 404);
        }

        $this->setFunctions($dto);

        try {
            $data = $this->_restore($dto->restoreDTO);
        } catch (Exception $e) {
            return Response::json(
                $e->getMessage(),
                $e->getCode() ?? 400
            );
        }

        if (empty($data) || !isset($data['message'])) {
            return Response::json([
                'message' => $dto->deleteDTO->successMessage
            ], 200);
        }

        return Response::json([
            'message' => $data['message']
        ], $data['code']);
    }
}
