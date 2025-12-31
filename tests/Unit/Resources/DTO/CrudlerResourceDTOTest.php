<?php

namespace Tests\Unit\Resources\DTO;

use Crudler\Resources\DTO\CrudlerResourceDTO;
use Tests\TestCase;

class CrudlerResourceDTOTest extends TestCase
{
    public function test_can_be_created_via_constructor(): void
    {
        $dto = new CrudlerResourceDTO(null, null);

        $this->assertNull($dto->resource);
        $this->assertNull($dto->generator);
    }

    public function test_start_factory_method(): void
    {
        $dto = CrudlerResourceDTO::start();

        $this->assertInstanceOf(CrudlerResourceDTO::class, $dto);
        $this->assertNull($dto->resource);
        $this->assertNull($dto->generator);
    }
}
