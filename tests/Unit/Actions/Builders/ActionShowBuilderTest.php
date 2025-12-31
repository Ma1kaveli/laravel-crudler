<?php

namespace Tests\Unit\Actions\Builders;

use Core\DTO\OnceDTO;
use Crudler\Actions\Builders\ActionShowBuilder;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\Interfaces\IShowAfterAction;
use Crudler\Actions\Interfaces\IShowReturnAction;
use Tests\TestCase;

class ActionShowBuilderTest extends TestCase
{
    protected function makeOnceDTO(): OnceDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, $userMock, 1, 2) extends OnceDTO {};
    }

    public function test_build(): void
    {
        $onceDTO = $this->makeOnceDTO();
        $afterMock = $this->createMock(IShowAfterAction::class);
        $returnMock = $this->createMock(IShowReturnAction::class);

        $builder = ActionShowBuilder::make($onceDTO)
            ->after($afterMock)
            ->return($returnMock);

        $dto = $builder->build();

        $this->assertInstanceOf(ActionShowDTO::class, $dto);
        $this->assertSame($onceDTO, $dto->onceDTO);
        $this->assertSame($afterMock, $dto->after);
        $this->assertSame($returnMock, $dto->return);
    }
}
