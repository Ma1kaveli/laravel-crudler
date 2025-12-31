<?php

namespace Tests\Unit\Services\DTO\Parts;

use Crudler\Services\DTO\Parts\ServiceUpdateDTO;
use Crudler\Services\DTO\Parts\ServiceMapperFieldDTO;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class ServiceUpdateDTOTest extends TestCase
{
    public function test_start_creates_update_dto(): void
    {
        $model = $this->createMock(Model::class);
        $formDTO = $this->createMock(FormDTO::class);

        $dto = ServiceUpdateDTO::start($model, $formDTO);

        $this->assertSame($model, $dto->data);
        $this->assertSame($formDTO, $dto->formDTO);
        $this->assertSame([], $dto->mapper);
    }

    public function test_mapper_wrapping_works(): void
    {
        $formDTO = $this->createMock(FormDTO::class);

        $dto = ServiceUpdateDTO::start(null, $formDTO, [
            'email' => 'email',
        ]);

        $this->assertInstanceOf(ServiceMapperFieldDTO::class, $dto->mapper['email']);
    }
}
