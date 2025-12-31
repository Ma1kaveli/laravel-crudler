<?php

namespace Crudler\Actions\Context;

use Crudler\Actions\Hooks\BeforeAction\AfterValidationResult;
use Crudler\Actions\Hooks\BeforeAction\AfterWithValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeValidationResult;
use Crudler\Actions\Hooks\BeforeAction\BeforeWithValidationResult;
use Crudler\Actions\Hooks\InAction\AfterActionResult;
use Crudler\Actions\Hooks\InAction\BeforeActionResult;
use Crudler\Actions\Hooks\InAction\ReturnResult;

final class ActionContext
{
    public ?BeforeWithValidationResult $beforeWithValidation = null;
    public ?BeforeValidationResult $beforeValidation = null;
    public ?AfterValidationResult $afterValidation = null;
    public ?AfterWithValidationResult $afterWithValidation = null;

    public ?BeforeActionResult $beforeAction = null;
    public ?AfterActionResult $afterAction = null;

    public ?ReturnResult $returnResult = null;

    public function ensureBeforeWithValidation($formDTO): void
    {
        if ($this->beforeWithValidation === null) {
            $this->beforeWithValidation = BeforeWithValidationResult::create($formDTO);
        }
    }
}
