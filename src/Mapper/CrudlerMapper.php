<?php

namespace Crudler\Mapper;

class CrudlerMapper
{
    private array $fields = [];

    public static function make(): self
    {
        return new self();
    }

    /**
     * Summary of field
     *
     * @param string $name
     * @param ?callable $resolver = null
     *
     * @return CrudlerMapper
     */
    public function field(string $name, ?callable $resolver = null): self
    {
        if ($resolver) {
            $this->fields[$name] = $resolver;
        } else {
            $this->fields[] = $name;
        }

        return $this;
    }

    public function toArray(): array
    {
        return $this->fields;
    }
}
