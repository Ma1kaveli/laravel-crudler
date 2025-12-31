<?php

namespace Tests\Fakes;

use Core\DTO\FormDTO;
use Illuminate\Contracts\Auth\Authenticatable;

class FakeFormDTO extends FormDTO
{
    public function __construct(
        Authenticatable $user,
        ?int $organizationId = null,
        ?int $roleId = null,
        ?string $id = null
    ) {
        parent::__construct($user, null, null, null);
    }
}
