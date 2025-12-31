<?php

namespace Tests\Unit\Repositories\DTO\Parts\ShowOnceById;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceConfigDTO;
use Core\DTO\FormDTO;
use Tests\TestCase;

class RepositoryShowOnceDTOTest extends TestCase
{
    public function test_empty_config(): void
    {
        $dto = new RepositoryShowOnceDTO(
            $this->mockFormDTO()
        );

        $this->assertTrue($dto->isEmpty());
        $this->assertFalse($dto->isCallable());
    }

    public function test_callable_config(): void
    {
        $dto = new RepositoryShowOnceDTO(
            $this->mockFormDTO(),
            fn () => new RepositoryShowOnceConfigDTO()
        );

        $this->assertTrue($dto->isCallable());
    }

    private function mockFormDTO(): FormDTO
    {
        return $this->createMock(FormDTO::class);
    }
}
