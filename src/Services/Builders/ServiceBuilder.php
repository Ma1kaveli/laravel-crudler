<?php

namespace Crudler\Services\Builders;

use Crudler\Mapper\CrudlerMapper;
use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Crudler\Services\DTO\Parts\ServiceUpdateDTO;

use Core\DTO\ExecutionOptionsDTO;
use Core\DTO\FormDTO;
use Illuminate\Database\Eloquent\Model;

class ServiceBuilder
{
    private FormDTO $dto;

    private ?Model $data = null;

    private array $create = [];

    private array $update = [];

    private array $delete = [];

    private array $restore = [];

    /**
     * Summary of make
     *
     * @param FormDTO $dto
     *
     * @return ServiceBuilder
     */
    public static function make(FormDTO $dto): self
    {
        $builder = new self();
        $builder->dto = $dto;

        return $builder;
    }

    /**
     * Добавляет конфигурацию для create.
     *
     * @param CrudlerMapper $mapper
     *
     * @return ServiceBuilder
     */
    public function addCreate(CrudlerMapper $mapper): self
    {
        $this->create['mapper'] = $mapper->toArray();

        return $this;
    }

    /**
     * Добавляет конфигурацию для create из массива.
     *
     * @param array $data
     *
     * @return ServiceBuilder
     */
    public function _addCreate(array $data): self
    {
        $this->create['mapper'] = $data;

        return $this;
    }

    /**
     * Добавляет конфигурацию для update.
     *
     * @param CrudlerMapper $mapper
     *
     * @return ServiceBuilder
     */
    public function addUpdate(CrudlerMapper $mapper): self
    {
        $this->update['mapper'] = $mapper->toArray();

        return $this;
    }

    /**
     * Добавляет конфигурацию для update из массива.
     *
     * @param array $data
     *
     * @return ServiceBuilder
     */
    public function _addUpdate(array $data): self
    {
        $this->update['mapper'] = $data;

        return $this;
    }

    /**
     * Добавляет конфигурацию для delete.
     *
     * @param bool $modelWithSoft = true
     * @param ?string $alreadyDeleteMessage = null
     * @param ?string $successMessage = null
     * @param ?string $errorMessage = null
     * @param ?array $configOpts = []
     *
     * @return ServiceBuilder
     */
    public function addDelete(
        bool $modelWithSoft = true,
        ?string $alreadyDeleteMessage = null,
        ?string $successMessage = null,
        ?string $errorMessage = null,
        ?array $configOpts = []
    ): self
    {
        $this->delete = [
            'model_with_soft' => $modelWithSoft,
            'already_delete_message' => $alreadyDeleteMessage,
            'success_message' => $successMessage,
            'error_message' => $errorMessage,
            'config_opts' => $configOpts,
        ];

        return $this;
    }

    /**
     * Добавляет конфигурацию для restore.
     *
     * @param ?string $notDeleteMessage = null
     * @param ?string $successMessage = null
     * @param ?string $errorMessage = null
     * @param ?array $configOpts = []
     *
     * @return ServiceBuilder
     */
    public function addRestore(
        ?string $notDeleteMessage = null,
        ?string $successMessage = null,
        ?string $errorMessage = null,
        ?array $configOpts = []
    ): self {
        $this->restore = [
            'not_delete_message' => $notDeleteMessage,
            'success_message' => $successMessage,
            'error_message' => $errorMessage,
            'config_opts' => $configOpts,
        ];

        return $this;
    }

    /**
     * Summary of setData
     *
     * @param Model $data
     *
     * @return self
     */
    public function setData(Model $data): self {
        $this->data = $data;

        return $this;
    }

    /**
     * Summary of setFormDTO
     *
     * @param FormDTO $dto
     *
     * @return self
     */
    public function setFormDTO(FormDTO $dto): self {
        $this->dto = $dto;

        return $this;
    }

