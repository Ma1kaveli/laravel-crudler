<?php

namespace Tests\Unit\Resources\Core;

use Crudler\Resources\Core\CoreCrudlerResource;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Tests\TestCase;

class CoreCrudlerResourceMethodsTest extends TestCase
{
    public function test_dynamic_method_call(): void
    {
        $dto = CrudlerResourceGeneratorDTO::start(
            data: [],
            additionalData: [],
            methods: [
                'hello' => fn ($r, $name) => "Hello $name"
            ]
        );

        $res = new CoreCrudlerResource([], $dto);

        $this->assertSame(
            'Hello John',
            $res->call('hello', 'John')
        );
    }

    public function test_calling_unknown_method_throws(): void
    {
        $this->expectException(\Exception::class);

        $dto = CrudlerResourceGeneratorDTO::start(
            data: [],
            additionalData: [],
            methods: []
        );

        $res = new CoreCrudlerResource([], $dto);
        $res->call('nope');
    }
}
