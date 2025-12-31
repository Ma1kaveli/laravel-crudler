<?php

namespace Tests\Unit\Services\DTO;

use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Core\DTO\FormDTO;
use Tests\TestCase;

class CrudlerServiceDTOTest extends TestCase
{
    public function test_start_empty(): void
    {
        $dto = CrudlerServiceDTO::start();

        $this->assertNull($dto->createDTO);
        $this->assertNull($dto->updateDTO);
    }

    public function test_set_create_dto_returns_new_instance(): void
    {
        $formDTO = $this->createMock(FormDTO::class);
        $createDTO = ServiceCreateDTO::start($formDTO);

        $dto = CrudlerServiceDTO::start();
        $new = $dto->setCreateDTO($createDTO);

        $this->assertNotSame($dto, $new);
        $this->assertSame($createDTO, $new->createDTO);
    }
}
