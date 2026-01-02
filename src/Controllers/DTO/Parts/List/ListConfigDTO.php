<?php

namespace Crudler\Controllers\DTO\Parts\List;

class ListConfigDTO
{
    /**
     * Summary of __construct
     *
     * @param array<mixed, string> $params
     * @param array<string, string> $mapParams
     */
    public function __construct(
        public readonly array $params = [],
        public readonly array $mapParams = [],
    ) {}

    /**
     * Summary of start
     *
     * @param array<mixed, string> $params
     * @param array<string, string> $mapParams
     *
     * @return ListConfigDTO
     */
    public static function start(
        array $params = [],
        array $mapParams = [],
    ): self {
        return new self(
            params: $params,
            mapParams: $mapParams
        );
    }
}
