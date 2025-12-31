<?php

namespace Tests\Unit\Resources\Core;

use Crudler\Resources\Core\CoreCrudlerResource;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Tests\TestCase;

class CoreCrudlerResourceDataTest extends TestCase
{
    public function test_string_field_is_resolved_from_resource(): void
    {
        $resource = ['id' => 10];

        $dto = CrudlerResourceGeneratorDTO::start(
            data: ['id'],
            additionalData: [],
            methods: []
        );

        $res = new CoreCrudlerResource($resource, $dto);

        $this->assertSame(
            ['id' => 10],
            $res->toArray(null)
        );
    }

    public function test_closure_field_is_resolved(): void
    {
        $resource = ['a' => 2, 'b' => 3];

        $dto = CrudlerResourceGeneratorDTO::start(
            data: [
                'sum' => fn ($r) => $r['a'] + $r['b']
            ],
            additionalData: [],
            methods: []
        );

        $res = new CoreCrudlerResource($resource, $dto);

        $this->assertSame(
            ['sum' => 5],
            $res->toArray(null)
        );
    }

    public function test_array_structure_is_resolved_recursively(): void
    {
        $resource = ['id' => 1, 'name' => 'John'];

        $dto = CrudlerResourceGeneratorDTO::start(
            data: [
                'user' => [
                    'id',
                    'name'
                ]
            ],
            additionalData: [],
            methods: []
        );

        $res = new CoreCrudlerResource($resource, $dto);

        $this->assertSame(
            ['user' => ['id' => 1, 'name' => 'John']],
            $res->toArray(null)
        );
    }

    public function test_int_key_is_treated_as_string_field(): void
    {
        $resource = ['email' => 'a@b.c'];

        $dto = CrudlerResourceGeneratorDTO::start(
            data: [
                0 => 'email'
            ],
            additionalData: [],
            methods: []
        );

        $res = new CoreCrudlerResource($resource, $dto);

        $this->assertSame(
            ['email' => 'a@b.c'],
            $res->toArray(null)
        );
    }
}
