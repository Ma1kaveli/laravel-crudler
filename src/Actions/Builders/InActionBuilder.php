<?php

namespace Crudler\Actions\Builders;

use Crudler\Adapters\ClosureHookAdapter;
use Crudler\Actions\DTO\Parts\InAction\InActionDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\InAction\AfterActionResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;
use Crudler\Actions\Hooks\InAction\ReturnResult;
use Crudler\Actions\Interfaces\IAfterAction;
use Crudler\Actions\Interfaces\IBeforeAction;
use Crudler\Actions\Interfaces\IReturn;

use Illuminate\Database\Eloquent\Model;

class InActionBuilder
{
    public ?IBeforeAction $beforeAction = null;

    public ?IAfterAction $afterAction = null;

    public ?IReturn $return = null;

    public static function make(): self
    {
        return new self();
    }

    /**
     * Summary of setBeforeAction
     *
     * @param IBeforeAction|callable(AfterWithValidationResult $result, Model|array|null $data = null): BeforeActionResult $beforeAction
     *
     * @return InActionBuilder
     */
    public function setBeforeAction(IBeforeAction|callable $beforeAction): self
    {
        $this->beforeAction = $beforeAction instanceof IBeforeAction
            ? $beforeAction
            : new class($beforeAction) extends ClosureHookAdapter implements IBeforeAction {
                public function __invoke(
                    AfterWithValidationResult $result,
                    Model|array|null $data = null
                ): BeforeActionResult {
                    return ($this->closure)($result, $data);
                }
            };

        return $this;
    }

    /**
     * Summary of setAfterAction
     *
     * @param IAfterAction|callable(BeforeActionResult $result, Model|array $data): AfterActionResult $afterAction
     *
     * @return InActionBuilder
     */
    public function setAfterAction(IAfterAction|callable $afterAction): self
    {
        $this->afterAction = $afterAction instanceof IAfterAction
            ? $afterAction
            : new class($afterAction) extends ClosureHookAdapter implements IAfterAction {
                public function __invoke(
                    BeforeActionResult $result,
                    Model|array|null $data = null
                ): AfterActionResult {
                    return ($this->closure)($result, $data);
                }
            };

        return $this;
    }

    /**
     * Summary of setReturn
     *
     * @param IReturn|callable(AfterActionResult $result): ReturnResult $return
     *
     * @return InActionBuilder
     */
    public function setReturn(IReturn|callable $return): self
    {
        $this->return = $return instanceof IReturn
            ? $return
            : new class($return) extends ClosureHookAdapter implements IReturn {
                public function __invoke(AfterActionResult $result): ReturnResult {
                    return ($this->closure)($result);
                }
            };

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
