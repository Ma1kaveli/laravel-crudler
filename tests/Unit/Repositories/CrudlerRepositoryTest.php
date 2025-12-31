<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;

use Crudler\Repositories\CrudlerRepository;
use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueConfigDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceConfigDTO;

use Core\DTO\FormDTO;
use Illuminate\Support\Facades\Schema;
use Exception;
use Tests\Fakes\FakeModel;

class CrudlerRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        FakeModel::create(['email' => 'test@example.com']);
    }

    public function test_crudler_is_unique(): void
    {
        $repo = new CrudlerRepository(FakeModel::class);

        $dto = CrudlerRepositoryDTO::start(
            uniqueDTO: new RepositoryUniqueDTO(
                $this->formDTO(['email' => 'new@example.com']),
                [
                    'email' => new RepositoryUniqueItemDTO(
                        'email',
                        new RepositoryUniqueConfigDTO('email')
                    )
                ]
            )
        );

        $this->assertTrue($repo->crudlerIsUnique($dto));
    }

    public function test_crudler_show_once_by_id(): void
    {
        $repo = new CrudlerRepository(FakeModel::class);

        $dto = CrudlerRepositoryDTO::start(
            showOnceDTO: new RepositoryShowOnceDTO(
                $this->formDTO(['id' => 1]),
                new RepositoryShowOnceConfigDTO()
            )
        );

        $model = $repo->crudlerShowOnceById($dto);

        $this->assertEquals(1, $model->id);
    }

    public function test_crudler_show_once_by_id_throws(): void
    {
        $this->expectException(Exception::class);

        $repo = new CrudlerRepository(FakeModel::class);

        $dto = CrudlerRepositoryDTO::start(
            showOnceDTO: new RepositoryShowOnceDTO(
                $this->formDTO(['id' => 999]),
                new RepositoryShowOnceConfigDTO(message: 'Not found')
            )
        );

        $repo->crudlerShowOnceById($dto);
    }

    private function formDTO(array $data): FormDTO
    {
        $dto = $this->createMock(FormDTO::class);

        foreach ($data as $key => $value) {
            $dto->$key = $value;
        }

        return $dto;
    }
}
