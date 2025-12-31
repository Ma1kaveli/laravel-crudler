<?php

namespace Tests\Unit\Requests\Core;

use Crudler\Requests\Core\BaseCrudlerFormRequest;
use Core\Requests\Context;
use Tests\TestCase;

/**
 * Мы НЕ тестируем Context enum,
 * только комбинирование правил
 */
class BaseCrudlerFormRequestTest extends TestCase
{
    public function test_rules_for_create(): void
    {
        $request = new BaseCrudlerFormRequest([
            'rules' => ['name' => ['required']],
            'create_rules' => ['age' => ['integer']],
        ]);

        $rules = $this->invoke($request, 'rulesFor', Context::CREATE);

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('age', $rules);
    }

    private function invoke(object $obj, string $method, mixed ...$args)
    {
        $ref = new \ReflectionMethod($obj, $method);
        $ref->setAccessible(true);
        return $ref->invoke($obj, ...$args);
    }
}
