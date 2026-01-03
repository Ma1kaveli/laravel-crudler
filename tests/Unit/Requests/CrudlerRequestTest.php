<?php

namespace Tests\Unit\Requests;

use Crudler\Requests\CrudlerRequest;
use Crudler\Requests\Builders\RequestBuilder;
use Crudler\Requests\Core\BaseCrudlerFormRequest;
use Crudler\Requests\Core\SimpleCrudlerFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Здесь проверяется ПОВЕДЕНИЕ системы
 */
class CrudlerRequestTest extends TestCase
{
    public function test_make_simple_request(): void
    {
        $dto = RequestBuilder::make()
            ->addCreateTag(
                'simple',
                ['name' => ['required']],
                [],
                [],
                [],
                true
            )
            ->build();

        $crudler = new CrudlerRequest($dto);

        // Создаём запрос с валидными данными (для прохождения validateResolved())
        $rawRequest = Request::create('/', 'POST', ['name' => 'John']);

        $request = $crudler->make('simple', $rawRequest);

        $this->assertInstanceOf(SimpleCrudlerFormRequest::class, $request);
    }

    public function test_make_contextual_request(): void
    {
        $dto = RequestBuilder::make()
            ->addRequestTag(
                'full',
                ['name' => ['required']],
                [],
                ['age' => ['integer']]
            )
            ->build();

        $crudler = new CrudlerRequest($dto);

        // Создаём запрос с валидными данными (метод POST для CREATE контекста)
        $rawRequest = Request::create('/', 'POST', ['name' => 'John', 'age' => 30]);

        $request = $crudler->make('full', $rawRequest);

        $this->assertInstanceOf(BaseCrudlerFormRequest::class, $request);
    }

    public function test_make_existing_request(): void
    {
        $dto = RequestBuilder::make()
            ->addExistingTag('existing', FormRequest::class)
            ->build();

        $crudler = new CrudlerRequest($dto);

        // Создаём пустой запрос (поскольку нет правил, validateResolved() пройдёт)
        $rawRequest = Request::create('/', 'GET');

        $request = $crudler->make('existing', $rawRequest);

        $this->assertInstanceOf(FormRequest::class, $request);
    }

    public function test_unknown_tag_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $crudler = new CrudlerRequest(
            RequestBuilder::make()->build()
        );

        // Передаём пустой запрос, чтобы соответствовать сигнатуре
        $rawRequest = Request::create('/', 'GET');

        $crudler->make('unknown', $rawRequest);
    }

    public function test_raw_request_injection(): void
    {
        $dto = RequestBuilder::make()
            ->addCreateTag('tag', ['name' => ['required']])
            ->build();

        $crudler = new CrudlerRequest($dto);

        $rawRequest = Request::create('/', 'POST', ['name' => 'John']);

        $request = $crudler->make('tag', $rawRequest);

        $this->assertSame('John', $request->input('name'));
    }

    public function test_validation_success_with_valid_data(): void
    {
        $dto = RequestBuilder::make()
            ->addCreateTag(
                'validation_test',
                [
                    'name' => ['required', 'string'],
                    'age' => ['required', 'integer', 'min:18'],
                ]
            )
            ->build();

        $crudler = new CrudlerRequest($dto);

        // Валидные данные
        $rawRequest = Request::create('/', 'POST', ['name' => 'John Doe', 'age' => 25]);

        $request = $crudler->make('validation_test', $rawRequest);

        // Проверяем, что валидация прошла и данные доступны
        $this->assertInstanceOf(BaseCrudlerFormRequest::class, $request);
        $this->assertEquals(['name' => 'John Doe', 'age' => 25], $request->validated());
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $dto = RequestBuilder::make()
            ->addCreateTag(
                'validation_test',
                [
                    'name' => ['required', 'string'],
                    'age' => ['required', 'integer', 'min:18'],
                ]
            )
            ->build();

        $crudler = new CrudlerRequest($dto);

        // Невалидные данные (отсутствует name, age меньше 18)
        $rawRequest = Request::create('/', 'POST', ['age' => 15]);

        $this->expectException(ValidationException::class);

        $crudler->make('validation_test', $rawRequest);
    }
}
