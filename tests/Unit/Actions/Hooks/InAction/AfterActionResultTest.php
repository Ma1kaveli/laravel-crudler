<?php

namespace Tests\Unit\Actions\Hooks\InAction;

use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;
use Crudler\Actions\Hooks\InAction\AfterActionResult;
use Tests\TestCase;

class AfterActionResultTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_create_with_model(): void
    {
        $formDTO = $this->makeFormDTO();
        $prev = BeforeActionResult::create(
            $formDTO,
            AfterWithValidationResult::create(
                $formDTO,
                $this->createMock(BeforeWithValidationResult::class)
            )
        );
        $model = $this->createMock(Model::class);

        $result = AfterActionResult::create($formDTO, $prev, $model, 'after_action_result');

        $this->assertSame($formDTO, $result->formDTO);
        $this->assertSame($prev, $result->previous);
        $this->assertSame($model, $result->data);
        $this->assertSame('after_action_result', $result->result);
    }
}
