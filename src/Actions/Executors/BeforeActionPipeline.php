<?php

namespace Crudler\Actions\Executors;

use Core\DTO\ExecutionOptionsDTO;
use Crudler\Actions\Context\ActionContext;
use Crudler\Actions\Constants\CrudlerPlaceUniqueEnum;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;

use Illuminate\Database\Eloquent\Model;

final class BeforeActionPipeline
{
    public function execute(
        ActionUpdateDTO|ActionCreateDTO|ActionDeleteDTO|ActionRestoreDTO $dto,
        callable $serviceAction,
        ?callable $uniqueCheck,
        ExecutionOptionsDTO $config,
        ?Model $data
    ): mixed {
        $ctx = new ActionContext();
        $before = $dto->beforeActionDTO ?? new BeforeActionDTO();

        $isUniqueAt = fn (CrudlerPlaceUniqueEnum $place) =>
            $uniqueCheck && $place === $dto->placeUnique;

        if ($isUniqueAt(CrudlerPlaceUniqueEnum::before_custom_validation)) {
            $uniqueCheck($dto->formDTO);
        }

        // Всегда устанавливаем beforeWithValidation в самом начале
        $ctx->beforeWithValidation = $before->beforeWithValidation
            ? ($before->beforeWithValidation)($dto->formDTO, $data)
            : BeforeWithValidationResult::create($dto->formDTO);

        $dto = $dto->setFormDTO($ctx->beforeWithValidation->formDTO);

        if ($isUniqueAt(CrudlerPlaceUniqueEnum::before_with_validation)) {
            $uniqueCheck($dto->formDTO);
        }

        if ($config->withValidation) {

            if ($isUniqueAt(CrudlerPlaceUniqueEnum::before_validation)) {
                $uniqueCheck($dto->formDTO);
            }

            if ($before->beforeValidation) {
                $ctx->beforeValidation = ($before->beforeValidation)($ctx->beforeWithValidation, $data);
                $dto = $dto->setFormDTO($ctx->beforeValidation->formDTO);
            } else {
                // Дефолт для beforeValidation, если нужно (аналогично другим)
                $ctx->beforeValidation =  BeforeValidationResult::create(
                    $ctx->beforeWithValidation->formDTO,
                    $ctx->beforeWithValidation
                );
                $dto = $dto->setFormDTO($ctx->beforeValidation->formDTO);
            }

            if ($isUniqueAt(CrudlerPlaceUniqueEnum::default)) {
                $uniqueCheck($dto->formDTO);
            }

            if ($before->afterValidation) {
                $ctx->afterValidation = ($before->afterValidation)($ctx->beforeValidation, $data);
                $dto = $dto->setFormDTO($ctx->afterValidation->formDTO);
            } else {
                // Дефолт для afterValidation
                $ctx->afterValidation = AfterValidationResult::create(
                    $ctx->beforeValidation->formDTO,
                    $ctx->beforeValidation
                );
                $dto = $dto->setFormDTO($ctx->afterValidation->formDTO);
            }
        }

        if ($isUniqueAt(CrudlerPlaceUniqueEnum::after_with_validation)) {
            $uniqueCheck($dto->formDTO);
        }

        if ($before->afterWithValidation) {
            $ctx->afterWithValidation = ($before->afterWithValidation)(
                $ctx->beforeWithValidation,
                $ctx->afterValidation,
                $data
            );
            $dto = $dto->setFormDTO($ctx->afterWithValidation->formDTO);
        } else {
            // Добавляем дефолт для afterWithValidation, если хук не указан
            $ctx->afterWithValidation = AfterWithValidationResult::create(
                $ctx->afterValidation->formDTO,
                $ctx->beforeWithValidation,
                $ctx->afterValidation,
                null
            );
            $dto = $dto->setFormDTO($ctx->afterWithValidation->formDTO);
        }

        return $serviceAction($dto, $ctx);
    }
}
