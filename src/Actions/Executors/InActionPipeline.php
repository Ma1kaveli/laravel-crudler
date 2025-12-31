<?php

namespace Crudler\Actions\Executors;

use Crudler\Actions\Context\ActionContext;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;

final class InActionPipeline
{
    public function execute(
        ActionUpdateDTO|ActionCreateDTO|ActionDeleteDTO|ActionRestoreDTO $dto,
        ActionContext $ctx,
        callable $serviceCall
    ): mixed {
        $in = $dto->inActionDTO;

        if ($in->beforeAction) {
            $ctx->beforeAction = ($in->beforeAction)($ctx->afterWithValidation);
            $dto = $dto->setFormDTO($ctx->beforeAction->formDTO);
        }

        $data = $serviceCall($dto);

        if ($in->afterAction) {
            $ctx->afterAction = ($in->afterAction)(
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
