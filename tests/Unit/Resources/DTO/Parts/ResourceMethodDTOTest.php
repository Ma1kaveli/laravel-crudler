<?php

namespace Tests\Unit\Resources\DTO\Parts;

use Crudler\Resources\DTO\Parts\ResourceMethodDTO;
use Tests\TestCase;
use Closure;

class ResourceMethodDTOTest extends TestCase
{
    public function test_accepts_closure(): void
    {
        $closure = fn () => 'ok';

        $dto = ResourceMethodDTO::start($closure);

        $this->assertSame($closure, $dto->callback);
    }

    public function test_accepts_callable_and_wraps_into_closure(): void
    {
        $dto = ResourceMethodDTO::start('strtolower');

        $this->assertInstanceOf(Closure::class, $dto->callback);
        $this->assertSame('test', ($dto->callback)('TEST'));
    }
}
