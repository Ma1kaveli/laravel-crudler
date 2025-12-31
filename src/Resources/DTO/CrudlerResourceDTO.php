<?php

namespace Crudler\Resources\DTO;

use Core\Resources\BaseResource;

class CrudlerResourceDTO {
    public function __construct(
        public readonly ?BaseResource $resource,
        public readonly ?CrudlerResourceGeneratorDTO $generator,
    ) {}

    /**
     * Summary of start
     *
     * @param ?CrudlerResourceGeneratorDTO $generator
     * @param ?BaseResource $resource
     *
     * @return CrudlerResourceDTO
     */
    public static function start(
        ?CrudlerResourceGeneratorDTO $generator = null,
        ?BaseResource $resource = null
    ): self {
        return new self(
            generator: $generator,
            resource: $resource
        );
    }
}
