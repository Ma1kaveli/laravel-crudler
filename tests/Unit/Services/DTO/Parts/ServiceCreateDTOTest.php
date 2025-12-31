<?php

namespace Tests\Unit\Services\DTO\Parts;

use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceMapperFieldDTO;
use Core\DTO\FormDTO;
use Tests\TestCase;

class ServiceCreateDTOTest extends TestCase
{
    public function test_start_creates_dto(): void
    {
        $formDTO = $this->createMock(FormDTO::class);

        $dto = ServiceCreateDTO::start($formDTO);

        $this->assertSame($formDTO, $dto->formDTO);
        $this->assertSame([], $dto->mapper);
    }

    public function test_mapper_is_wrapped_into_dto(): void
    {
        $formDTO = $this->createMock(FormDTO::class);

        $dto = ServiceCreateDTO::start($formDTO, [
            'email' => 'email',
            'name' => fn () => 'test',
        ]);

        $this->assertCount(2, $dto->mapper);
        $this->assertInstanceOf(ServiceMapperFieldDTO::class, $dto->mapper['email']);
        $this->assertInstanceOf(ServiceMapperFieldDTO::class, $dto->mapper['name']);
    }
}
