<?php

namespace Crudler\Repositories\Builders;

use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueConfigDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;

use Closure;
use Exception;

final class RepositoryUniqueItemBuilder
{
    private ?string $field = null;

    private mixed $column = null;

    private ?Closure $modifier = null;

    private bool $isOrWhere = false;

    public static function make(): self
    {
        return new self();
    }

    public function field(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function column(mixed $column): self
    {
        $this->column = $column;

        return $this;
    }

    public function modifier(callable $modifier): self
    {

        $this->modifier = $modifier instanceof Closure
            ? $modifier
            : Closure::fromCallable($modifier);

        return $this;
    }

    public function isOrWhere(): self
    {
        $this->isOrWhere = true;

        return $this;
    }

    public function build(): RepositoryUniqueItemDTO
    {
        if (empty($this->field)) {
            throw new Exception('You need to set field');
        }

        if (empty($this->column)) {
            throw new Exception('You need to set column');
        }

        if (empty($this->modifier)) {
            return new RepositoryUniqueItemDTO(
                $this->field,
                $this->column
            );
        }

        return new RepositoryUniqueItemDTO(
            $this->field,
            new RepositoryUniqueConfigDTO(
                column: $this->column,
                modifier: $this->modifier,
                isOrWhere: $this->isOrWhere
            )
        );
    }
}
