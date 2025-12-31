<?php

namespace Crudler\Requests\Builders;

use Crudler\Requests\DTO\Parts\RequestTagDTO;

class RequestTagBuilder
{
    private ?string $class = null;

    private bool $authorize = true;

    private bool $isBase = false;

    private ?array $rules = [];

    private ?array $createRules = [];

    private ?array $updateRules = [];

    private ?array $deleteRules = [];

    private ?array $messages = [];

    private ?array $createMessages = [];

    private ?array $updateMessages = [];

    private ?array $deleteMessages = [];

    public static function make(): self
    {
        return new self();
    }

    public function getClass() {
        return $this->class;
    }

    public function getAuthorize() {
        return $this->authorize;
    }

    public function getRules() {
        return $this->rules;
    }

    public function getCreateRules() {
        return $this->createRules;
    }

    public function getUpdateRules() {
        return $this->updateRules;
    }

    public function getDeleteRules() {
        return $this->deleteRules;
    }

    public function getIsBase() {
        return $this->isBase;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function getCreateMessages() {
        return $this->createMessages;
    }

    public function getUpdateMessages() {
        return $this->updateMessages;
    }

    public function getDeleteMessages() {
        return $this->deleteMessages;
    }

    public function class(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function rules(?array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function createRules(?array $createRules): self
    {
        $this->createRules = $createRules;

        return $this;
    }

    public function updateRules(?array $updateRules): self
    {
        $this->updateRules = $updateRules;

        return $this;
    }

    public function deleteRules(?array $deleteRules): self
    {
        $this->deleteRules = $deleteRules;

        return $this;
    }

    public function messages(?array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function createMessages(?array $createMessages): self
    {
        $this->createMessages = $createMessages;

        return $this;
    }

    public function updateMessages(?array $updateMessages): self
    {
        $this->updateMessages = $updateMessages;

        return $this;
    }

    public function deleteMessages(?array $deleteMessages): self
    {
        $this->deleteMessages = $deleteMessages;

        return $this;
    }

    public function isBase(): self
    {
        $this->isBase = true;

        return $this;
    }

    public function unauthorize(): self
    {
        $this->authorize = false;

        return $this;
    }

    public function generate(): RequestTagDTO
    {
        return RequestTagDTO::start(
            class: $this->class,
            rules: $this->rules,
            createRules: $this->createRules,
            updateRules: $this->updateRules,
            deleteRules: $this->deleteRules,
            messages: $this->messages,
            createMessages: $this->createMessages,
            updateMessages: $this->updateMessages,
            deleteMessages: $this->deleteMessages,
            isBase: $this->isBase,
            authorize: $this->authorize,
        );
    }

    public function generateCreateTag(
        array $rules,
        array $messages = [],
        array $createRules = [],
        array $createMessages = [],
        bool $isBase = false,
        bool $authorize = true,
    ): RequestTagDTO {
        return RequestTagDTO::start(
            class: null,
            rules: $rules,
            createRules: $createRules,
            updateRules: [],
            deleteRules: [],
            messages: $messages,
            createMessages: $createMessages,
            updateMessages: [],
            deleteMessages: [],
            isBase: $isBase,
            authorize: $authorize,
        );
    }

    public function generateUpdateTag(
        array $rules,
        array $messages = [],
        array $updateRules = [],
        array $updateMessages = [],
        bool $isBase = true,
        bool $authorize = true,
    ): RequestTagDTO {
        return RequestTagDTO::start(
            class: null,
            rules: $rules,
            createRules: [],
            updateRules: $updateRules,
            deleteRules: [],
            messages: $messages,
            createMessages: [],
            updateMessages: $updateMessages,
            deleteMessages: [],
            isBase: $isBase,
            authorize: $authorize,
        );
    }

    public function generateDeleteTag(
        array $rules,
        array $messages = [],
        array $deleteRules = [],
        array $deleteMessages = [],
        bool $isBase = true,
        bool $authorize = true,
    ): RequestTagDTO {
        return RequestTagDTO::start(
            class: null,
            rules: $rules,
            createRules: [],
            updateRules: [],
            deleteRules: $deleteRules,
            messages: $messages,
            createMessages: [],
            updateMessages: [],
            deleteMessages: $deleteMessages,
            isBase: $isBase,
            authorize: $authorize,
        );
    }

    public function generateRequestTag(
        array $rules,
        array $messages = [],
        array $createRules = [],
        array $updateRules = [],
        array $deleteRules = [],
        array $createMessages = [],
        array $updateMessages = [],
        array $deleteMessages = [],
        bool $isBase = true,
        bool $authorize = true,
    ): RequestTagDTO {
        return RequestTagDTO::start(
            class: null,
            rules: $rules,
            createRules: $createRules,
            updateRules: $updateRules,
            deleteRules: $deleteRules,
            messages: $messages,
            createMessages: $createMessages,
            updateMessages: $updateMessages,
            deleteMessages: $deleteMessages,
            isBase: $isBase,
            authorize: $authorize,
        );
    }

    public function generateExistRequestTag(string $class): RequestTagDTO {
        return RequestTagDTO::start(
            class: $class,
            rules: [],
            createRules: [],
            updateRules: [],
            deleteRules: [],
            messages: [],
            createMessages: [],
            updateMessages: [],
            deleteMessages: [],
            isBase: false,
            authorize: true,
        );
    }
}
