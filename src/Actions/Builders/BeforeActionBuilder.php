<?php

namespace Crudler\Actions\Builders;

use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Interfaces\IAfterValidation;
use Crudler\Actions\Interfaces\IAfterWithValidation;
use Crudler\Actions\Interfaces\IBeforeValidation;
use Crudler\Actions\Interfaces\IBeforeWithValidation;

use Core\DTO\FormDTO;
use Crudler\Adapters\ClosureHookAdapter;
use Illuminate\Database\Eloquent\Model;

class BeforeActionBuilder
{
    public ?IBeforeWithValidation $beforeWithValidation = null;

    public ?IBeforeValidation $beforeValidation = null;

    public ?IAfterValidation $afterValidation = null;

    public ?IAfterWithValidation $afterWithValidation = null;

    public static function make(): self
    {
        return new self();
    }

    /**
     * Summary of setBeforeWithValidation
     *
     * @param IBeforeWithValidation|callable(FormDTO $dto, Model|array|null $data = null): BeforeWithValidationResult $beforeWithValidation
     *
     * @return BeforeActionBuilder
     */
    public function setBeforeWithValidation(IBeforeWithValidation|callable $beforeWithValidation): self
    {
        $this->beforeWithValidation = $beforeWithValidation instanceof IBeforeWithValidation
            ? $beforeWithValidation
            : new class($beforeWithValidation) extends ClosureHookAdapter implements IBeforeWithValidation {
                public function __invoke(FormDTO $dto, Model|array|null $data = null): BeforeWithValidationResult {
                    return ($this->closure)($dto, $data);
                }
            };

        return $this;
    }

    /**
     * Summary of setBeforeValidation
     *
     * @param IBeforeValidation|callable(BeforeWithValidationResult $result, Model|array|null $data = null): BeforeValidationResult $beforeValidation
     *
     * @return BeforeActionBuilder
     */
    public function setBeforeValidation(IBeforeValidation|callable $beforeValidation): self
    {
        $this->beforeValidation = $beforeValidation instanceof IBeforeValidation
            ? $beforeValidation
            : new class($beforeValidation) extends ClosureHookAdapter implements IBeforeValidation {

                public function __invoke(
                    BeforeWithValidationResult $result,
                    Model|array|null $data = null
                ): BeforeValidationResult {
                    return ($this->closure)($result, $data);
                }
            };

        return $this;
    }

    /**
     * Summary of setAfterValidation
     *
     * @param IAfterValidation|callable(BeforeValidationResult $result, Model|array|null $data = null): AfterValidationResult $afterValidation
     *
     * @return BeforeActionBuilder
     */
    public function setAfterValidation(IAfterValidation|callable $afterValidation): self
    {
        $this->afterValidation = $afterValidation instanceof IAfterValidation
            ? $afterValidation
            : new class($afterValidation) extends ClosureHookAdapter implements IAfterValidation {
                public function __invoke(
                    BeforeValidationResult $result,
                    Model|array|null $data = null
                ): AfterValidationResult {
                    return ($this->closure)($result, $data);
                }
            };


        return $this;
    }

    /**
     * Summary of setAfterWithValidation
     *
     * @param IAfterWithValidation|callable(BeforeWithValidationResult $beforeWithValidationResult, ?AfterValidationResult $afterValidationResult = null, Model|array|null $data = null): AfterWithValidationResult $afterWithValidation
     *
     * @return BeforeActionBuilder
     */
    public function setAfterWithValidation(IAfterWithValidation|callable $afterWithValidation): self
    {
        $this->afterWithValidation = $afterWithValidation instanceof IAfterWithValidation
            ? $afterWithValidation
            : new class($afterWithValidation) extends ClosureHookAdapter implements IAfterWithValidation {
                public function __invoke(
                    BeforeWithValidationResult $beforeWithValidationResult,
                    ?AfterValidationResult $afterValidationResult = null,
                    Model|array|null $data = null
                ): AfterWithValidationResult {
                    return ($this->closure)($beforeWithValidationResult, $afterValidationResult, $data);
                }
            };

        return $this;
    }

    public function fromConfig(array $config): self
    {
        if (isset($config['before_with_validation'])) {
            $this->setBeforeWithValidation($config['before_with_validation']);
        }

        if (isset($config['before_validation'])) {
            $this->setBeforeValidation($config['before_validation']);
        }

        if (isset($config['after_validation'])) {
            $this->setAfterValidation($config['after_validation']);
        }

        if (isset($config['after_with_validation'])) {
            $this->setAfterWithValidation($config['after_with_validation']);
        }

        return $this;
    }

    public function build(): BeforeActionDTO
    {
        return new BeforeActionDTO(
            $this->beforeWithValidation,
            $this->beforeValidation,
            $this->afterValidation,
            $this->afterWithValidation
        );
    }
}
