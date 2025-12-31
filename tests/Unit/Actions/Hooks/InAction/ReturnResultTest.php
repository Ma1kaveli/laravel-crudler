<?php

namespace Tests\Unit\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;
use Crudler\Actions\Hooks\InAction\ReturnResult;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Tests\TestCase;

class ReturnResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_and_properties(): void
    {
        $formDTO = $this->makeFormDTO();
        $prev = BeforeActionResult::create(
            $formDTO,
            AfterWithValidationResult::create(
                $formDTO,
                $this->createMock(BeforeWithValidationResult::class)
            )
        );

        $result = ReturnResult::create($formDTO, $prev, 'return_value');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame($prev, $result->previous);
        $this->assertSame('return_value', $result->result);
    }
}
