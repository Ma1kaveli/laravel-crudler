<?php

namespace Crudler\Requests;

use Crudler\Requests\Core\BaseCrudlerFormRequest;
use Crudler\Requests\Core\SimpleCrudlerFormRequest;
use Crudler\Requests\DTO\CrudlerRequestDTO;
use Crudler\Requests\DTO\Parts\RequestTagDTO;
use Crudler\Requests\DTO\Parts\RequestRulesDTO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CrudlerRequest
{
    protected CrudlerRequestDTO $dto;

    public function __construct(CrudlerRequestDTO $dto)
    {
        $this->dto = $dto;
    }

    /**
     * Создаёт instance FormRequest на основе тега.
     *
     * @param string $tag Имя тега (e.g., 'tag_create')
     * @param Request $rawRequest Опционально: исходный request для инжекции (если нужно)
     *
     * @return FormRequest
     */
    public function make(string $tag, Request $rawRequest): FormRequest
    {
        if (!isset($this->dto->tags[$tag])) {
            throw new \InvalidArgumentException("Tag '$tag' not found in DTO");
        }

        $tagDto = $this->dto->tags[$tag];
        $config = $this->buildConfigFromDto($tagDto);

        if ($tagDto->class) {
            // Вариант 3: прокидываем существующий класс (без config, manual authorize если нужно)
            $requestClass = $tagDto->class;
            $instance = new $requestClass();
        } elseif ($tagDto->isBase) {
            // Вариант 2: simple FormRequest (без контекстов)
            $instance = new SimpleCrudlerFormRequest($config);
        } else {
            // Вариант 1: generated с контекстами
            $instance = new BaseCrudlerFormRequest($config);
        }

        $instance->initialize(
            $rawRequest->query() ?? [],
            $rawRequest->request->all() ?? [],
            $rawRequest->attributes->all() ?? [],
            $rawRequest->cookies->all() ?? [],
            $rawRequest->files->all() ?? [],
            $rawRequest->server() ?? []
        );

        // Установка метода для определения контекста
        $instance->setMethod($rawRequest->method());

        // Установка контейнера для валидатора
        $instance->setContainer(app());

        $instance->setRedirector(app(\Illuminate\Routing\Redirector::class));

        // Запуск валидации (бросит ValidationException при ошибке)
        $instance->validateResolved();

        return $instance;
    }

    private function buildConfigFromDto(RequestTagDTO $tagDto): array
    {
        // Базовый config
        $config = [
            'authorize' => $tagDto->authorize,
            'messages' => $tagDto->messages ?? [],
        ];

        if ($tagDto->isBase) {
            // Для simple: только общие rules/messages (игнорируем create/update/delete)
            $config['rules'] = $this->unwrapRules($tagDto->rules);
        } else {
            // Для contextual: комбинируем общие + специфические
            $config['rules'] = $this->unwrapRules($tagDto->rules);
            $config['create_rules'] = $this->unwrapRules($tagDto->createRules);
            $config['update_rules'] = $this->unwrapRules($tagDto->updateRules);
            $config['delete_rules'] = $this->unwrapRules($tagDto->deleteRules);
            $config['create_messages'] = $tagDto->createMessages ?? [];
            $config['update_messages'] = $tagDto->updateMessages ?? [];
            $config['delete_messages'] = $tagDto->deleteMessages ?? [];
        }

        return $config;
    }

    /**
     * Unwrap'ит rules из DTO в плоский array (field => [rule_values])
     *
     * @param ?array<string,RequestRulesDTO>
     */
    private function unwrapRules(?array $rulesDto): array
    {
        if (empty($rulesDto)) {
            return [];
        }

        $rules = [];
        foreach ($rulesDto as $field => $rulesDtoItem) {
            // rulesDtoItem->value = array<RuleDTO>
            $rules[$field] = array_map(
                fn($ruleDto) => $ruleDto->value,
                $rulesDtoItem->value ?? []
            );
        }

        return $rules;
    }
}
