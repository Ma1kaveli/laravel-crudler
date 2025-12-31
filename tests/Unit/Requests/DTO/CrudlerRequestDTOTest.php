<?php

namespace Tests\Unit\Requests\DTO;

use Crudler\Requests\DTO\CrudlerRequestDTO;
use Crudler\Requests\DTO\Parts\RequestRuleDTO;
use Crudler\Requests\DTO\Parts\RequestRulesDTO;
use Crudler\Requests\DTO\Parts\RequestTagDTO;
use InvalidArgumentException;
use Tests\TestCase;
use TypeError;

class CrudlerRequestDTOTest extends TestCase
{
    /**
     * Тестируем RequestRuleDTO с разными типами данных
     */
    public function test_request_rule_DTO_initialization(): void
    {
        // строка
        $rule = new RequestRuleDTO('required');
        $this->assertSame('required', $rule->value);

        // объект
        $obj = new \stdClass();
        $rule = new RequestRuleDTO($obj);
        $this->assertSame($obj, $rule->value);

        // callable (Closure)
        $closure = fn() => true;
        $rule = new RequestRuleDTO($closure);
        $this->assertInstanceOf(\Closure::class, $rule->value);

        // callable (не Closure)
        $rule = new RequestRuleDTO(fn() => true);
        $this->assertInstanceOf(\Closure::class, $rule->value);

        // int
        $rule = new RequestRuleDTO(123);
        $this->assertSame('123', $rule->value);
    }

    /**
     * Тестируем RequestRulesDTO
     */
    public function test_request_rules_DTO_initialization(): void
    {
        // одиночная строка
        $rules = RequestRulesDTO::start('required', 'name');
        $this->assertCount(1, $rules->value);
        $this->assertSame('name', $rules->key);
        $this->assertSame('required', $rules->value[0]->value);

        // массив строк
        $rules = RequestRulesDTO::start(['required', 'string'], 'name');
        $this->assertCount(2, $rules->value);
        $this->assertSame('required', $rules->value[0]->value);
        $this->assertSame('string', $rules->value[1]->value);
    }

    /**
     * Тестируем RequestTagDTO
     */
    public function test_request_tag_DTO_initialization(): void
    {
        $rulesArray = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email']
        ];

        $tag = RequestTagDTO::start(
            class: 'TestClass',
            rules: $rulesArray,
            createRules: $rulesArray,
            updateRules: $rulesArray,
            deleteRules: $rulesArray,
            messages: ['name.required' => 'Name is required'],
        );

        $this->assertSame('TestClass', $tag->class);
        $this->assertTrue($tag->authorize);
        $this->assertFalse($tag->isBase);

        // Проверяем, что rules конвертированы в RequestRulesDTO
        $this->assertArrayHasKey('name', $tag->rules);
        $this->assertInstanceOf(RequestRulesDTO::class, $tag->rules['name']);
        $this->assertSame('name', $tag->rules['name']->key);
        $this->assertCount(2, $tag->rules['name']->value);
    }

    /**
     * Неправильный ключ в RequestTagDTO вызывает исключение
     */
    public function test_request_tag_DTO_invalid_key(): void
    {
        $this->expectException(InvalidArgumentException::class);
        RequestTagDTO::start(rules: ['required']);
    }

    /**
     * Тестируем CrudlerRequestDTO
     */
    public function test_crudler_request_DTO(): void
    {
        $tag = RequestTagDTO::start(
            class: 'TestClass',
            rules: [
                'name' => ['required']
            ]
        );

        $crudler = new CrudlerRequestDTO(['user' => $tag]);
        $this->assertArrayHasKey('user', $crudler->tags);
        $this->assertInstanceOf(RequestTagDTO::class, $crudler->tags['user']);
        $this->assertSame('TestClass', $crudler->tags['user']->class);
    }

    /**
     * Проверка исключения, если тег пустой
     */
    public function test_crudler_request_DTO_empty_tag_throws(): void
    {
        $this->expectException(\Error::class);

        $tag = RequestTagDTO::start();
        CrudlerRequestDTO::start(['user' => $tag]);
    }
}
