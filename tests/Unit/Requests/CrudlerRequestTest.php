<?php

namespace Tests\Unit\Requests;

use Crudler\Requests\CrudlerRequest;
use Crudler\Requests\Builders\RequestBuilder;
use Crudler\Requests\Core\BaseCrudlerFormRequest;
use Crudler\Requests\Core\SimpleCrudlerFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
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

        $request = $crudler->make('simple');

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

        $request = $crudler->make('full');

        $this->assertInstanceOf(BaseCrudlerFormRequest::class, $request);
    }

    public function test_make_existing_request(): void
    {
        $dto = RequestBuilder::make()
            ->addExistingTag('existing', FormRequest::class)
            ->build();

        $crudler = new CrudlerRequest($dto);

        $request = $crudler->make('existing');

        $this->assertInstanceOf(FormRequest::class, $request);
    }

    public function test_unknown_tag_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $crudler = new CrudlerRequest(
            RequestBuilder::make()->build()
        );

        $crudler->make('unknown');
    }

    public function test_raw_request_injection(): void
    {
        $dto = RequestBuilder::make()
            ->addCreateTag('tag', ['name' => ['required']])
            ->build();

        $crudler = new CrudlerRequest($dto);

        $raw = Request::create('/', 'POST', ['name' => 'John']);

        $request = $crudler->make('tag', $raw);

        $this->assertSame('John', $request->input('name'));
    }
}
