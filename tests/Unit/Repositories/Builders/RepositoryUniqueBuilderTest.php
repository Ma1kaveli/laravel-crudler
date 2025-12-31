<?php

namespace Tests\Unit\Repositories\Builders;

use Crudler\Repositories\Builders\RepositoryUniqueBuilder;
use Core\DTO\FormDTO;
use Tests\TestCase;

class RepositoryUniqueBuilderTest extends TestCase
{
    public function test_add_unique_item(): void
    {
        $builder = RepositoryUniqueBuilder::make($this->mockFormDTO())
            ->addUniqueItem('email', 'email');

        $dto = $builder->build();

        $this->assertFalse($dto->isEmpty());
    }

    private function mockFormDTO(): FormDTO
    {
        return $this->createMock(FormDTO::class);
    }
}
