<?php

namespace Tests\Unit\Requests\DTO\Parts;

use Crudler\Requests\DTO\Parts\RequestRulesDTO;
use Crudler\Requests\DTO\Parts\RequestRuleDTO;
use Tests\TestCase;

class RequestRulesDTOTest extends TestCase
{
    public function test_start_with_string(): void
    {
        $rules = RequestRulesDTO::start('required', 'name');
        $this->assertSame('name', $rules->key);
        $this->assertCount(1, $rules->value);
        $this->assertInstanceOf(RequestRuleDTO::class, $rules->value[0]);
        $this->assertSame('required', $rules->value[0]->value);
    }

    public function test_start_with_array(): void
    {
        $rules = RequestRulesDTO::start(['required', 'string'], 'name');
        $this->assertCount(2, $rules->value);
        $this->assertSame('required', $rules->value[0]->value);
        $this->assertSame('string', $rules->value[1]->value);
    }
}
