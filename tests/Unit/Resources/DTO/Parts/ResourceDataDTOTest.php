<?php

namespace Tests\Unit\Resources\DTO\Parts;

use Crudler\Resources\DTO\Parts\ResourceDataDTO;
use Tests\TestCase;
use Closure;
use InvalidArgumentException;

class ResourceDataDTOTest extends TestCase
{
    public function test_accepts_string(): void
    {
        $dto = ResourceDataDTO::start('name', 'key');

        $this->assertSame('name', $dto->value);
        $this->assertSame('key', $dto->key);
    }

    public function test_accepts_array(): void
    {
        $dto = ResourceDataDTO::start(['a' => 1], 'key');

        $this->assertSame(['a' => 1], $dto->value);
    }

    public function test_accepts_closure(): void
    {
        $dto = ResourceDataDTO::start(fn () => 'test');

        $this->assertInstanceOf(Closure::class, $dto->value);
    }

    public function test_accepts_callable_and_wraps(): void
    {
        $dto = ResourceDataDTO::start('strlen');

        $this->assertInstanceOf(Closure::class, $dto->value);
        $this->assertSame(4, ($dto->value)('test'));
    }

    public function test_int_is_casted_to_string(): void
    {
        $dto = ResourceDataDTO::start(123);

        $this->assertSame('123', $dto->value);
    }
}
