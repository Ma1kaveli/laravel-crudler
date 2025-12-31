<?php

namespace Tests\Unit\Resources\DTO\Parts;

use Crudler\Resources\DTO\Parts\ResourceAdditionalDataDTO;
use Closure;
use InvalidArgumentException;
use Tests\TestCase;

class ResourceAdditionalDataDTOTest extends TestCase
{
    public function test_accepts_string_value(): void
    {
        $dto = new ResourceAdditionalDataDTO('test', 'key');

        $this->assertSame('test', $dto->value);
        $this->assertSame('key', $dto->key);
    }

    public function test_accepts_closure_value(): void
    {
        $closure = fn () => 'value';

        $dto = new ResourceAdditionalDataDTO($closure, 'key');

        $this->assertInstanceOf(Closure::class, $dto->value);
    }

    public function test_accepts_callable_and_wraps_into_closure(): void
    {
        $callable = 'strtoupper';

        $dto = new ResourceAdditionalDataDTO($callable, 'key');

        $this->assertInstanceOf(Closure::class, $dto->value);
        $this->assertSame('TEST', ($dto->value)('test'));
    }

    public function test_int_is_casted_to_string(): void
    {
        $dto = new ResourceAdditionalDataDTO(123);
        $this->assertSame('123', $dto->value);
    }
}
