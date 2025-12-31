<?php

namespace Tests\Unit\Actions\DTO\Parts;

use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Interfaces\IBeforeWithValidation;
use Crudler\Actions\Interfaces\IBeforeValidation;
use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Tests\TestCase;

class ActionCreateDTOTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_before_hooks_called(): void
    {
        $formDTO = $this->makeFormDTO();

        $beforeWithValidation = $this->createMock(IBeforeWithValidation::class);
        $beforeWithValidationResult = $this->createMock(BeforeWithValidationResult::class);

        $beforeWithValidation->expects($this->once())
            ->method('__invoke')
            ->with($formDTO)
            ->willReturn($beforeWithValidationResult);

        $beforeValidation = $this->createMock(IBeforeValidation::class);
        $beforeValidationResult = $this->createMock(BeforeValidationResult::class);

        $beforeValidation->expects($this->once())
            ->method('__invoke')
            ->with($beforeWithValidationResult)
            ->willReturn($beforeValidationResult);

        $beforeDTO = new BeforeActionDTO(
            beforeWithValidation: $beforeWithValidation,
            beforeValidation: $beforeValidation
        );

        $actionDTO = ActionCreateDTO::start(
            formDTO: $formDTO,
            beforeActionDTO: $beforeDTO
        );

        $beforeWithValidationResultReturned = ($actionDTO->beforeActionDTO->beforeWithValidation)($formDTO);
        ($actionDTO->beforeActionDTO->beforeValidation)($beforeWithValidationResultReturned);

        $this->assertTrue(true);
    }
}
