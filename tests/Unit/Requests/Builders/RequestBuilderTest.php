<?php

namespace Tests\Unit\Requests\Builders;

use Crudler\Requests\Builders\RequestBuilder;
use Crudler\Requests\DTO\CrudlerRequestDTO;
use Tests\TestCase;

/**
 * RequestBuilder — это фабрика DTO
 * Проверяем:
 * - addCreateTag / addUpdateTag
 * - fromConfig
 * - build()
 */
class RequestBuilderTest extends TestCase
{
    public function test_build_with_create_tag(): void
    {
        $dto = RequestBuilder::make()
            ->addCreateTag(
                'create',
                ['name' => ['required']],
                ['name.required' => 'Required']
            )
            ->build();

        $this->assertInstanceOf(CrudlerRequestDTO::class, $dto);
        $this->assertArrayHasKey('create', $dto->tags);
    }

    public function test_add_existing_tag(): void
    {
        $dto = RequestBuilder::make()
            ->addExistingTag('existing', \Illuminate\Foundation\Http\FormRequest::class)
            ->build();

        $this->assertSame(
            \Illuminate\Foundation\Http\FormRequest::class,
            $dto->tags['existing']->class
        );
    }

    public function test_from_config(): void
    {
        $dto = RequestBuilder::make()
            ->fromConfig([
                'base' => [
                    'rules' => ['id' => ['required']],
                    'is_base' => true,
                    'authorize' => false,
                ],
            ])
            ->build();

        $tag = $dto->tags['base'];

        $this->assertTrue($tag->isBase);
        $this->assertFalse($tag->authorize);
        $this->assertArrayHasKey('id', $tag->rules);
    }
}
