<?php

namespace Tests\Unit\Actions\Hooks\BeforeAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Tests\TestCase;

class AfterValidationResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_and_properties(): void
    {
        $formDTO = $this->makeFormDTO();
        $prev = BeforeValidationResult::create($formDTO, BeforeWithValidationResult::create($formDTO));

        $result = AfterValidationResult::create($formDTO, $prev, 'after_result');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame($prev, $result->previous);
        $this->assertSame('after_result', $result->result);
    }
}
