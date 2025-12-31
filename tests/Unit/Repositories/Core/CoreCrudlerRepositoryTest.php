<?php

namespace Tests\Unit\Repositories\Core;

use Tests\TestCase;

use Crudler\Repositories\Core\CoreCrudlerRepository;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueConfigDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceConfigDTO;

use Core\DTO\FormDTO;
use Exception;
use Tests\Fakes\FakeModel;

class CoreCrudlerRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        FakeModel::create(['email' => 'test@example.com']);
    }

    public function test_is_unique_returns_true(): void
    {
        $repo = new CoreCrudlerRepository(FakeModel::class);

        $dto = new RepositoryUniqueDTO(
            $this->formDTO(['email' => 'new@example.com']),
            [
                'email' => new RepositoryUniqueItemDTO(
                    'email',
                    new RepositoryUniqueConfigDTO('email')
                )
            ]
        );

        $this->assertTrue($repo->_isUnique($dto));
    }

    public function test_is_unique_returns_false(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Запись с такими данными уже существует!');

        $repo = new CoreCrudlerRepository(FakeModel::class);

        $dto = new RepositoryUniqueDTO(
            $this->formDTO(['email' => 'test@example.com']),
            [
                'email' => new RepositoryUniqueItemDTO(
                    'email',
                    new RepositoryUniqueConfigDTO('email')
                )
            ]
        );

        $repo->_isUnique($dto);
    }

    public function test_show_once_by_id_returns_model(): void
    {
        $repo = new CoreCrudlerRepository(FakeModel::class);

        $dto = new RepositoryShowOnceDTO(
            $this->formDTO(['id' => 1]),
            new RepositoryShowOnceConfigDTO()
        );

        $model = $repo->_showOnceById($dto);

        $this->assertInstanceOf(FakeModel::class, $model);
        $this->assertEquals(1, $model->id);
    }

    public function test_show_once_by_id_throws_exception(): void
    {
        $this->expectException(Exception::class);

        $repo = new CoreCrudlerRepository(FakeModel::class);

        $dto = new RepositoryShowOnceDTO(
            $this->formDTO(['id' => 999]),
            new RepositoryShowOnceConfigDTO(message: 'Not found')
        );

        $repo->_showOnceById($dto);
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
