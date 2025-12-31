<?php

namespace Crudler\Repositories\DTO\Parts\ShowOnceById;

use Closure;

final class RepositoryShowOnceConfigDTO
{
    /**
     * @var array<int, string>|null
     */
    public readonly ?array $with;

    /**
     * @var array<int, string>|null
     */
    public readonly ?array $withCount;

    /**
     * @var bool
     */
    public readonly bool $withTrashed;

    /**
     * @var string|null
     */
    public readonly ?string $message;

    /**
     * @var Closure|null
     */
    public readonly ?Closure $query;


    /**
     * @param array<int, string>|null $with
     * @param array<int, string>|null $withCount
     * @param bool $withTrashed
     * @param string|null $message
     * @param callable|null $query
     */
    public function __construct(
        ?array $with = null,
        ?array $withCount = null,
        bool $withTrashed = false,
        ?string $message = null,
        ?callable $query = null
    ) {
        $this->with = $with;
        $this->withCount = $withCount;
        $this->withTrashed = $withTrashed;
        $this->message = $message ?? config('crudler.repositories.show_once_by_id_not_found_message');

        if (is_callable($query)) {
            $this->query = $query instanceof Closure
                ? $query
                : Closure::fromCallable($query);
            return;
        }

        $this->query = $query;
    }
}
