<?php

namespace Crudler\Requests\DTO\Parts;

class RequestRulesDTO
{
    /**
     * @var array<RequestRuleDTO>
     */
    public readonly array $value;

    /**
     * @var string
     */
    public readonly string $key;

    public function __construct(array $value, string $key)
    {
        $this->value = $value;
        $this->key = $key;
    }

    /**
     * Summary of start
     *
     * @param string|array $value
     * @param string $key
     *
     * @return static
     */
    public static function start(
        string|array $value,
        string $key,
    ): static {
        return new self(
            value: self::wrap($value, RequestRuleDTO::class),
            key: $key,
        );
    }

    /**
     * Config to DTO
     *
     * @param array|string $raw
     * @param string $class
     *
     * @return array<RequestRuleDTO>
     */
    private static function wrap(
        array|string $raw,
        string $class
    ): array {
        $result = [];

        if (!is_array($raw)) {
            $raw = [$raw];
        }

        foreach ($raw as $value) {
            $result[] = new $class($value);
        }

        return $result;
    }
}
