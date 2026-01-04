<?php

namespace Crudler\Actions\Runners;

use Crudler\Traits\DBTransaction;

use Core\DTO\ExecutionOptionsDTO;
use Logger\Traits\AsyncLogger;

final class ActionExecutionRunner
{
    use DBTransaction, AsyncLogger;

    public function run(
        callable $action,
        ExecutionOptionsDTO $config,
        ?string $errorMessage,
        ?string $successLog,
        ?string $errorLog
    ) {
        if ($config->getFunc) {
            return $action;
        }

        $success = fn () => $successLog
            ? fn () => $this->successAsyncLog($successLog)
            : null;

        $error = fn () => $errorLog
            ? fn (string $e) => $this->errorAsyncLog($errorLog, $e)
            : null;

        if ($config->withTransaction) {
            return $this->transactionConstructionWithFunc(
                $action,
                $errorMessage,
                $success(),
                $error()
            );
        }

        return $action();
    }
}
