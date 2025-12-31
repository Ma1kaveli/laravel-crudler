<?php

namespace Crudler\Requests\Core;

use Core\Requests\BaseFormRequest;
use Core\Requests\Context;

class BaseCrudlerFormRequest extends BaseFormRequest
{
    protected array $rulesCommon = [];

    protected array $rulesCreate = [];

    protected array $rulesUpdate = [];

    protected array $rulesDelete = [];

    protected array $messagesCommon = [];

    protected array $messagesCreate = [];

    protected array $messagesUpdate = [];

    protected array $messagesDelete = [];

    protected bool $authorizeFlag = true;

    public function __construct(array $config = [])
    {
        parent::__construct();

        $this->rulesCommon = $config['rules'] ?? [];
        $this->rulesCreate = $config['create_rules'] ?? [];
        $this->rulesUpdate = $config['update_rules'] ?? [];
        $this->rulesDelete = $config['delete_rules'] ?? [];
        $this->messagesCommon = $config['messages'] ?? [];
        $this->messagesCreate = $config['create_messages'] ?? [];
        $this->messagesUpdate = $config['update_messages'] ?? [];
        $this->messagesDelete = $config['delete_messages'] ?? [];
        $this->authorizeFlag = $config['authorize'] ?? true;
    }

    protected function rulesFor(Context $context): array
    {
        $common = $this->rulesCommon;
        return match ($context) {
            Context::CREATE => array_merge($common, $this->rulesCreate),
            Context::UPDATE => array_merge($common, $this->rulesUpdate),
            Context::DELETE => $this->rulesDelete,
            default => [],
        };
    }

    protected function messagesFor(Context $context): array
    {
        $common = $this->messagesCommon;
        return match ($context) {
            Context::CREATE => array_merge($common, $this->messagesCreate),
            Context::UPDATE => array_merge($common, $this->messagesUpdate),
            Context::DELETE => $this->messagesDelete,
            default => [],
        };
    }

    public function authorize(): bool
    {
        return $this->authorizeFlag;
    }
}
