<?php

namespace Crudler\Requests\Builders;

use Crudler\Requests\DTO\CrudlerRequestDTO;
use Crudler\Requests\DTO\Parts\RequestTagDTO;
use Crudler\Requests\Builders\RequestTagBuilder;

class RequestBuilder
{
    /**
     * @var array<string,RequestTagBuilder>
     */
    private array $tags = [];

    public static function make(): self
    {
        return new self();
    }

    /**
     * Добавляет тег по имени с готовым билдером.
     */
    public function addTag(string $name, RequestTagBuilder $tagBuilder): self
    {
        $this->tags[$name] = $tagBuilder;

        return $this;
    }

    /**
     * Добавляет существующий request
     */
    public function addExistingTag(string $name, string $class): self
    {
        $tagBuilder = RequestTagBuilder::make()->class($class);

        return $this->addTag($name, $tagBuilder);
    }

    /**
     * Добавляет тег для создания.
     */
    public function addCreateTag(
        string $name,
        array $rules,
        array $messages = [],
        array $createRules = [],
        array $createMessages = [],
        bool $isBase = false,
        bool $authorize = true
    ): self {
        $tagBuilder = RequestTagBuilder::make()
            ->rules($rules)
            ->messages($messages)
            ->createRules($createRules)
            ->createMessages($createMessages);

        if (!$authorize) {
            $tagBuilder->unauthorize();
        }

        if ($isBase) {
            $tagBuilder->isBase();
        }

        return $this->addTag($name, $tagBuilder);
    }

    /**
     * Добавляет тег для обновления (аналогично, для UPDATE).
     */
    public function addUpdateTag(
        string $name,
        array $rules,
        array $messages = [],
        array $updateRules = [],
        array $updateMessages = [],
        bool $isBase = false,
        bool $authorize = true
    ): self {
        $tagBuilder = RequestTagBuilder::make()
            ->rules($rules)
            ->messages($messages)
            ->updateRules($updateRules)
            ->updateMessages($updateMessages);

        if (!$authorize) {
            $tagBuilder->unauthorize();
        }

        if ($isBase) {
            $tagBuilder->isBase();
        }

        return $this->addTag($name, $tagBuilder);
    }

    /**
     * Добавляет тег для удаления (аналогично, для DELETE).
     */
    public function addDeleteTag(
        string $name,
        array $rules,
        array $messages = [],
        array $deleteRules = [],
        array $deleteMessages = [],
        bool $isBase = false,
        bool $authorize = true
    ): self {
        $tagBuilder = RequestTagBuilder::make()
            ->rules($rules)
            ->messages($messages)
            ->deleteRules($deleteRules)
            ->deleteMessages($deleteMessages);

        if (!$authorize) {
            $tagBuilder->unauthorize();
        }

        if ($isBase) {
            $tagBuilder->isBase();
        }

        return $this->addTag($name, $tagBuilder);
    }

    /**
     * Добавляет универсальный тег (для CREATE/UPDATE/DELETE, вариант 1 или 2).
     */
    public function addRequestTag(
        string $name,
        array $rules,
        array $messages = [],
        array $createRules = [],
        array $updateRules = [],
        array $deleteRules = [],
        array $createMessages = [],
        array $updateMessages = [],
        array $deleteMessages = [],
        bool $isBase = false,
        bool $authorize = true
    ): self {
        $tagBuilder = RequestTagBuilder::make()
            ->rules($rules)
            ->messages($messages)
            ->createRules($createRules)
            ->updateRules($updateRules)
            ->deleteRules($deleteRules)
            ->createMessages($createMessages)
            ->updateMessages($updateMessages)
            ->deleteMessages($deleteMessages);

        if (!$authorize) {
            $tagBuilder->unauthorize();
        }

        if ($isBase) {
            $tagBuilder->isBase();
        }

        return $this->addTag($name, $tagBuilder);
    }

    /**
     * Парсит весь конфиг из массива.
     * Автоматически определяет тип тега по полям (class, rules, is_base и т.д.).
     */
    public function fromConfig(array $config): self
    {
        foreach ($config as $name => $data) {
            $tagBuilder = RequestTagBuilder::make();

            if (isset($data['class'])) {
                $tagBuilder->class($data['class']);
            } else {
                // Общие правила
                if (isset($data['rules'])) {
                    $tagBuilder->rules($data['rules']);
                }
                if (isset($data['messages'])) {
                    $tagBuilder->messages($data['messages']);
                }

                // CREATE-specific
                if (isset($data['create_rules'])) {
                    $tagBuilder->createRules($data['create_rules']);
                }
                if (isset($data['create_messages'])) {
                    $tagBuilder->createMessages($data['create_messages']);
                }

                // UPDATE-specific
                if (isset($data['update_rules'])) {
                    $tagBuilder->updateRules($data['update_rules']);
                }
                if (isset($data['update_messages'])) {
                    $tagBuilder->updateMessages($data['update_messages']);
                }

                // DELETE-specific
                if (isset($data['delete_rules'])) {
                    $tagBuilder->deleteRules($data['delete_rules']);
                }
                if (isset($data['delete_messages'])) {
                    $tagBuilder->deleteMessages($data['delete_messages']);
                }
            }

            if (isset($data['is_base']) && $data['is_base']) {
                $tagBuilder->isBase();
            }

            if (isset($data['authorize']) && !$data['authorize']) {
                $tagBuilder->unauthorize();
            }

            $this->addTag($name, $tagBuilder);
        }

        return $this;
    }

    /**
     * Строит DTO.
     */
    public function build(): CrudlerRequestDTO
    {
        $tags = [];
        foreach ($this->tags as $name => $tagBuilder) {
            if ($tagBuilder->getClass() !== null) {
                // Вариант 3: игнорируем rules/messages
                $tags[$name] = RequestTagDTO::start(
                    class: $tagBuilder->getClass(),
                    authorize: $tagBuilder->getAuthorize()
                );
            } elseif ($tagBuilder->getIsBase()) {
                // Вариант 2: SimpleRequest, игнорируем create/update/delete (одно действие)
                $tags[$name] = RequestTagDTO::start(
                    rules: $tagBuilder->getRules(),
                    messages: $tagBuilder->getMessages(),
                    isBase: true,
                    authorize: $tagBuilder->getAuthorize()
                );
            } else {
                // Вариант 1: комбинируем rules + create/update/delete
                $tags[$name] = RequestTagDTO::start(
                    rules: $tagBuilder->getRules(),
                    createRules: $tagBuilder->getCreateRules(),
                    updateRules: $tagBuilder->getUpdateRules(),
                    deleteRules: $tagBuilder->getDeleteRules(),
                    messages: $tagBuilder->getMessages(),
                    createMessages: $tagBuilder->getCreateMessages(),
                    updateMessages: $tagBuilder->getUpdateMessages(),
                    deleteMessages: $tagBuilder->getDeleteMessages(),
                    authorize: $tagBuilder->getAuthorize()
                );
            }
        }

        return new CrudlerRequestDTO($tags);
    }
}
