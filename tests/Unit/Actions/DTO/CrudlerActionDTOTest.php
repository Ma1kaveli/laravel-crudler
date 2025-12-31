<?php

namespace Tests\Unit\Actions\DTO;

use Crudler\Actions\DTO\CrudlerActionDTO;
use Crudler\Repositories\Interfaces\IRepositoryFunction;
use Crudler\Services\Interfaces\IServiceFunction;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Core\DTO\FormDTO;
use Tests\TestCase;

class CrudlerActionDTOTest extends TestCase
{
    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);
        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_start_method_creates_dto(): void
    {
        $repoMock = $this->createMock(IRepositoryFunction::class);
        $serviceMock = $this->createMock(IServiceFunction::class);

        $createDTO = ActionCreateDTO::start($this->makeFormDTO());

        $actionDTO = CrudlerActionDTO::start(
            $repoMock,
            $serviceMock,
            actionCreateDTO: $createDTO
        );

        $this->assertSame($repoMock, $actionDTO->repositoryFunc);
        $this->assertSame($serviceMock, $actionDTO->serviceFunc);
        $this->assertSame($createDTO, $actionDTO->actionCreateDTO);
        $this->assertNull($actionDTO->actionUpdateDTO);
        $this->assertNull($actionDTO->actionDeleteDTO);
        $this->assertNull($actionDTO->actionRestoreDTO);
        $this->assertNull($actionDTO->actionShowDTO);
    }
}
