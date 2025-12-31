<?php

namespace Tests\Unit\Services\DTO\Parts;

use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Core\DTO\ExecutionOptionsDTO;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\Fakes\FakeResolver;
use Tests\TestCase;

class ServiceDeleteDTOTest extends TestCase
{
    public function test_start_sets_defaults_from_config(): void
    {
        Config::set('crudler.services.delete.success_message', 'OK');
        Config::set('crudler.services.delete.error_message', 'ERROR');
        Config::set('crudler.services.delete.already_delete_message', 'ALREADY');
        Config::set('core.form_dto.context_resolver', FakeResolver::class);

        Auth::shouldReceive('user')
            ->andReturn(Mockery::mock(Authenticatable::class));

        $model = $this->createMock(Model::class);

        $dto = ServiceDeleteDTO::start($model);

        $this->assertSame($model, $dto->data);
        $this->assertSame('OK', $dto->successMessage);
        $this->assertSame('ERROR', $dto->errorMessage);
        $this->assertSame('ALREADY', $dto->alreadyDeleteMessage);
        $this->assertInstanceOf(ExecutionOptionsDTO::class, $dto->config);
    }
}
