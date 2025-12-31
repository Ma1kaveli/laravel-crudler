<?php

namespace Crudler\Resources\DTO;

use Crudler\Resources\DTO\Parts\{
    ResourceDataDTO,
    ResourceAdditionalDataDTO,
    ResourceMethodDTO
};

class CrudlerResourceGeneratorDTO {
    /**
     * @param array<string,ResourceDataDTO> $data - Array with model fields that the toArray method will return
     * @param array<string,ResourceAdditionalDataDTO> $additionalData - Array with other resources that the getAdditionalData method will return
     * @param array<string,ResourceMethodDTO> $methods - Custom methods in resource generator
     */
    public function __construct(
        public readonly array $data,
        public readonly array $additionalData,
        public readonly array $methods,
    ) {}

    /**
     * Summary of fromArray
     *
     * @param array $data
     *
     * @return CrudlerResourceGeneratorDTO
     */
    public static function fromArray(array $data): static {
        return self::start(
            $data['data'],
            $data['additional_data'],
            $data['methods']
        );
    }

    /**
     * Summary of start
     *
     * @param array $data
     * @param array $additionalData
     * @param array $methods
     *
     * @return static
     */
    public static function start(
        array $data = [],
        array $additionalData = [],
        array $methods = [],
    ): static {
        return new self(
            data: self::wrap($data, ResourceDataDTO::class),
            additionalData: self::wrap($additionalData, ResourceAdditionalDataDTO::class),
            methods: self::wrap($methods, ResourceMethodDTO::class),
        );
    }

    /**
     * Summary of setAdditionalData
     *
     * @param array $additionalData
     *
     * @return CrudlerResourceGeneratorDTO
     */
    public function setAdditionalData(array $additionalData = []): static {
        return new static(
            data: $this->data,
            additionalData: $this->wrap($additionalData, ResourceAdditionalDataDTO::class),
            methods: $this->methods
        );
    }

    /**
     * Config to DTO
     *
     * @param array $raw
     * @param string $class
     *
     * @return object[]
     */
    private static function wrap(array $raw, string $class): array
    {
        $result = [];

        foreach ($raw as $key => $value) {
            $result[$key] = new $class($value, $key);
        }

        return $result;
    }
}
