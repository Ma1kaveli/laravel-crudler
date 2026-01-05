<?php

namespace Crudler\Repositories\Builders;

use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueItemDTO;
use Crudler\Repositories\Interfaces\{
    IUniqueItemCallable,
    IFromConfigCallable
};

use Core\DTO\FormDTO;
use Crudler\Adapters\ClosureHookAdapter;
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

    /**
     * Summary of closure
     *
     * @param IUniqueItemCallable|callable(FormDTO $formDTO): array $closure
     *
     * @return RepositoryUniqueBuilder
     */
    public function closure(IUniqueItemCallable|callable $closure): self
    {
        $this->uniques = $closure instanceof IUniqueItemCallable
            ? $closure
            : new class($closure) extends ClosureHookAdapter implements IUniqueItemCallable {
                public function __invoke(FormDTO $formDTO): array {
                    return ($this->closure)($formDTO);
                }
            };

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

    /**
     * Summary of fromConfig
     *
     * @param array|IFromConfigCallable|callable(FormDTO $formDTO): array $config
     *
     * @return RepositoryUniqueBuilder|Exception
     */
    public function fromConfig(
        array|IFromConfigCallable|callable $config
    ): self {
        if (!is_array($config)) {
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

