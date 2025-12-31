<?php

namespace Crudler\Services\DTO;

use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Crudler\Services\DTO\Parts\ServiceUpdateDTO;

class CrudlerServiceDTO {
    public function __construct(
        public readonly ?ServiceCreateDTO $createDTO = null,
        public readonly ?ServiceUpdateDTO $updateDTO = null,
        public readonly ?ServiceDeleteDTO $deleteDTO = null,
        public readonly ?ServiceRestoreDTO $restoreDTO = null,
    ) {}

    /**
     * Summary of start
     *
     * @param ?ServiceCreateDTO $createDTO = null
     * @param ?ServiceUpdateDTO $updateDTO = null
     * @param ?ServiceDeleteDTO $deleteDTO = null
     * @param ?ServiceRestoreDTO $restoreDTO = null
     *
     * @return CrudlerServiceDTO
     */
    public static function start(
        ?ServiceCreateDTO $createDTO = null,
        ?ServiceUpdateDTO $updateDTO = null,
        ?ServiceDeleteDTO $deleteDTO = null,
        ?ServiceRestoreDTO $restoreDTO = null
    ): static {
        return new self(
            createDTO: $createDTO,
            updateDTO: $updateDTO,
            deleteDTO: $deleteDTO,
            restoreDTO: $restoreDTO,
        );
    }

    /**
     * Summary of setCreateDTO
     *
     * @param ServiceCreateDTO $createDTO
     *
     * @return CrudlerServiceDTO
     */
    public function setCreateDTO(ServiceCreateDTO $createDTO): static {
        return new static(
            createDTO: $createDTO,
            updateDTO: $this->updateDTO,
            deleteDTO: $this->deleteDTO,
            restoreDTO: $this->restoreDTO,
        );
    }

    /**
     * Summary of setUpdateDTO
     *
     * @param ServiceUpdateDTO $updateDTO
     *
     * @return CrudlerServiceDTO
     */
    public function setUpdateDTO(ServiceUpdateDTO $updateDTO): static {
        return new static(
            createDTO: $this->createDTO,
            updateDTO: $updateDTO,
            deleteDTO: $this->deleteDTO,
            restoreDTO: $this->restoreDTO,
        );
    }

    /**
     * Summary of setDeleteDTO
     *
     * @param ServiceDeleteDTO $updateDTO
     *
     * @return CrudlerServiceDTO
     */
    public function setDeleteDTO(ServiceDeleteDTO $deleteDTO): static {
        return new static(
            createDTO: $this->createDTO,
            updateDTO: $this->updateDTO,
            deleteDTO: $deleteDTO,
            restoreDTO: $this->restoreDTO,
        );
    }

    /**
     * Summary of setRestoreDTO
     *
     * @param ServiceRestoreDTO $updateDTO
     *
     * @return CrudlerServiceDTO
     */
    public function setRestoreDTO(ServiceRestoreDTO $restoreDTO): static {
        return new static(
            createDTO: $this->createDTO,
            updateDTO: $this->updateDTO,
            deleteDTO: $this->deleteDTO,
            restoreDTO: $restoreDTO,
        );
    }
}
