<?php

namespace Crudler\Services\DTO\Parts;

use Core\DTO\FormDTO;
use Crudler\Services\Traits\ServiceMapperAware;

class ServiceCreateDTO {
    use ServiceMapperAware;

    /**
     * Summary of __construct
     *
     * @param FormDTO $formDTO
     * @param array<string,ServiceMapperFieldDTO> $mapper
     */
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly array $mapper,
    ) {}

    /**
     * Summary of start
     *
     * @param FormDTO $formDTO
     * @param array $mapper = []
     *
     * @return static
     */
    public static function start(
        FormDTO $formDTO,
        array $mapper = []
    ): static {
        return new self(
            formDTO: $formDTO,
            mapper: self::wrapMapper($mapper),
        );
    }
}
