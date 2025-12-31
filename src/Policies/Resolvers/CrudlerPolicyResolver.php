<?php

namespace Crudler\Policies\Resolvers;

use Core\DTO\FormDTO;
use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;
use Illuminate\Database\Eloquent\Model;
use Exception;

class CrudlerPolicyResolver
{
    public function resolve(
        CrudlerPolicyDTO $policy,
        string $ability,
        FormDTO $dto,
        ?Model $data = null
    ): bool {
        $rule = $policy->get($ability);

        if (!$rule) {
            return true;
        }

        return $this->execute($policy, $rule, $dto, $data);
    }

    protected function execute(
        CrudlerPolicyDTO $policy,
        CrudlerPolicyRuleDTO $rule,
        FormDTO $dto,
        ?Model $data
    ): bool {
        if ($rule->isCallable()) {
            return ($rule->handler)($dto, $data);
        }

        if ($rule->isAlias()) {
            $alias = $policy->get($rule->handler);

            if (!$alias) {
                throw new Exception("Policy alias '{$rule->handler}' not found");
            }

            return $this->execute($policy, $alias, $dto, $data);
        }

        throw new Exception('Invalid policy rule');
    }
}

