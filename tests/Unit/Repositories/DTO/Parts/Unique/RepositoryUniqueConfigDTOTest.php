<?php

namespace Tests\Unit\Repositories\DTO\Parts\Unique;

use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueConfigDTO;
use Tests\TestCase;

class RepositoryUniqueConfigDTOTest extends TestCase
{
    public function test_modifier_callable_converted_to_closure(): void
    {
        $dto = new RepositoryUniqueConfigDTO(
            column: 'email',
            modifier: fn ($v) => strtolower($v),
            isOrWhere: true
        );

        $this->assertInstanceOf(\Closure::class, $dto->modifier);
        $this->assertTrue($dto->isOrWhere);
    }
}
