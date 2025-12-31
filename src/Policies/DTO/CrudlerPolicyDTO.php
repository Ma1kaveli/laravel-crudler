<?php

namespace Crudler\Policies\DTO;

use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;

class CrudlerPolicyDTO
{
    /**
     * @var array<string, CrudlerPolicyRuleDTO>
     * */
    public readonly array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public static function fromArray(array $raw): self
    {
        $rules = [];

        foreach ($raw as $name => $rule) {
            $rules[$name] = new CrudlerPolicyRuleDTO(
                name: $name,
                handler: $rule
            );
        }

        return new self($rules);
    }

    public function get(string $name): ?CrudlerPolicyRuleDTO
    {
        return $this->rules[$name] ?? null;
    }
}
