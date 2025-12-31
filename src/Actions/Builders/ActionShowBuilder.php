<?php

namespace Crudler\Actions\Builders;

use Core\DTO\OnceDTO;
use Crudler\Actions\DTO\Parts\ActionShowDTO;
use Crudler\Actions\Interfaces\{
    IShowAfterAction,
    IShowReturnAction
};

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

    public function after(IShowAfterAction $after): self
    {
        $this->after = $after;

        return $this;
    }

    public function return(IShowReturnAction $return): self
    {
        $this->return = $return;

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
