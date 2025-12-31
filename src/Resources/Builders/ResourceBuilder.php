<?php

namespace Crudler\Resources\Builders;

use Crudler\Mapper\CrudlerMapper;
use Crudler\Resources\DTO\CrudlerResourceDTO;
use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;

class ResourceBuilder
{
    private array $data = [];
    private array $additional = [];
    private array $methods = [];

    private ?string $resource = null;

    public static function make(): self
    {
        return new self();
    }

    public function data(CrudlerMapper $mapper): self
    {
        $this->data = $mapper->toArray();

        return $this;
    }

    public function _data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function additional(CrudlerMapper $mapper): self
    {
        $this->additional = $mapper->toArray();

        return $this;
    }

    public function _additional(array $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    public function methods(CrudlerMapper $mapper): self
    {
        $this->methods = $mapper->toArray();

        return $this;
    }

    public function _methods(array $methods): self
    {
        $this->methods = $methods;

        return $this;
    }

    public function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Парсит весь конфиг из массива.
     *
     * @param array $config
     */
    public function fromConfig(array $config): self
    {
        $this->_data($config['data']);
        $this->_additional($config['additional_data']);
        $this->_methods($config['methods']);

        return $this;
    }

    /**
     * This method build generator DTO for use Resource Crudler
     *
     * @return CrudlerResourceGeneratorDTO
     */
    public function generator(): CrudlerResourceGeneratorDTO
    {
        return CrudlerResourceGeneratorDTO::start(
            data: $this->data,
            additionalData: $this->additional,
            methods: $this->methods,
        );
    }

    /**
     * This method build Resource DTO for Crudler
     *
     * @return CrudlerResourceDTO
     */
    public function build(): CrudlerResourceDTO
    {
        $dto = $this->generator();

        return CrudlerResourceDTO::start(
            $dto,
            $this->resource
        );
    }
}
