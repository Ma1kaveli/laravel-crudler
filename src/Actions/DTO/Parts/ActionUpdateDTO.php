<?php

namespace Crudler\Actions\DTO\Parts;

use Core\DTO\ExecutionOptionsDTO;
use Core\DTO\FormDTO;
use Crudler\Actions\Constants\CrudlerPlaceUniqueEnum;
use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\DTO\Parts\InAction\InActionDTO;

use Illuminate\Support\Facades\Config;

class ActionUpdateDTO
{
    public function __construct(
        public readonly FormDTO $formDTO,
        public readonly ?ExecutionOptionsDTO $config = null,
        public readonly bool $withValidation = true,
        public readonly bool $getFunc = false,
        public readonly bool $withTransaction = false,
        public readonly CrudlerPlaceUniqueEnum $placeUnique = CrudlerPlaceUniqueEnum::default,
        public readonly ?string $errorMessage = null,
        public readonly ?string $successLog = null,
        public readonly ?string $errorLog = null,
        public readonly ?BeforeActionDTO $beforeActionDTO = null,
        public readonly ?InActionDTO $inActionDTO = null,
    ) {}

    /**
     * Summary of start
     *
     * @param FormDTO $formDTO
     * @param ?ExecutionOptionsDTO $config = null
     * @param bool $withValidation = true
     * @param bool $getFunc = false
     * @param bool $withTransaction = false
     * @param CrudlerPlaceUniqueEnum $placeUnique = CrudlerPlaceUniqueEnum::default,
     * @param ?string $errorMessage = null
     * @param ?string $successLog = null
     * @param ?string $errorLog = null
     * @param ?BeforeActionDTO $beforeActionDTO = null
     * @param ?InActionDTO $inActionDTO = null
     *
     * @return static
     */
    public static function start(
        FormDTO $formDTO,
        ?ExecutionOptionsDTO $config = null,
        bool $withValidation = true,
        bool $getFunc = false,
        bool $withTransaction = false,
        CrudlerPlaceUniqueEnum $placeUnique = CrudlerPlaceUniqueEnum::default,
        ?string $errorMessage = null,
        ?string $successLog = null,
        ?string $errorLog = null,
        ?BeforeActionDTO $beforeActionDTO = null,
        ?InActionDTO $inActionDTO = null,
    ): self {
        return new self(
            formDTO: $formDTO,
            config: $config,
            withValidation: $withValidation,
            getFunc: $getFunc,
            withTransaction: $withTransaction,
            placeUnique: $placeUnique,
            errorMessage: $errorMessage ?? Config::get('crudler.actions.error_update_message'),
            successLog: $successLog,
            errorLog: $errorLog,
            beforeActionDTO: $beforeActionDTO,
            inActionDTO: $inActionDTO
        );
    }

    /**
     * Summary of setFormDTO
     *
     * @param FormDTO $formDTO
     *
     * @return ActionUpdateDTO
     */
    public function setFormDTO(FormDTO $formDTO): self
    {
        return new self(
            $formDTO,
            $this->config,
            $this->withValidation,
            $this->getFunc,
            $this->withTransaction,
            $this->placeUnique,
            $this->errorMessage,
            $this->successLog,
            $this->errorLog,
            $this->beforeActionDTO,
            $this->inActionDTO,
        );
    }
}
