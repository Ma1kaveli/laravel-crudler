<?php

namespace Tests\Unit\Repositories\DTO\Parts\ShowOnceById;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceConfigDTO;
use Tests\TestCase;

class RepositoryShowOnceConfigDTOTest extends TestCase
{
    public function test_construct_with_defaults(): void
    {
        $dto = new RepositoryShowOnceConfigDTO();

        $this->assertFalse($dto->withTrashed);
        $this->assertNull($dto->with);
        $this->assertNull($dto->withCount);
        $this->assertNotEmpty($dto->message);
        $this->assertNull($dto->query);
    }

    public function test_query_callable_converted_to_closure(): void
    {
        $dto = new RepositoryShowOnceConfigDTO(
            query: fn () => null
        );

        $this->assertInstanceOf(\Closure::class, $dto->query);
    }
}
