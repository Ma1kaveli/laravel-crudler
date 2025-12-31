<?php

namespace Tests\Unit\Actions\DTO\Parts;

use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Interfaces\IBeforeWithValidation;
use Core\DTO\FormDTO;
use Tests\TestCase;

class ActionDeleteDTOTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_before_with_validation_called(): void
    {
        $formDTO = $this->makeFormDTO();

        $beforeWithValidation = $this->createMock(IBeforeWithValidation::class);
        $beforeWithValidation->expects($this->once())
            ->method('__invoke')
            ->with($formDTO);

        $beforeDTO = new BeforeActionDTO(
            beforeWithValidation: $beforeWithValidation
        );

        $actionDTO = ActionDeleteDTO::start(
            formDTO: $formDTO,
            beforeActionDTO: $beforeDTO
        );

        if ($actionDTO->beforeActionDTO?->beforeWithValidation) {
            ($actionDTO->beforeActionDTO->beforeWithValidation)($formDTO);
        }

        $this->assertTrue(true);
    }
}
