<?php

namespace Tests\Unit\Policies\DTO;

use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;
use Crudler\Policies\Interfaces\IRuleCallable;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Tests\TestCase;

class CrudlerPolicyRuleDTOTest extends TestCase
{
    public function test_callable_rule(): void
    {
        $rule = new CrudlerPolicyRuleDTO(
            'can_view',
            new class implements IRuleCallable {
                public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                    return true;
                }
            }
        );

        $this->assertTrue($rule->isCallable());
        $this->assertFalse($rule->isAlias());
    }

    public function test_alias_rule(): void
    {
        $rule = new CrudlerPolicyRuleDTO(
            'can_update',
            'can_view'
        );

        $this->assertTrue($rule->isAlias());
        $this->assertFalse($rule->isCallable());
    }
}
