<?php

namespace Tests\Unit\Policies\Builders;

use Crudler\Policies\Builders\PolicyBuilder;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\Interfaces\IRuleCallable;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Tests\TestCase;

class PolicyBuilderTest extends TestCase
{
    public function test_builder_creates_policy_dto(): void
    {
        $policy = PolicyBuilder::make()
            ->canView(
                new class implements IRuleCallable {
                    public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                        return true;
                    }
                }
            )
            ->canUpdate('can_view')
            ->build();

        $this->assertInstanceOf(CrudlerPolicyDTO::class, $policy);
        $this->assertNotNull($policy->get('can_view'));
        $this->assertNotNull($policy->get('can_update'));
    }
}
