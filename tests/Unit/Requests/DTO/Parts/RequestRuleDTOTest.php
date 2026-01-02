<?php

namespace Tests\Unit\Requests\DTO\Parts;

use Crudler\Requests\DTO\Parts\RequestRuleDTO;
use InvalidArgumentException;
use Tests\TestCase;

class RequestRuleDTOTest extends TestCase
{
    public function test_initialization_with_string(): void
    {
        $rule = RequestRuleDTO::start('required');
        $this->assertSame('required', $rule->value);
    }

    public function test_initialization_with_object(): void
    {
        $obj = new \stdClass();
        $rule = RequestRuleDTO::start($obj);
        $this->assertSame($obj, $rule->value);
    }

    public function test_initialization_with_closure(): void
    {
        $closure = fn() => true;
        $rule = RequestRuleDTO::start($closure);
        $this->assertInstanceOf(\Closure::class, $rule->value);
    }

    public function test_initialization_with_callable(): void
    {
        $rule = RequestRuleDTO::start(fn() => true);
        $this->assertInstanceOf(\Closure::class, $rule->value);
    }

    public function test_initialization_with_int(): void
    {
        $rule = RequestRuleDTO::start(123);
        $this->assertSame('123', $rule->value);
    }
}
