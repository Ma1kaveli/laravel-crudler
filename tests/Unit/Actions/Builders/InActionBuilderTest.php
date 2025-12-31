<?php

namespace Tests\Unit\Actions\Builders;

use Crudler\Actions\Builders\InActionBuilder;
use Crudler\Actions\DTO\Parts\InAction\InActionDTO;
use Crudler\Actions\Interfaces\IBeforeAction;
use Crudler\Actions\Interfaces\IAfterAction;
use Crudler\Actions\Interfaces\IReturn;
use Tests\TestCase;

class InActionBuilderTest extends TestCase
{
    public function test_build(): void
    {
        $beforeMock = $this->createMock(IBeforeAction::class);
        $afterMock = $this->createMock(IAfterAction::class);
        $returnMock = $this->createMock(IReturn::class);

        $builder = InActionBuilder::make()
            ->setBeforeAction($beforeMock)
            ->setAfterAction($afterMock)
            ->setReturn($returnMock);

        $dto = $builder->build();

        $this->assertInstanceOf(InActionDTO::class, $dto);
        $this->assertSame($beforeMock, $dto->beforeAction);
        $this->assertSame($afterMock, $dto->afterAction);
        $this->assertSame($returnMock, $dto->return);
    }
}
