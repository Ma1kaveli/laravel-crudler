<?php

namespace Crudler\Policies\Builders;

use Crudler\Policies\DTO\CrudlerPolicyDTO;
use Crudler\Policies\DTO\CrudlerPolicyRuleDTO;
use Crudler\Policies\Interfaces\IRuleCallable;

use Exception;
use Core\DTO\FormDTO;
use Crudler\Adapters\ClosureHookAdapter;
use Illuminate\Database\Eloquent\Model;

class PolicyBuilder
{
    private array $rules = [];

    public static function make(): self
    {
        return new self();
    }

    /**
     * Summary of normalizeRule
     *
     * @param IRuleCallable|string|callable(FormDTO $dto, ?Model $data): Exception|true $rule
     *
     * @return IRuleCallable|object|string
     */
    private function normalizeRule(IRuleCallable|string|callable $rule): IRuleCallable|string
    {
        if (is_string($rule) || $rule instanceof IRuleCallable) return $rule;

        return new class($rule) extends ClosureHookAdapter implements IRuleCallable {
            public function __invoke(FormDTO $dto, ?Model $data): Exception|true {
                return ($this->closure)($dto, $data);
            }
        };
    }

    /**
     * Summary of canView
     *
     * @param IRuleCallable|string|callable(FormDTO $dto, ?Model $data): Exception|true $rule
     *
     * @return PolicyBuilder
     */
    public function canView(IRuleCallable|string|callable $rule): self
    {
        return $this->add('can_view', $this->normalizeRule($rule));
    }

    /**
     * Summary of canUpdate
     *
     * @param IRuleCallable|string|callable(FormDTO $dto, ?Model $data): Exception|true $rule
     *
     * @return PolicyBuilder
     */
    public function canUpdate(IRuleCallable|string|callable $rule): self
    {
        return $this->add('can_update', $this->normalizeRule($rule));
    }

    /**
     * Summary of canDelete
     *
     * @param IRuleCallable|string|callable(FormDTO $dto, ?Model $data): Exception|true $rule
     *
     * @return PolicyBuilder
     */
    public function canDelete(IRuleCallable|string|callable $rule): self
    {
        return $this->add('can_delete', $this->normalizeRule($rule));
    }

    /**
     * Summary of canRestore
     *
     * @param IRuleCallable|string|callable(FormDTO $dto, ?Model $data): Exception|true $rule
     *
     * @return PolicyBuilder
     */
    public function canRestore(IRuleCallable|string|callable $rule): self
    {
        return $this->add('can_restore', $this->normalizeRule($rule));
    }

    /**
     * Summary of add
     *
     * @param string $ability
     * @param IRuleCallable|string|callable(FormDTO $dto, ?Model $data): Exception|true $rule
     *
     * @return PolicyBuilder
     */
    protected function add(string $ability, IRuleCallable|string|callable $rule): self
    {
        $this->rules[$ability] = new CrudlerPolicyRuleDTO(
            $ability,
            $this->normalizeRule($rule)
        );

        return $this;
    }

    public function build(): CrudlerPolicyDTO
    {
        return new CrudlerPolicyDTO($this->rules);
    }
}

