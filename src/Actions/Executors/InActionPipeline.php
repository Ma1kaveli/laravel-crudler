<?php

namespace Crudler\Actions\Executors;

use Crudler\Actions\Context\ActionContext;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Actions\DTO\Parts\InAction\InActionDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Hooks\InAction\AfterActionResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;

use Illuminate\Database\Eloquent\Model;

final class InActionPipeline
{
    public function execute(
        ActionUpdateDTO|ActionCreateDTO|ActionDeleteDTO|ActionRestoreDTO $dto,
        ActionContext $ctx,
        callable $serviceCall,
        ?Model $data
    ): mixed {
        $in = $dto->inActionDTO ?? new InActionDTO();

        // Добавляем дефолт для afterWithValidation, если не установлен (на случай, если в BeforePipeline не был хук)
        if (empty($ctx->afterWithValidation)) {
            $ctx->afterWithValidation = AfterWithValidationResult::create(
                $dto->formDTO,
                $ctx->beforeWithValidation ?? BeforeWithValidationResult::create($dto->formDTO), // fallback на beforeWithValidation или базовый
                $ctx->afterValidation ?? null,
                null
            );
            $dto = $dto->setFormDTO($ctx->afterWithValidation->formDTO);
        }

        if ($in->beforeAction) {
            $ctx->beforeAction = ($in->beforeAction)($ctx->afterWithValidation, $data);
            $dto = $dto->setFormDTO($ctx->beforeAction->formDTO);
        } else {
            // Дефолт, если хук не указан
            $ctx->beforeAction = BeforeActionResult::create(
                $ctx->afterWithValidation->formDTO,
                $ctx->afterWithValidation
            );
            $dto = $dto->setFormDTO($ctx->beforeAction->formDTO);
        }

        $data = $serviceCall($dto);

        if ($in->afterAction) {
            $ctx->afterAction = ($in->afterAction)(
                $ctx->beforeAction,
                $data
            );
            $dto = $dto->setFormDTO($ctx->afterAction->formDTO);
        } else {
            // Дефолт, если хук не указан
            $ctx->afterAction = AfterActionResult::create(
                $dto->formDTO,
                $ctx->beforeAction,
                $data
            );
            $dto = $dto->setFormDTO($ctx->afterAction->formDTO);
        }

        return $in->return
            ? ($in->return)($ctx->afterAction)->result
            : $data;
    }
}
