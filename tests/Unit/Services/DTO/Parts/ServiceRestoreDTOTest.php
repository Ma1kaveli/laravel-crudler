<?php

namespace Tests\Unit\Services\DTO\Parts;

use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Core\DTO\ExecutionOptionsDTO;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\Fakes\FakeResolver;
use Tests\TestCase;

class ServiceRestoreDTOTest extends TestCase
{
    public function test_start_uses_config_defaults(): void
    {
        Config::set('crudler.services.restore.success_message', 'RESTORED');
        Config::set('crudler.services.restore.error_message', 'ERROR');
        Config::set('crudler.services.restore.not_delete_message', 'NOT_DELETED');
        Config::set('core.form_dto.context_resolver', FakeResolver::class);

        Auth::shouldReceive('user')
            ->andReturn(Mockery::mock(Authenticatable::class));

        $dto = ServiceRestoreDTO::start();

        $this->assertSame('RESTORED', $dto->successMessage);
        $this->assertSame('ERROR', $dto->errorMessage);
        $this->assertSame('NOT_DELETED', $dto->notDeleteMessage);
        $this->assertInstanceOf(ExecutionOptionsDTO::class, $dto->config);
    }
}
