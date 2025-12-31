<?php

namespace Tests\Unit\Policies\Resolvers;

use Crudler\Policies\Resolvers\CrudlerPolicyResolver;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\Interfaces\IRuleCallable;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Tests\TestCase;

class CrudlerPolicyResolverTest extends TestCase
{
    public function test_undefined_ability_returns_true(): void
    {
        $resolver = new CrudlerPolicyResolver();
        $policy = new CrudlerPolicyDTO([]);

        $this->assertTrue(
            $resolver->resolve($policy, 'can_view', $this->mockDto())
        );
    }

    public function test_callable_rule_returns_true(): void
    {
        $policy = new CrudlerPolicyDTO([
            'can_view' => new \Crudler\Policies\DTO\CrudlerPolicyRuleDTO(
                'can_view',
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        return true;
                    }
                }
            )
        ]);

        $resolver = new CrudlerPolicyResolver();

        $this->assertTrue(
            $resolver->resolve($policy, 'can_view', $this->mockDto())
        );
    }

    public function test_callable_rule_throws_exception(): void
    {
        $this->expectException(Exception::class);

        $policy = new CrudlerPolicyDTO([
            'can_view' => new \Crudler\Policies\DTO\CrudlerPolicyRuleDTO(
                'can_view',
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        throw new Exception('Forbidden');
                    }
                }
            )
        ]);

        (new CrudlerPolicyResolver())
            ->resolve($policy, 'can_view', $this->mockDto());
    }

    private function mockDto(): FormDTO
    {
        return $this->createMock(FormDTO::class);
    }
}
