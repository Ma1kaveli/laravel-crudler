<?php

namespace Tests\Unit\Repositories\DTO;

use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Tests\TestCase;

class CrudlerRepositoryDTOTest extends TestCase
{
    public function test_start(): void
    {
        $dto = CrudlerRepositoryDTO::start();

        $this->assertNull($dto->uniqueDTO);
        $this->assertNull($dto->showOnceDTO);
    }
}
