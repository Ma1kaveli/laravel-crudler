<?php

namespace Crudler\Actions\Builders;

use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\Interfaces\IAfterValidation;
use Crudler\Actions\Interfaces\IAfterWithValidation;
use Crudler\Actions\Interfaces\IBeforeValidation;
use Crudler\Actions\Interfaces\IBeforeWithValidation;

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

    public function setBeforeWithValidation(IBeforeWithValidation $beforeWithValidation): self
    {
        $this->beforeWithValidation = $beforeWithValidation;

        return $this;
    }

    public function setBeforeValidation(IBeforeValidation $beforeValidation): self
    {
        $this->beforeValidation = $beforeValidation;

        return $this;
    }

    public function setAfterValidation(IAfterValidation $afterValidation): self
    {
        $this->afterValidation = $afterValidation;

        return $this;
    }

    public function setAfterWithValidation(IAfterWithValidation $afterWithValidation): self
    {
        $this->afterWithValidation = $afterWithValidation;

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
