<?php

namespace Crudler\Repositories\DTO\Parts\ShowOnceById;

use Core\DTO\FormDTO;
use Closure;

final class RepositoryShowOnceDTO
{
    /**
     * @var FormDTO
     */
    public readonly FormDTO $formDTO;

    /**
     * @var RepositoryShowOnceConfigDTO|Closure|null
     */
    public readonly RepositoryShowOnceConfigDTO|Closure|null $config;

    /**
     * @param FormDTO $formDTI
     * @param RepositoryShowOnceConfigDTO|callable|null $config
     */
    public function __construct(
        FormDTO $formDTO,
        RepositoryShowOnceConfigDTO|callable|null $config = null
    ) {
        $this->formDTO = $formDTO;

        if (is_callable($config)) {
            $this->config = $config instanceof Closure
                ? $config
                : Closure::fromCallable($config);
            return;
        }

        $this->config = $config;
    }

    public function isCallable(): bool
    {
        return $this->config instanceof Closure;
    }

    public function isEmpty(): bool
    {
        return $this->config === null;
    }
}
