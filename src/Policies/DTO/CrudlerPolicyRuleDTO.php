<?php

namespace Crudler\Policies\DTO;

use Crudler\Policies\Interfaces\IRuleCallable;

class CrudlerPolicyRuleDTO
{
    public function __construct(
        public readonly string $name,
        public readonly IRuleCallable|string $handler
    ) {}

    public function isAlias(): bool
    {
        return is_string($this->handler);
    }

    public function isCallable(): bool
    {
        return $this->handler instanceof IRuleCallable;
    }
}
