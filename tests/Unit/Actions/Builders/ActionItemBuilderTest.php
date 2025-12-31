<?php

namespace Tests\Unit\Actions\Builders;

use Core\DTO\FormDTO;
use Core\DTO\OnceDTO;
use Crudler\Actions\Builders\ActionItemBuilder;
use Crudler\Actions\Constants\CrudlerPlaceUniqueEnum;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ActionItemBuilderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('crudler.actions.error_create_message', 'ERROR CREATE MESSAGE');
        Config::set('crudler.actions.error_update_message', 'ERROR UPDATE MESSAGE');
        Config::set('crudler.actions.error_delete_message', 'ERROR DELETE MESSAGE');
        Config::set('crudler.actions.error_restore_message', 'ERROR RESTORE MESSAGE');
    }

    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    protected function makeOnceDTO(): OnceDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, $userMock, 1, 2) extends OnceDTO {};
    }

    public function test_build_create_and_update(): void
    {
        $formDTO = $this->makeFormDTO();
        $builder = ActionItemBuilder::make($formDTO)
            ->withoutValidation()
            ->getFunc()
            ->withoutTransaction()
            ->placeUnique(CrudlerPlaceUniqueEnum::after_validation)
            ->errorMessage('error')
            ->successLog('success')
            ->errorLog('log');

        $createDTO = $builder->buildCreate();
        $updateDTO = $builder->buildUpdate();

        foreach ([$createDTO, $updateDTO] as $dto) {
            $this->assertSame($formDTO, $dto->formDTO);
            $this->assertFalse($dto->withValidation);
            $this->assertTrue($dto->getFunc);
            $this->assertFalse($dto->withTransaction);
            $this->assertSame(CrudlerPlaceUniqueEnum::after_validation, $dto->placeUnique);
            $this->assertSame('error', $dto->errorMessage);
            $this->assertSame('success', $dto->successLog);
            $this->assertSame('log', $dto->errorLog);
        }
    }

    public function test_build_delete_and_restore(): void
    {
        $onceDTO = $this->makeOnceDTO();
        $builder = ActionItemBuilder::make($onceDTO);

        $deleteDTO = $builder->buildDelete();
        $restoreDTO = $builder->buildRestore();

        $this->assertSame($onceDTO, $deleteDTO->formDTO);
        $this->assertSame($onceDTO, $restoreDTO->formDTO);
    }

    public function test_build_delete_with_formDTO_throws(): void
    {
        $this->expectException(\Exception::class);
        $builder = ActionItemBuilder::make($this->makeFormDTO());
        $builder->buildDelete();
    }

    public function test_build_restore_with_formDTO_throws(): void
    {
        $this->expectException(\Exception::class);
        $builder = ActionItemBuilder::make($this->makeFormDTO());
        $builder->buildRestore();
    }
}
