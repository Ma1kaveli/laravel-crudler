<?php

namespace Tests\Unit\Requests\DTO\Parts;

use Crudler\Requests\DTO\CrudlerRequestDTO;
use Crudler\Requests\DTO\Parts\RequestTagDTO;
use InvalidArgumentException;
use Tests\TestCase;

class CrudlerRequestDTOTest extends TestCase
{
    public function test_start_with_valid_tags(): void
    {
        $tag = RequestTagDTO::start(
            class: 'TestClass',
            rules: [
                'name' => ['required']
            ]
        );

        $dto = new CrudlerRequestDTO(['user' => $tag]);
        $this->assertArrayHasKey('user', $dto->tags);
        $this->assertSame('TestClass', $dto->tags['user']->class);
    }

    public function test_start_with_empty_tag_throws(): void
    {
        $this->expectException(\Error::class);

        $tag = RequestTagDTO::start();
        CrudlerRequestDTO::start(['test' => $tag]);
    }
}
