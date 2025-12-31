<?php

namespace Tests\Unit\Policies\Resolvers;

use Crudler\Policies\Resolvers\CrudlerPolicyResolver;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;
use Crudler\Policies\Interfaces\IRuleCallable;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Tests\TestCase;

class CrudlerPolicyAliasTest extends TestCase
{
    public function test_alias_executes_target_rule(): void
    {
        $policy = new CrudlerPolicyDTO([
            'can_view' => new CrudlerPolicyRuleDTO(
                'can_view',
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        return true;
                    }
                }
            ),
            'can_update' => new CrudlerPolicyRuleDTO(
                'can_update',
                'can_view'
            )
        ]);

        $resolver = new CrudlerPolicyResolver();

        $this->assertTrue(
            $resolver->resolve($policy, 'can_update', $this->mockDto())
        );
    }

    public function test_alias_to_missing_rule_throws(): void
    {
        $this->expectException(Exception::class);

        $policy = new CrudlerPolicyDTO([
            'can_update' => new CrudlerPolicyRuleDTO(
                'can_update',
                'missing_rule'
            )
        ]);

        (new CrudlerPolicyResolver())
            ->resolve($policy, 'can_update', $this->mockDto());
    }

    private function mockDto(): FormDTO
    {
        return $this->createMock(FormDTO::class);
    }
}
