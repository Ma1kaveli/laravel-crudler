<?php

namespace Tests\Unit\Resources\Builders;

use Crudler\Mapper\CrudlerMapper;
use Crudler\Resources\Builders\ResourceBuilder;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Tests\TestCase;

class ResourceBuilderTest extends TestCase
{
    public function test_builder_generates_from_array_generator_dto(): void
    {
        $builder = ResourceBuilder::make()
            ->_data(['id', 'name'])
            ->_additional(['extra' => fn () => 1])
            ->_methods(['ping' => fn () => 'pong']);

        $dto = $builder->generator();

        $this->assertInstanceOf(
            CrudlerResourceGeneratorDTO::class,
            $dto
        );

        $this->assertCount(2, $dto->data);
        $this->assertCount(1, $dto->additionalData);
        $this->assertCount(1, $dto->methods);
    }

    public function test_builder_generates_from_mapper_generator_dto(): void
    {
        $builder = ResourceBuilder::make()
            ->data(
                CrudlerMapper::make()->field('id')->field('name')
            )
            ->additional(CrudlerMapper::make()->field('extra', fn () => 1))
            ->methods(CrudlerMapper::make()->field('ping', fn () => 'pong'));

        $dto = $builder->generator();

        $this->assertInstanceOf(
            CrudlerResourceGeneratorDTO::class,
            $dto
        );

        $this->assertCount(2, $dto->data);
        $this->assertCount(1, $dto->additionalData);
        $this->assertCount(1, $dto->methods);
    }
}
