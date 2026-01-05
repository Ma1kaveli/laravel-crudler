<?php

namespace Crudler\Repositories\Builders;

use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceConfigDTO;
use Crudler\Repositories\Interfaces\{
    IShowOnceByIdCallable,
    IFromConfigCallable
};

use Core\DTO\FormDTO;
use Closure;
use Crudler\Adapters\ClosureHookAdapter;

final class RepositoryShowOnceBuilder
{
    private FormDTO $dto;

    private ?array $with = null;
    private ?array $withCount = null;
    private bool $withTrashed = false;
    private ?string $message = null;
    private ?Closure $query = null;

    private IShowOnceByIdCallable|null $config = null;

    public static function make(FormDTO $dto): self
    {
        $builder = new self();
        $builder->dto = $dto;

        return $builder;
    }

    public function with(array $relations): self
    {
        $this->with = $relations;

        return $this;
    }

    public function withCount(array $relations): self
    {
        $this->withCount = $relations;

        return $this;
    }

    public function withoutTrashed(): self
    {
        $this->withTrashed = false;

        return $this;
    }

    public function withTrashed(): self
    {
        $this->withTrashed = true;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function query(callable $query): self
    {
        $this->query = $query instanceof Closure
            ? $query
            : Closure::fromCallable($query);

        return $this;
    }

    /**
     * Summary of closure
     *
     * @param IShowOnceByIdCallable|callable(FormDTO $formDTO): RepositoryShowOnceConfigDTO $closure
     *
     * @return RepositoryShowOnceBuilder
     */
    public function closure(IShowOnceByIdCallable|callable $closure): self
    {
        $this->config = $closure instanceof IShowOnceByIdCallable
            ? $closure
            : new class($closure) extends ClosureHookAdapter implements IShowOnceByIdCallable {
                public function __invoke(FormDTO $formDTO): RepositoryShowOnceConfigDTO {
                    return ($this->closure)($formDTO);
                }
            };

        return $this;
    }

    /**
     * Summary of fromConfig
     *
     * @param array|IFromConfigCallable|callable(FormDTO $formDTO): array $config
     *
     * @return RepositoryShowOnceBuilder
     */
    public function fromConfig(array|IFromConfigCallable|callable $config): self
    {
        if (!is_array($config)) {
            $this->fromConfig(
                ($config)($this->dto)
            );
        } else {
            if (isset($config['with'])) {
                $this->with($config['with']);
            }

            if (isset($config['with_count'])) {
                $this->withCount($config['with_count']);
            }

            if (isset($config['message'])) {
                $this->message($config['message']);
            }

            if (isset($config['query'])) {
                $this->message($config['query']);
            }

            if (isset($config['with_trashed']) && !$config['with_trashed']) {
                $this->withoutTrashed();
            }

            if (isset($config['with_trashed']) && $config['with_trashed']) {
                $this->withTrashed();
            }
        }

        return $this;
    }

    public function build(): RepositoryShowOnceDTO
    {
        if ($this->config instanceof Closure) {
            return new RepositoryShowOnceDTO(
                $this->dto,
                $this->config
            );
        }

        return new RepositoryShowOnceDTO(
            $this->dto,
            new RepositoryShowOnceConfigDTO(
                with: $this->with,
                withCount: $this->withCount,
                withTrashed: $this->withTrashed,
                message: $this->message,
                query: $this->query
            )
        );
    }
}
