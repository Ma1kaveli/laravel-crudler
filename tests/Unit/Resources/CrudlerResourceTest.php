<?php

namespace Tests\Unit\Resources;

use Crudler\Resources\CrudlerResource;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Tests\TestCase;

class CrudlerResourceTest extends TestCase
{
    public function test_only_selected_additional_fields_are_returned(): void
    {
        $dto = CrudlerResourceGeneratorDTO::start(
            data: [],
            additionalData: [
                'a' => fn () => 1,
                'b' => fn () => 2,
            ],
            methods: []
        );

        $crudler = new CrudlerResource($dto);

        $res = $crudler->resource([], ['b']);

        $this->assertSame(
            ['b' => 2],
            $res->toArray(null)
        );
    }
}