    /**
     * Парсит конфиг из массива.
     *
     * @param array $config
     *
     * @return self
     */
    public function fromConfig(array $config): self
    {
        if (isset($config['create'])) {
            $this->_addCreate($config['create']);
        }

        if (isset($config['update'])) {
            $this->_addUpdate($config['update']);
        }

        if (isset($config['delete'])) {
            $this->addDelete(
                $config['delete']['model_with_soft'] ?? true,
                $config['delete']['already_delete_message'] ?? null,
                $config['delete']['success_message'] ?? null,
                $config['delete']['error_message'] ?? null,
                $config['delete']['config'] ?? []
            );
        }

        if (isset($config['restore'])) {
            $this->addRestore(
                $config['restore']['not_delete_message'] ?? null,
                $config['restore']['success_message'] ?? null,
                $config['restore']['error_message'] ?? null,
                $config['restore']['config'] ?? []
            );
        }

        return $this;
    }

    /**
     * Строит полный CrudlerServiceDTO.
     *
     * @return CrudlerServiceDTO
     */
    public function build(): CrudlerServiceDTO
    {
        return CrudlerServiceDTO::start(
            $this->buildCreate(),
            $this->buildUpdate(),
            $this->buildDelete(),
            $this->buildRestore()
        );
    }

    /**
     * Строит только ServiceCreateDTO.
     *
     * @return ?ServiceCreateDTO
     */
    public function buildCreate(): ?ServiceCreateDTO
    {
        if (empty($this->create)) {
            return null;
        }

        return ServiceCreateDTO::start(
            $this->dto,
            $this->create['mapper']
        );
    }

    /**
     * Строит только ServiceUpdateDTO.
     *
     * @return ?ServiceUpdateDTO
     */
    public function buildUpdate(): ?ServiceUpdateDTO
    {
        if (empty($this->update) || empty($this->data)) {
            return null;
        }

        return ServiceUpdateDTO::start(
            $this->data,
            $this->dto,
            $this->update['mapper']
        );
    }

    /**
     * Строит только ServiceDeleteDTO.
     *
     * @return ?ServiceDeleteDTO
     */
    public function buildDelete(): ?ServiceDeleteDTO
    {
        if (empty($this->delete) || empty($this->data)) {
            return null;
        }

        $config = ExecutionOptionsDTO::make();
        $opts = $this->delete['config_opts'];
        if (isset($opts['getFunc']) && $opts['getFunc']) $config = $config->appendGetFunc();
        if (isset($opts['withTransaction']) && !$opts['withTransaction']) $config = $config->withoutTransaction();
        if (isset($opts['withValidation']) && !$opts['withValidation']) $config = $config->withoutValidation();
        if (isset($opts['writeErrorLog']) && !$opts['writeErrorLog']) $config = $config->withoutErrorLog();

        return ServiceDeleteDTO::start(
            $this->data,
            $this->delete['already_delete_message'],
            $this->delete['success_message'],
            $this->delete['error_message'],
            $config,
            $this->delete['model_with_soft']
        );
    }

    /**
     * Строит только ServiceRestoreDTO.
     *
     * @return ?ServiceRestoreDTO
     */
    public function buildRestore(): ?ServiceRestoreDTO
    {
        if (empty($this->restore) || empty($this->data)) {
            return null;
        }

        $config = ExecutionOptionsDTO::make();
        $opts = $this->restore['config_opts'];
        if (isset($opts['getFunc']) && $opts['getFunc']) $config = $config->appendGetFunc();
        if (isset($opts['withTransaction']) && !$opts['withTransaction']) $config = $config->withoutTransaction();
        if (isset($opts['withValidation']) && !$opts['withValidation']) $config = $config->withoutValidation();
        if (isset($opts['writeErrorLog']) && !$opts['writeErrorLog']) $config = $config->withoutErrorLog();

        return ServiceRestoreDTO::start(
            $this->data,
            $this->restore['not_delete_message'],
            $this->restore['success_message'],
            $this->restore['error_message'],
            $config
        );
    }
}
