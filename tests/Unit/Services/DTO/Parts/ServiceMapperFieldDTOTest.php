<?php

namespace Tests\Unit\Services\DTO\Parts;

use Crudler\Services\DTO\Parts\ServiceMapperFieldDTO;
use Tests\TestCase;

class ServiceMapperFieldDTOTest extends TestCase
{
    public function test_simple_string_mapper(): void
    {
        $dto = new ServiceMapperFieldDTO('email', 0);

        $this->assertTrue($dto->isSimple());
        $this->assertFalse($dto->isCallable());
        $this->assertSame('email', $dto->value);
    }

    public function test_callable_mapper(): void
    {
        $callable = fn () => 'value';

        $dto = new ServiceMapperFieldDTO($callable, 'email');

        $this->assertTrue($dto->isCallable());
        $this->assertFalse($dto->isSimple());
        $this->assertIsCallable($dto->value);
    }

    public function test_invalid_mapper_throws_exception(): void
    {
        $this->expectException(\TypeError::class);

        new ServiceMapperFieldDTO([], 'email');
    }
}
