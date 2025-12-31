<?php

namespace Crudler\Repositories\Builders;

use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;
use Crudler\Repositories\Interfaces\{
    IUniqueItemCallable,
    IFromConfigCallable
};

use Core\DTO\FormDTO;
use Exception;

final class RepositoryUniqueBuilder
{
    private FormDTO $dto;

    /**
     * @var array<string, RepositoryUniqueItemDTO>|IUniqueItemCallable|null
     */
    private array|IUniqueItemCallable|null $uniques = null;

    private ?string $message = null;

    public static function make(FormDTO $dto): self
    {
        $builder = new self();
        $builder->dto = $dto;

        return $builder;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function closure(IUniqueItemCallable $closure): self
    {
        $this->uniques = $closure;

        return $this;
    }

    public function addUniqueItem(
        string $field,
        mixed $column,
        ?callable $modifier = null,
        bool $isOrWhere = false
    ): self {
        $uniqueItem = RepositoryUniqueItemBuilder::make()
            ->field($field)
            ->column($column);

        if (!empty($modifier)) {
            $uniqueItem = $uniqueItem->modifier($modifier);
        }

        if ($isOrWhere) {
            $uniqueItem = $uniqueItem->isOrWhere();
        }

        $this->uniques[$field] = $uniqueItem->build();

        return $this;
    }

    public function fromConfig(
        array|IFromConfigCallable $config
    ): self {
        if ($config instanceof IFromConfigCallable) {
            $this->fromConfig(
                ($config)($this->dto)
            );
        } else {
            foreach ($config as $key => $value) {
                if (!is_string($key)) {
                    throw new Exception('You need to set key as string');
                }

                $column = $value['column'];

                if (!isset($column)) {
                    throw new Exception('You need to set column as string');
                }

                $modifier = $value['modifier'] ?? null;
                $isOrWhere = $value['is_or_where'] ?? false;

                $this->addUniqueItem(
                    $key,
                    $column,
                    $modifier,
                    $isOrWhere
                );
            }
        }

        return $this;
    }

    public function build(): RepositoryUniqueDTO
    {
        return new RepositoryUniqueDTO(
            formDTO: $this->dto,
            mapParams: $this->uniques,
            message: $this->message
        );
    }
}

