<?php

namespace Tests\Unit\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Tests\TestCase;

class BeforeValidationResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_and_properties(): void
    {
        $formDTO = $this->makeFormDTO();
        $previous = BeforeWithValidationResult::create($formDTO, 'prev_result');

        $result = BeforeValidationResult::create($formDTO, $previous, 'my_result');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame($previous, $result->previous);
        $this->assertSame('my_result', $result->result);
    }
}
