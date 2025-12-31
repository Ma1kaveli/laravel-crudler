<?php

namespace Tests\Unit\Actions\Builders;

use Crudler\Actions\Builders\BeforeActionBuilder;
use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Interfaces\IBeforeWithValidation;
use Crudler\Actions\Interfaces\IBeforeValidation;
use Crudler\Actions\Interfaces\IAfterValidation;
use Crudler\Actions\Interfaces\IAfterWithValidation;
use Tests\TestCase;

class BeforeActionBuilderTest extends TestCase
{
    public function test_build(): void
    {
        $beforeWithMock = $this->createMock(IBeforeWithValidation::class);
        $beforeMock = $this->createMock(IBeforeValidation::class);
        $afterMock = $this->createMock(IAfterValidation::class);
        $afterWithMock = $this->createMock(IAfterWithValidation::class);

        $builder = BeforeActionBuilder::make()
            ->setBeforeWithValidation($beforeWithMock)
            ->setBeforeValidation($beforeMock)
            ->setAfterValidation($afterMock)
            ->setAfterWithValidation($afterWithMock);

        $dto = $builder->build();

        $this->assertInstanceOf(BeforeActionDTO::class, $dto);
        $this->assertSame($beforeWithMock, $dto->beforeWithValidation);
        $this->assertSame($beforeMock, $dto->beforeValidation);
        $this->assertSame($afterMock, $dto->afterValidation);
        $this->assertSame($afterWithMock, $dto->afterWithValidation);
    }
}
