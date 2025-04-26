<?php

namespace LaravelCrudler\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use LaravelLogger\Facades\LaravelLog;

trait DBTransaction
{
    /**
     * Обвязка вокруг конструкции DB транзакции
     *
     * @param callable $callable
     *
     * @return mixed
     */
    public static function transactionConstruction(callable $callable): mixed
    {
        try {
            DB::beginTransaction();
            $data = $callable();
            DB::commit();
        } catch (Exception $e) {
            LaravelLog::error($e);
            DB::rollBack();
            throw $e;
        }

        return $data;
    }

    /**
     * Еще одна обвязка но уже с Except и возможностью
     * выполнения еще функций при успешном выполнение и не успешным (перед Except)
     *
     * @param callable $callable
     * @param string $errMessage
     * @param ?callable $successFunc = null
     * @param ?callable $errorFunc = null
     * @param int $errCode = 400
     *
     * @return mixed
     */
    public static function transactionConstructionWithFunc(
        callable $callable,
        string $errMessage,
        ?callable $successFunc = null,
        ?callable $errorFunc = null,
        int $errCode = 400,
    ): mixed {
        try {
            $data = self::transactionConstruction($callable);

            if (!empty($successFunc)) {
                $successFunc();
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $message = $e->getMessage();

            if (!empty($errorFunc)) {
                $errorFunc($message);
            }

            throw new Exception(
                (empty($code) || $code === 500) ? $errMessage : $message,
                (empty($code) || $code === 500) ?  $errCode : $code
            );
        }

        return $data;
    }
}
