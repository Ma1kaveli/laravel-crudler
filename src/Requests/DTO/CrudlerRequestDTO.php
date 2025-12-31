<?php

namespace Crudler\Requests\DTO;

use Crudler\Requests\DTO\Parts\RequestTagDTO;

class CrudlerRequestDTO {
    /**
     * @var array<string,RequestTagDTO>
     */
    public readonly ?array $tags;

    public function __construct(array $tags = [])
    {
        $this->tags = $tags;
    }

    public static function start(array $tags): static
    {
        return new self(
            tags: self::wrap($tags, RequestTagDTO::class)
        );
    }

    /**
     * Config to DTO
     *
     * @param array $tags
     * @param string $class
     *
     * @return object[]
     */
    private static function wrap(
        array $tags,
        string $class
    ): array {
        $result = [];

        foreach ($tags as $key => $value) {
            $_class = $value['class'];
            $_authorize = $value['authorize'];
            $_rules = $value['rules'];
            $_createRules = $value['create_rules'];
            $_updateRules = $value['update_rules'];
            $_deleteRules = $value['delete_rules'];
            $_messages = $value['messages'];
            $_updateMessages = $value['update_messages'];
            $_createMessages = $value['create_messages'];
            $_createMessages = $value['delete_messages'];
            $_deleteMessages = $value['delete_messages'];

            if (
                empty($_class)
                && empty($_rules)
                && empty($_createRules)
                && empty($_updateRules)
                && empty($_deleteRules)
            ) {
                throw new \InvalidArgumentException('You have to set one parametr for tag');
            }

            $result[$key] = $class::start(
                $_class,
                $_rules,
                $_createRules,
                $_updateRules,
                $_deleteRules,
                $_messages,
                $_createMessages,
                $_updateMessages,
                $_deleteMessages,
                $_authorize,
            );
        }

        return $result;
    }
}
