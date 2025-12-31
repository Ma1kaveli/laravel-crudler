<?php

namespace Crudler\Services\DTO\Parts;

use Core\DTO\ExecutionOptionsDTO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ServiceDeleteDTO {
    public function __construct(
        public readonly string $alreadyDeleteMessage,
        public readonly string $successMessage,
        public readonly string $errorMessage,
        public readonly ExecutionOptionsDTO $config,
        public readonly ?Model $data = null,
        public readonly bool $modelWithSoft = true,
    ) {}

    /**
     * Summary of start
     *
     * @param ?Model $data = null
     * @param ?string $alreadyDeleteMessage = null
     * @param ?string $successMessage =null
     * @param ?string $errorMessage = null
     * @param ?ExecutionOptionsDTO $config = null
     * @param bool $modelWithSoft = true
     *
     * @return static
     */
    public static function start(
        ?Model $data = null,
        ?string $alreadyDeleteMessage = null,
        ?string $successMessage = null,
        ?string $errorMessage = null,
        ?ExecutionOptionsDTO $config = null,
        bool $modelWithSoft = true,
    ): static {
        $config ??= ExecutionOptionsDTO::make();
        $alreadyDeleteMessage ??= Config::get('crudler.services.delete.already_delete_message', '');
        $successMessage ??= Config::get('crudler.services.delete.success_message', '');
        $errorMessage ??= Config::get('crudler.services.delete.error_message', '');

        return new self(
            data: $data,
            alreadyDeleteMessage: $alreadyDeleteMessage,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
            config: $config,
            modelWithSoft: $modelWithSoft,
        );
    }
}
