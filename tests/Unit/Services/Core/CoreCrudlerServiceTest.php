<?php

namespace Tests\Unit\Crudler;

use Crudler\Services\Core\CoreCrudlerService;
use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Core\DTO\FormDTO;
use Crudler\Mapper\CrudlerMapper;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Crudler\Services\DTO\Parts\ServiceMapperFieldDTO;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class CoreCrudlerServiceTest extends TestCase
{
    use WithFaker;

    protected function makeFormDTO(): FormDTO
    {
        $userMock = $this->createMock(\Illuminate\Contracts\Auth\Authenticatable::class);

        return new class($userMock, 1, 2) extends FormDTO {};
    }

    public function test_map_form_to_attributes_simple_and_callable(): void
    {
        $dto = $this->makeFormDTO();
        $dto->name = 'John';

        $mapper = [
            new ServiceMapperFieldDTO('name', 0),
            new ServiceMapperFieldDTO(fn(FormDTO $d) => strtoupper($d->name), 'upper'),
        ];

        $service = new CoreCrudlerService(\stdClass::class);
        $result = $this->invokeMethod($service, 'mapFormToAttributes', [$dto, $mapper]);

        $this->assertSame('John', $result['name']);
        $this->assertSame('JOHN', $result['upper']);
    }

    /**
     * Вспомогательный метод для вызова protected/private методов
     */
    protected function invokeMethod(object $object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

