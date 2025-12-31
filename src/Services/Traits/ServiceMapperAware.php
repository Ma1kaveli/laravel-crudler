<?php

namespace Crudler\Services\Traits;

use Crudler\Services\DTO\Parts\ServiceMapperFieldDTO;

trait ServiceMapperAware
{
    protected static function wrapMapper(
        array $raw,
        string $class = ServiceMapperFieldDTO::class
    ): array {
        $result = [];

        foreach ($raw as $key => $value) {
            $result[$key] = new $class($value, $key);
        }

        return $result;
    }
}

