<?php

namespace Crudler\Actions\Builders;

use Crudler\Actions\DTO\Parts\InAction\InActionDTO;
use Crudler\Actions\Interfaces\IAfterAction;
use Crudler\Actions\Interfaces\IBeforeAction;
use Crudler\Actions\Interfaces\IReturn;

class InActionBuilder
{
    public ?IBeforeAction $beforeAction = null;

    public ?IAfterAction $afterAction = null;

    public ?IReturn $return = null;

    public static function make(): self
    {
        return new self();
    }

    public function setBeforeAction(IBeforeAction $beforeAction): self
    {
        $this->beforeAction = $beforeAction;

        return $this;
    }

    public function setAfterAction(IAfterAction $afterAction): self
    {
        $this->afterAction = $afterAction;

        return $this;
    }

    public function setReturn(IReturn $return): self
    {
        $this->return = $return;

        return $this;
    }

    public function fromConfig(array $config): self
    {
        if (isset($config['before_action'])) {
            $this->setBeforeAction($config['before_action']);
        }

        if (isset($config['after_action'])) {
            $this->setAfterAction($config['after_action']);
        }

        if (isset($config['return'])) {
            $this->setReturn($config['return']);
        }

        return $this;
    }

    public function build(): InActionDTO
    {
        return new InActionDTO(
            $this->beforeAction,
            $this->afterAction,
            $this->return
        );
    }
}
