<?php

namespace Crudler\Actions\Builders;

use Core\DTO\ExecutionOptionsDTO;
use Core\DTO\FormDTO;
use Core\DTO\OnceDTO;
use Crudler\Actions\Constants\CrudlerPlaceUniqueEnum;
use Crudler\Actions\DTO\Parts\ActionCreateDTO;
use Crudler\Actions\DTO\Parts\ActionDeleteDTO;
use Crudler\Actions\DTO\Parts\ActionRestoreDTO;
use Crudler\Actions\DTO\Parts\ActionUpdateDTO;
use Crudler\Actions\DTO\Parts\BeforeAction\BeforeActionDTO;
use Crudler\Actions\DTO\Parts\InAction\InActionDTO;

use Exception;

class ActionItemBuilder
{
    private FormDTO|OnceDTO|null $dto = null;

    private ?ExecutionOptionsDTO $config = null;

    private bool $withValidation = true;

    private bool $getFunc = false;

    private bool $withTransaction = true;

    private CrudlerPlaceUniqueEnum $placeUnique = CrudlerPlaceUniqueEnum::default;

    private ?string $errorMessage = null;

    private ?string $successLog = null;

    private ?string $errorLog = null;

    private ?BeforeActionDTO $beforeActionDTO = null;

    private ?InActionDTO $inActionDTO = null;

    public static function make(FormDTO|OnceDTO $dto): self {
        $builder = new self();
        $builder->dto = $dto;

        return $builder;
    }

    public function withoutValidation(): self
    {
        $this->withValidation = false;

        return $this;
    }

    public function getFunc(): self
    {
        $this->getFunc = true;

        return $this;
    }

    public function withoutTransaction(): self
    {
        $this->withTransaction = false;

        return $this;
    }

    public function placeUnique(CrudlerPlaceUniqueEnum $placeUnique): self
    {
        $this->placeUnique = $placeUnique;

        return $this;
    }

    public function errorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function successLog(string $successLog): self
    {
        $this->successLog = $successLog;

        return $this;
    }

    public function errorLog(string $errorLog): self
    {
        $this->errorLog = $errorLog;

        return $this;
    }

    public function beforeActionDTO(BeforeActionDTO $beforeActionDTO): self
    {
        $this->beforeActionDTO = $beforeActionDTO;

        return $this;
    }

    public function inActionDTO(InActionDTO $inActionDTO): self
    {
        $this->inActionDTO = $inActionDTO;

        return $this;
    }

    public function excutionOptionsDTO(ExecutionOptionsDTO $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function beforeActionBuilder(BeforeActionBuilder $builder): self
    {
        return $this->beforeActionDTO($builder->build());
    }

    public function inActionBuilder(InActionBuilder $builder): self
    {
        return $this->inActionDTO($builder->build());
    }

    public function fromConfig(array $config): self
    {
        if (isset($config['with_validation']) && !$config['with_validation']) {
            $this->withoutValidation();
        }

        if (isset($config['get_func']) && $config['get_func']) {
            $this->getFunc();
        }

        if (isset($config['with_transaction']) && !$config['with_transaction']) {
            $this->withoutTransaction();
        }

        if (isset($config['place_unique'])) {
            $this->placeUnique($config['place_unique']);
        }

        if (isset($config['error_message'])) {
            $this->errorMessage($config['error_message']);
        }

        if (isset($config['success_log'])) {
            $this->successLog($config['success_log']);
        }

        if (isset($config['error_log'])) {
            $this->errorLog($config['error_log']);
        }

        if (isset($config['config'])) {
            $this->excutionOptionsDTO($config['config']);
        }

        if (isset($config['before_action'])) {
            $this->beforeActionDTO(
                BeforeActionBuilder::make()
                    ->fromConfig($config['before_action'])
                    ->build()
            );
        }

        if (isset($config['in_action'])) {
            $this->inActionDTO(
                InActionBuilder::make()
                    ->fromConfig($config['in_action'])
                    ->build()
            );
        }

        return $this;
    }


    public function buildCreate(): ActionCreateDTO|Exception
    {
        return ActionCreateDTO::start(
            formDTO: $this->dto,
            config: $this->config,
            withValidation: $this->withValidation,
            getFunc: $this->getFunc,
            withTransaction: $this->withTransaction,
            placeUnique: $this->placeUnique,
            errorMessage: $this->errorMessage,
            successLog: $this->successLog,
            errorLog: $this->errorLog,
            beforeActionDTO: $this->beforeActionDTO,
            inActionDTO: $this->inActionDTO
        );
    }

    public function buildUpdate(): ActionUpdateDTO|Exception
    {
        return ActionUpdateDTO::start(
            formDTO: $this->dto,
            config: $this->config,
            withValidation: $this->withValidation,
            getFunc: $this->getFunc,
            withTransaction: $this->withTransaction,
            placeUnique: $this->placeUnique,
            errorMessage: $this->errorMessage,
            successLog: $this->successLog,
            errorLog: $this->errorLog,
            beforeActionDTO: $this->beforeActionDTO,
            inActionDTO: $this->inActionDTO
        );
    }

    public function buildDelete(): ActionDeleteDTO
    {
        if (!($this->dto instanceof OnceDTO)) {
            throw new Exception('Only OnceDTO can be restored');
        }

        return ActionDeleteDTO::start(
            formDTO: $this->dto,
            config: $this->config,
            withValidation: $this->withValidation,
            getFunc: $this->getFunc,
            withTransaction: $this->withTransaction,
            errorMessage: $this->errorMessage,
            successLog: $this->successLog,
            errorLog: $this->errorLog,
            beforeActionDTO: $this->beforeActionDTO,
            inActionDTO: $this->inActionDTO
        );
    }

    public function buildRestore(): ActionRestoreDTO
    {
        if (!($this->dto instanceof OnceDTO)) {
            throw new Exception('Only OnceDTO can be restored');
        }

        return ActionRestoreDTO::start(
            formDTO: $this->dto,
            config: $this->config,
            withValidation: $this->withValidation,
            getFunc: $this->getFunc,
            withTransaction: $this->withTransaction,
            errorMessage: $this->errorMessage,
            successLog: $this->successLog,
            errorLog: $this->errorLog,
            beforeActionDTO: $this->beforeActionDTO,
            inActionDTO: $this->inActionDTO
        );
    }
}
