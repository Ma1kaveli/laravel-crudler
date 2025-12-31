<?php

namespace Crudler\Requests\DTO\Parts;

class RequestTagDTO {
    /**
     * @var string|null
     */
    public readonly ?string $class;

    /**
     * @var ?array<string,RequestRulesDTO>
     */
    public readonly ?array $rules;

    /**
     * @var ?array<string,RequestRulesDTO>
     */
    public readonly ?array $updateRules;

    /**
     * @var ?array<string,RequestRulesDTO>
     */
    public readonly ?array $createRules;

    /**
     * @var ?array<string,RequestRulesDTO>
     */
    public readonly ?array $deleteRules;

    /**
     * @var ?array
     */
    public readonly ?array $messages;

    /**
     * @var ?array
     */
    public readonly ?array $updateMessages;

    /**
     * @var ?array
     */
    public readonly ?array $createMessages;

    /**
     * @var ?array
     */
    public readonly ?array $deleteMessages;

    /**
     * @var bool
     */
    public readonly bool $authorize;

    /**
     * @var bool
     */
    public readonly bool $isBase;

    public function __construct(
        ?string $class = null,
        ?array $rules = [],
        ?array $createRules = [],
        ?array $updateRules = [],
        ?array $deleteRules = [],
        ?array $messages = [],
        ?array $updateMessages = [],
        ?array $createMessages = [],
        ?array $deleteMessages = [],
        ?bool $isBase = false,
        ?bool $authorize = true
    ) {
        $this->class = $class;
        $this->rules = $rules;
        $this->createRules = $createRules;
        $this->updateRules = $updateRules;
        $this->deleteRules = $deleteRules;
        $this->messages = $messages;
        $this->createMessages = $createMessages;
        $this->updateMessages = $updateMessages;
        $this->deleteMessages = $deleteMessages;
        $this->isBase = $isBase;
        $this->authorize = $authorize;
    }

    /**
     * Summary of start
     *
     * @param ?string $class = null
     * @param ?array $rules = []
     * @param ?array $createRules = []
     * @param ?array $updateRules = []
     * @param ?array $deleteRules = []
     * @param ?array $messages = []
     * @param ?array $createMessages = []
     * @param ?array $updateMessages = []
     * @param ?array $deleteMessages = []
     * @param ?bool $isBase = false
     * @param ?bool $authorize = true
     *
     * @return static
     */
    public static function start(
        ?string $class = null,
        ?array $rules = [],
        ?array $createRules = [],
        ?array $updateRules = [],
        ?array $deleteRules = [],
        ?array $messages = [],
        ?array $createMessages = [],
        ?array $updateMessages = [],
        ?array $deleteMessages = [],
        ?bool $isBase = false,
        ?bool $authorize = true,
    ): static {
        return new self(
            class: $class,
            isBase: $isBase,
            authorize: $authorize,
            rules: self::wrap($rules, RequestRulesDTO::class),
            createRules: self::wrap($createRules, RequestRulesDTO::class),
            updateRules: self::wrap($updateRules, RequestRulesDTO::class),
            deleteRules: self::wrap($deleteRules, RequestRulesDTO::class),
            messages: $messages,
            updateMessages: $updateMessages,
            createMessages: $createMessages,
            deleteMessages: $deleteMessages,
        );
    }

    /**
     * Config to DTO
     *
     * @param array $raw
     * @param string $class
     *
     * @return object[]
     */
    private static function wrap(
        array $raw,
        string $class
    ): array {
        $result = [];

        foreach ($raw as $key => $value) {
            if (is_int($key)) {
                throw new \InvalidArgumentException('Invalid key for _CrudlerRequestTagDTO value: keys must be field names');
            }

            $result[$key] = $class::start($value, $key);
        }

        return $result;
    }
}
