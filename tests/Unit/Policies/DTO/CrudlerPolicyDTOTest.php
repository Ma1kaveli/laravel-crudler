<?php

namespace Tests\Unit\Policies\DTO;

use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;
use Tests\TestCase;

class CrudlerPolicyDTOTest extends TestCase
{
    public function test_from_array_creates_rules(): void
    {
        $dto = CrudlerPolicyDTO::fromArray([
            'can_view' => 'can_update',
        ]);

        $this->assertInstanceOf(CrudlerPolicyRuleDTO::class, $dto->get('can_view'));
        $this->assertNull($dto->get('missing'));
    }
}
