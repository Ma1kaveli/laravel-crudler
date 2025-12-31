<?php

namespace Crudler\Repositories\Builders;

use Crudler\Repositories\DTO\CrudlerRepositoryDTO;
use Crudler\Repositories\DTO\Parts\Unique\RepositoryUniqueDTO;
use Crudler\Repositories\DTO\Parts\ShowOnceById\RepositoryShowOnceDTO;

use Core\DTO\FormDTO;

final class RepositoryBuilder
{
    private ?RepositoryUniqueDTO $unique = null;
    private ?RepositoryShowOnceDTO $showOnce = null;

    public static function make(): self
    {
        return new self();
    }

    public function unique(RepositoryUniqueDTO $dto): self
    {
        $this->unique = $dto;

        return $this;
    }

    public function showOnceById(RepositoryShowOnceDTO $dto): self
    {
        $this->showOnce = $dto;

        return $this;
    }

    public function uniqueBuilder(RepositoryUniqueBuilder $builder): self
    {
        return $this->unique($builder->build());
    }

    public function showOnceByIdBuilder(RepositoryShowOnceBuilder $builder): self
    {
        return $this->showOnceById($builder->build());
    }

    public function fromConfig(FormDTO $formDTO, array $config)
    {
        if (isset($config['uniques'])) {
            $builder = RepositoryUniqueBuilder::make($formDTO)->fromConfig($config['uniques']);

            if (isset($config['unique_message'])) {
                $builder = $builder->message($config['unique_message']);
            }

            $this->uniqueBuilder($builder);
        }

        if (isset($config['show_once_by_id'])) {
            $builder = RepositoryShowOnceBuilder::make($formDTO)->fromConfig($config['show_once_by_id']);
        }
    }

    public function build(): CrudlerRepositoryDTO
    {
        return new CrudlerRepositoryDTO(
            uniqueDTO: $this->unique,
            showOnceDTO: $this->showOnce
        );
    }
}
