<?php

namespace Crudler\Services\Core;

use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Crudler\Services\DTO\Parts\ServiceUpdateDTO;

use Exception;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;

class CoreCrudlerService extends BaseCrudlerService
{
    public function __construct(string $modelClass)
    {
        parent::__construct($modelClass);
    }

    protected function mapFormToAttributes(
        FormDTO $formDTO,
        array $mapper
    ): array {
        $result = [];

        foreach ($mapper as $mapped) {

            if ($mapped->isSimple()) {
                $field = $mapped->value;

                $result[Str::snake($field)] =
                    $formDTO->{Str::camel($field)} ?? null;

                continue;
            }

            if ($mapped->isCallable()) {
                $result[Str::snake($mapped->key)] =
                    ($mapped->value)($formDTO);

                continue;
            }

            throw new Exception('Invalid mapper configuration');
        }

        return $result;
    }


    /**
     * Summary of _create
     *
     * @param ServiceCreateDTO $dto
     *
     * @return Model|LogicException
     */
    public function _create(ServiceCreateDTO $dto): Model|LogicException
    {
        $attributes = $this->mapFormToAttributes(
            $dto->formDTO,
            $dto->mapper
        );

        return $this->create($attributes);
    }

    /**
     * Summary of _update
     *
     * @param ServiceUpdateDTO $dto
     *
     * @return Model|LogicException
     */
    public function _update(ServiceUpdateDTO $dto): Model|LogicException
    {
        if (empty($dto->data)) {
            throw new LogicException('Update not supported');
        }

        $attributes = $this->mapFormToAttributes(
            $dto->formDTO,
            $dto->mapper
        );

        return $this->update($dto->data, $attributes);
    }

    /**
     * Summary of _destroy
     *
     * @param ServiceDeleteDTO $dto
     *
     * @return array
     */
    public function _destroy(ServiceDeleteDTO $dto): array
    {
        if (empty($dto->data)) {
            throw new LogicException('Destroy not supported');
        }

        return $this->destroy(
            data: $dto->data,
            config: $dto->config,
            alreadyDeleteMessage: $dto->alreadyDeleteMessage,
            successMessage: $dto->successMessage,
            errorMessage: $dto->errorMessage
        );
    }

    /**
     * Summary of _restore
     *
     * @param ServiceRestoreDTO $dto
     *
     * @return array
     */
    public function _restore(ServiceRestoreDTO $dto): array
    {
        if (empty($dto->data)) {
            throw new LogicException('Restore not supported');
        }

        return $this->restore(
            data: $dto->data,
            config: $dto->config,
            notDeleteMessage: $dto->notDeleteMessage,
            successMessage: $dto->successMessage,
            errorMessage: $dto->errorMessage
        );
    }

    /**
     * Summary of _forceDelete
     *
     * @param ServiceDeleteDTO $dto
     *
     * @return void
     */
    public function _forceDelete(ServiceDeleteDTO $dto): void
    {
        if (empty($dto->data)) {
            throw new LogicException('Force delete not supported');
        }

        $this->forceDelete($dto->data);
    }
}
