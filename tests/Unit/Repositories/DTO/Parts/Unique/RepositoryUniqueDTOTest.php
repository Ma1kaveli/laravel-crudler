<?php

namespace Tests\Unit\Repositories\DTO\Parts\Unique;

use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Repositories\Interfaces\IUniqueItemCallable;
use Core\DTO\FormDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueConfigDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;
use Tests\TestCase;

class RepositoryUniqueDTOTest extends TestCase
{
    public function test_empty_unique(): void
    {
        $dto = new RepositoryUniqueDTO($this->mockFormDTO());

        $this->assertTrue($dto->isEmpty());
        $this->assertFalse($dto->isCallable());
    }

    public function test_callable_unique(): void
    {
        $dto = new RepositoryUniqueDTO(
            $this->mockFormDTO(),
            new class implements IUniqueItemCallable {
                public function __invoke(FormDTO $formDTO): array {
                    return [];
                }
            }
        );

        $this->assertTrue($dto->isCallable());
    }

    public function test_unique_dto_to_array(): void
    {
        $dto = new RepositoryUniqueDTO(
            $this->formDTO(['email' => 'test@test.com']),
            [
                'email' => new RepositoryUniqueItemDTO(
                    'email',
                    new RepositoryUniqueConfigDTO('email')
                )
            ]
        );

        $this->assertEquals([
            'email' => [
                'column' => 'email',
                'modifier' => null,
                'is_or_where' => false,
            ]
        ], $dto->toArray());
    }


    private function mockFormDTO(): FormDTO
    {
        return $this->createMock(FormDTO::class);
    }

    private function formDTO(array $data): FormDTO
    {
        $dto = $this->mockFormDTO();

        foreach ($data as $key => $value) {
            $dto->$key = $value;
        }

        return $dto;
    }
}
