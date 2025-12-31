<?php

namespace Tests\Unit\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Tests\TestCase;

class AfterWithValidationResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_and_properties(): void
    {
        $formDTO = $this->makeFormDTO();
        $before = BeforeWithValidationResult::create($formDTO);
        $afterValidation = AfterValidationResult::create($formDTO, $this->createMock(\Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult::class));

        $result = AfterWithValidationResult::create($formDTO, $before, $afterValidation, 'final_result');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame($before, $result->beforeWithValidationResult);
        $this->assertSame($afterValidation, $result->afterValidationResult);
        $this->assertSame('final_result', $result->result);
    }
}
