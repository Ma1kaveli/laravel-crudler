<?php

namespace Crudler\Services\DTO\Parts;

use Core\DTO\FormDTO;
use Crudler\Services\Traits\ServiceMapperAware;

use Illuminate\Database\Eloquent\Model;

class ServiceUpdateDTO {
    use ServiceMapperAware;

    /**
     * Summary of __construct
     *
     * @param ?Model $data = null
     * @param FormDTO $formDTO
     * @param array<string,ServiceMapperFieldDTO> $mapper - Custom methods in resource generator
     */
    public function __construct(
        public readonly ?Model $data = null,
        public readonly FormDTO $formDTO,
        public readonly array $mapper,
    ) {}

    /**
     * Summary of start
     *
     * @param ?Model $data = null
     * @param FormDTO $formDTO
     * @param array $mapper = []
     *
     * @return static
     */
    public static function start(
        ?Model $data = null,
        FormDTO $formDTO,
        array $mapper = []
    ): static {
        return new self(
            data: $data,
            formDTO: $formDTO,
            mapper: self::wrapMapper($mapper),
        );
    }
}
