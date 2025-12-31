<?php

namespace Tests\Unit\Resources\Core;

use Crudler\Resources\Core\CoreCrudlerResource;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Tests\TestCase;

class CoreCrudlerResourceAdditionalTest extends TestCase
{
    public function test_additional_field_is_lazy(): void
    {
        $called = false;

        $resource = ['x' => 5];

        $dto = CrudlerResourceGeneratorDTO::start(
            data: [],
            additionalData: [
                'lazy' => function () use (&$called) {
                    $called = true;
                    return 123;
                }
            ],
            methods: []
        );

        $res = new CoreCrudlerResource($resource, $dto);

        // ещё не вычислялось
        $this->assertFalse($called);

        $out = $res->toArray(null);

        $this->assertTrue($called);
        $this->assertSame(['lazy' => 123], $out);
    }

    public function test_additional_array_is_merged(): void
    {
        $dto = CrudlerResourceGeneratorDTO::start(
            data: [],
            additionalData: [
                'meta' => fn () => ['a' => 1, 'b' => 2]
            ],
            methods: []
        );

        $res = new CoreCrudlerResource([], $dto);

        $this->assertSame(
            ['a' => 1, 'b' => 2],
            $res->toArray(null)
        );
    }
}
