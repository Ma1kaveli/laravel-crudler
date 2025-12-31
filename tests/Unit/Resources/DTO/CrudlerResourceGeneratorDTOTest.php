<?php

namespace Tests\Unit\Resources\DTO;

use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Crudler\Resources\DTO\Parts\ResourceDataDTO;
use Crudler\Resources\DTO\Parts\ResourceAdditionalDataDTO;
use Crudler\Resources\DTO\Parts\ResourceMethodDTO;
use Tests\TestCase;

class CrudlerResourceGeneratorDTOTest extends TestCase
{
    public function test_start_wraps_arrays_into_dtos(): void
    {
        $dto = CrudlerResourceGeneratorDTO::start(
            data: ['name' => 'id'],
            additionalData: ['extra' => fn () => 'x'],
            methods: ['upper' => 'strtoupper']
        );

        $this->assertInstanceOf(ResourceDataDTO::class, $dto->data['name']);
        $this->assertInstanceOf(ResourceAdditionalDataDTO::class, $dto->additionalData['extra']);
        $this->assertInstanceOf(ResourceMethodDTO::class, $dto->methods['upper']);
    }

    public function test_from_array_works(): void
    {
        $dto = CrudlerResourceGeneratorDTO::fromArray([
            'data' => ['id' => 'id'],
            'additional_data' => [],
            'methods' => [],
        ]);

        $this->assertArrayHasKey('id', $dto->data);
    }

    public function test_set_additional_data_returns_new_instance(): void
    {
        $dto = CrudlerResourceGeneratorDTO::start(
            data: ['id' => 'id']
        );

        $newDto = $dto->setAdditionalData([
            'foo' => 'bar'
        ]);

        $this->assertNotSame($dto, $newDto);
        $this->assertArrayHasKey('foo', $newDto->additionalData);
        $this->assertArrayNotHasKey('foo', $dto->additionalData);
    }
}
