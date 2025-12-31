<?php

namespace Tests\Unit\Requests\DTO\Parts;

use Crudler\Requests\DTO\Parts\RequestTagDTO;
use Crudler\Requests\DTO\Parts\RequestRulesDTO;
use InvalidArgumentException;
use Tests\TestCase;

class RequestTagDTOTest extends TestCase
{
    public function test_start_with_valid_rules(): void
    {
        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email']
        ];

        $tag = RequestTagDTO::start(
            class: 'TestClass',
            rules: $rules,
            createRules: $rules,
            updateRules: $rules,
            deleteRules: $rules
        );

        $this->assertSame('TestClass', $tag->class);
        $this->assertInstanceOf(RequestRulesDTO::class, $tag->rules['name']);
        $this->assertSame('name', $tag->rules['name']->key);
    }

    public function test_start_with_invalid_key_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        RequestTagDTO::start(rules: ['required']);
    }
}
