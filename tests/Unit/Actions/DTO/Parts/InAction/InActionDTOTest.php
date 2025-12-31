<?php

namespace Tests\Unit\Actions\DTO\Parts\InAction;

use Crudler\Actions\DTO\Parts\InAction\InActionDTO;
use Crudler\Actions\Interfaces\IBeforeAction;
use Crudler\Actions\Interfaces\IAfterAction;
use Crudler\Actions\Interfaces\IReturn;
use Tests\TestCase;

class InActionDTOTest extends TestCase
{
    public function test_hooks_storage(): void
    {
        $beforeAction = $this->createMock(IBeforeAction::class);
        $afterAction = $this->createMock(IAfterAction::class);
        $returnAction = $this->createMock(IReturn::class);

        $dto = new InActionDTO(
            beforeAction: $beforeAction,
            afterAction: $afterAction,
            return: $returnAction
        );

        $this->assertSame($beforeAction, $dto->beforeAction);
        $this->assertSame($afterAction, $dto->afterAction);
        $this->assertSame($returnAction, $dto->return);
    }
}
