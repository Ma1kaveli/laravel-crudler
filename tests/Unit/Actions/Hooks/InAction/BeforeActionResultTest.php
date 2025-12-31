<?php

namespace Tests\Unit\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;
use Tests\TestCase;

class BeforeActionResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_and_properties(): void
    {
        $formDTO = $this->makeFormDTO();
        $prev = AfterWithValidationResult::create(
            $formDTO,
            $this->createMock(BeforeWithValidationResult::class)
        );

        $result = BeforeActionResult::create($formDTO, $prev, 'before_action_result');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame($prev, $result->previous);
        $this->assertSame('before_action_result', $result->result);
    }
}
