<?php

namespace Crudler\Policies\Builders;

use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;
use Crudler\Policies\Interfaces\IRuleCallable;

class PolicyBuilder
{
    private array $rules = [];

    public static function make(): self
    {
        return new self();
    }

    public function canView(IRuleCallable|string $rule): self
    {
        return $this->add('can_view', $rule);
    }

    public function canUpdate(IRuleCallable|string $rule): self
    {
        return $this->add('can_update', $rule);
    }

    public function canDelete(IRuleCallable|string $rule): self
    {
        return $this->add('can_delete', $rule);
    }

    public function canRestore(IRuleCallable|string $rule): self
    {
        return $this->add('can_restore', $rule);
    }

    protected function add(string $ability, IRuleCallable|string $rule): self
    {
        $this->rules[$ability] = new CrudlerPolicyRuleDTO(
            $ability,
            $rule
        );

        return $this;
    }

    public function build(): CrudlerPolicyDTO
    {
        return new CrudlerPolicyDTO($this->rules);
    }
}

