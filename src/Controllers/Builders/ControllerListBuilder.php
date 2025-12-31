<?php

namespace Crudler\Controllers\Builders;

use Crudler\Controllers\DTO\Parts\ControllerListDTO;
use Crudler\Controllers\DTO\Parts\List\ListConfigDTO;
use Crudler\Controllers\Interfaces\IListCallableDTO;

use Core\Interfaces\IListDTO;

class ControllerListBuilder
{
    public IListCallableDTO|IListDTO|ListConfigDTO|null $dto = null;

    public bool $isGetAll = false;

    public bool $isPaginateResponse = true;

    public array $additionalData = [];

    public static function make(): self
    {
        return new self();
    }

    public function dto(IListCallableDTO|IListDTO|ListConfigDTO $dto): self
    {
        $this->dto = $dto;

        return $this;
    }

    public function isGetAll(): self
    {
        $this->isGetAll = true;

        return $this;
    }

    public function isNotPaginateResponse(): self
    {
        $this->isPaginateResponse = false;

        return $this;
    }

    public function additionalData(array $data): self
    {
        $this->additionalData = $data;

        return $this;
    }

    public function fromConfig(array $config): self {
        if (isset($config['dto'])) {
            $this->dto = $config['dto'];
        }

        if (isset($config['is_get_all'])) {
            $this->isGetAll = $config['is_get_all'];
        }

        if (isset($config['is_paginate_response'])) {
            $this->isPaginateResponse = $config['is_paginate_response'];
        }

        if (isset($config['additional_data'])) {
            $this->additionalData = $config['additional_data'];
        }

        return $this;
    }

    public function build(): ControllerListDTO
    {
        return new ControllerListDTO(
            dto: $this->dto,
            isGetAll: $this->isGetAll,
            isPaginateResponse: $this->isPaginateResponse,
            additionalData: $this->additionalData
        );
    }
}
