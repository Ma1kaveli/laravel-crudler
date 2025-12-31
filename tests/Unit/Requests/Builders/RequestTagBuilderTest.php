<?php

namespace Tests\Unit\Requests\Builders;

use Crudler\Requests\Builders\RequestTagBuilder;
use Crudler\Requests\DTO\Parts\RequestTagDTO;
use Tests\TestCase;

/**
 * Тестируем только Builder:
 * - установка флагов
 * - генерацию DTO
 * НИКАКОЙ логики CrudlerRequest здесь нет
 */
class RequestTagBuilderTest extends TestCase
{
    public function test_generate_basic_tag(): void
    {
        $dto = RequestTagBuilder::make()
            ->rules(['name' => ['required']])
            ->messages(['name.required' => 'Required'])
            ->generate();

        $this->assertInstanceOf(RequestTagDTO::class, $dto);
        $this->assertArrayHasKey('name', $dto->rules);
        $this->assertTrue($dto->authorize);
        $this->assertFalse($dto->isBase);
    }

    public function test_unauthorize_flag(): void
    {
        $dto = RequestTagBuilder::make()
            ->rules(['id' => ['integer']])
            ->unauthorize()
            ->generate();

        $this->assertFalse($dto->authorize);
    }

    public function test_is_base_flag(): void
    {
        $dto = RequestTagBuilder::make()
            ->rules(['name' => ['string']])
            ->isBase()
            ->generate();

        $this->assertTrue($dto->isBase);
    }

    public function test_existing_class_tag(): void
    {
        $dto = RequestTagBuilder::make()
            ->class(\Illuminate\Foundation\Http\FormRequest::class)
            ->generate();

        $this->assertSame(
            \Illuminate\Foundation\Http\FormRequest::class,
            $dto->class
        );
    }
}
