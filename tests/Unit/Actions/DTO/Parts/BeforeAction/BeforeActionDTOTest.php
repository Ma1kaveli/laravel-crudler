<?php

namespace Tests\Unit\Actions\DTO\Parts\BeforeAction;

use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Interfaces\IBeforeWithValidation;
use Crudler\Actions\Interfaces\IBeforeValidation;
use Crudler\Actions\Interfaces\IAfterValidation;
use Crudler\Actions\Interfaces\IAfterWithValidation;
use Core\DTO\FormDTO;
use Tests\TestCase;

class BeforeActionDTOTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_hooks_storage_and_call(): void
    {
        $formDTO = $this->makeFormDTO();

        $beforeWithValidation = $this->createMock(IBeforeWithValidation::class);
        $beforeValidation = $this->createMock(IBeforeValidation::class);
        $afterValidation = $this->createMock(IAfterValidation::class);
        $afterWithValidation = $this->createMock(IAfterWithValidation::class);

        $dto = new BeforeActionDTO(
            beforeWithValidation: $beforeWithValidation,
            beforeValidation: $beforeValidation,
            afterValidation: $afterValidation,
            afterWithValidation: $afterWithValidation
        );

        $this->assertSame($beforeWithValidation, $dto->beforeWithValidation);
        $this->assertSame($beforeValidation, $dto->beforeValidation);
        $this->assertSame($afterValidation, $dto->afterValidation);
        $this->assertSame($afterWithValidation, $dto->afterWithValidation);
    }
}
