<?php

namespace Crudler\Repositories\DTO\Parts\Unique;

use Crudler\Repositories\Interfaces\IUniqueItemCallable;

use Core\DTO\FormDTO;

class RepositoryUniqueDTO
{
    /**
     * @var string|null
     */
    public readonly ?string $message;

    /**
     * @var array<string, RepositoryUniqueItemDTO>|IUniqueItemCallable|null
     */
    public readonly IUniqueItemCallable|array|null $mapParams;

    /**
     * @var FormDTO
     */
    public readonly FormDTO $formDTO;

    /**
     * @param array<string, RepositoryUniqueItemDTO>|callable|null $mapParams
     * @param string|null $message
     */
    public function __construct(
        FormDTO $formDTO,
        array|IUniqueItemCallable|null $mapParams = null,
        ?string $message = null
    ) {
        $this->message = $message ?? config('crudler.repositories.is_not_unique_message');

        $this->formDTO = $formDTO;

        $this->mapParams = $mapParams;
    }

    public function isCallable(): bool
    {
        return $this->mapParams instanceof IUniqueItemCallable;
    }

    public function isEmpty(): bool
    {
        return $this->mapParams === null;
    }

    public function toArray(): array
    {
        if ($this->mapParams === null) {
            return [];
        }

        // Если callable — вызываем
        $items = $this->isCallable()
            ? ($this->mapParams)($this->formDTO)
            : $this->mapParams;

        $result = [];

        foreach ($items as $key => $item) {
            if (! $item instanceof RepositoryUniqueItemDTO) {
                continue;
            }

            $config = $item->config;

            $result[$item->field] = [
                'column' => $config instanceof RepositoryUniqueConfigDTO
                    ? $config->column
                    : $config,
                'modifier' => $config->modifier ?? null,
                'is_or_where' => $config->isOrWhere ?? false,
            ];
        }

        return $result;
    }

}
