<?php

namespace Tests\Unit\Repositories\Builders;

use Crudler\Repositories\Builders\RepositoryShowOnceBuilder;
use Core\DTO\FormDTO;
use Tests\TestCase;

class RepositoryShowOnceBuilderTest extends TestCase
{
    public function test_builder_creates_dto(): void
    {
        $dto = RepositoryShowOnceBuilder::make($this->mockFormDTO())
            ->with(['user'])
            ->withTrashed()
            ->message('Not found')
            ->build();

        $this->assertFalse($dto->isEmpty());
    }

    private function mockFormDTO(): FormDTO
    {
        return $this->createMock(FormDTO::class);
    }
}
