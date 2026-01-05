<?php

namespace Crudler\Actions\Builders;

use Crudler\Adapters\ClosureHookAdapter;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\Interfaces\{
    IShowAfterAction,
    IShowReturnAction
};

use Core\DTO\OnceDTO;
use Illuminate\Database\Eloquent\Model;

class ActionShowBuilder
{
    public ?OnceDTO $dto = null;

    public ?IShowAfterAction $after = null;

    public ?IShowReturnAction $return = null;


    public static function make(OnceDTO $dto): self
    {
        $builder = new self();
        $builder->dto = $dto;

        return $builder;
    }

    /**
     * Summary of after
     *
     * @param IShowAfterAction|callable(OnceDTO $dto, Model $data): mixed $after
     *
     * @return ActionShowBuilder
     */
    public function after(IShowAfterAction|callable $after): self
    {
        $this->after = $after instanceof IShowAfterAction
            ? $after
            : new class($after) extends ClosureHookAdapter implements IShowAfterAction {
                public function __invoke(OnceDTO $dto, Model $data): mixed {
                    return ($this->closure)($dto, $data);
                }
            };

        return $this;
    }

    /**
     * Summary of return
     *
     * @param IShowReturnAction|callable(OnceDTO $dto, Model $data, mixed $afterResult = null): mixed $return
     *
     * @return ActionShowBuilder
     */
    public function return(IShowReturnAction|callable $return): self
    {
        $this->return = $return instanceof IShowReturnAction
            ? $return
            : new class($return) extends ClosureHookAdapter implements IShowReturnAction {
                public function __invoke(OnceDTO $dto, Model $data, mixed $afterResult = null): mixed {
                    return ($this->closure)($dto, $data, $afterResult);
                }
            };

        return $this;
    }

    public function fromConfig(array $config): self
    {
        if (isset($config['after'])) {
            $this->after($config['after']);
        }

        if (isset($config['return'])) {
            $this->return($config['return']);
        }

        return $this;
    }

    public function build(): ActionShowDTO
    {
        return ActionShowDTO::start(
            onceDTO: $this->dto,
            after: $this->after,
            return: $this->return
        );
    }
}
