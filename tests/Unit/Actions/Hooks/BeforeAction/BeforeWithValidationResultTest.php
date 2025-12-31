<?php

namespace Tests\Unit\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Tests\TestCase;

class BeforeWithValidationResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_and_properties(): void
    {
        $formDTO = $this->makeFormDTO();
        $result = BeforeWithValidationResult::create($formDTO, 'some_result');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame('some_result', $result->result);
    }
}
