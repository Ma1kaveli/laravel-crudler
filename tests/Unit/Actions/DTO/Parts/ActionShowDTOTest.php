<?php

namespace Tests\Unit\Actions\DTO\Parts;

use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\Interfaces\IShowAfterAction;
use Core\DTO\OnceDTO;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class ActionShowDTOTest extends TestCase
{
    public function test_show_after_action_called(): void
    {
        $onceDTO = $this->createMock(OnceDTO::class);
        $model = $this->createMock(Model::class);

        $after = $this->createMock(IShowAfterAction::class);
        $after->expects($this->once())
            ->method('__invoke')
            ->with($onceDTO, $model);

        $actionShowDTO = ActionShowDTO::start(
            onceDTO: $onceDTO,
            after: $after
        );

        ($actionShowDTO->after)($onceDTO, $model);

        $this->assertTrue(true);
    }
}
