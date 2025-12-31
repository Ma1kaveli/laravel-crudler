<?php

namespace Crudler\Services\DTO\Parts;

use Core\DTO\ExecutionOptionsDTO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ServiceRestoreDTO {
    public function __construct(
        public readonly string $notDeleteMessage,
        public readonly string $successMessage,
        public readonly string $errorMessage,
        public readonly ExecutionOptionsDTO $config,
        public readonly ?Model $data = null,
    ) {}

    /**
     * Summary of start
     *
     * @param ?Model $data = null
     * @param ?string $notDeleteMessage = null
     * @param ?string $successMessage =null
     * @param ?string $errorMessage = null
     * @param ?ExecutionOptionsDTO $config = null
     *
     * @return static
     */
    public static function start(
        ?Model $data = null,
        ?string $notDeleteMessage = null,
        ?string $successMessage = null,
        ?string $errorMessage = null,
        ?ExecutionOptionsDTO $config = null
    ): static {
        $config ??= ExecutionOptionsDTO::make();
        $notDeleteMessage ??= Config::get('crudler.services.restore.not_delete_message', '');
        $successMessage ??= Config::get('crudler.services.restore.success_message', '');
        $errorMessage ??= Config::get('crudler.services.restore.error_message', '');

        return new self(
            data: $data,
            notDeleteMessage: $notDeleteMessage,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
            config: $config,
        );
    }
}
