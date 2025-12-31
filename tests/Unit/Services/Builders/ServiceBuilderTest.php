<?php

namespace Tests\Unit\Services\Builders;

use Crudler\Mapper\CrudlerMapper;
use Crudler\Services\Builders\ServiceBuilder;
use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Crudler\Services\DTO\Parts\ServiceDeleteDTO;
use Crudler\Services\DTO\Parts\ServiceRestoreDTO;
use Crudler\Services\DTO\Parts\ServiceUpdateDTO;
use Core\DTO\FormDTO;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\Fakes\FakeResolver;
use Tests\TestCase;

class ServiceBuilderTest extends TestCase
{
    private FormDTO $formDTO;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('core.form_dto.context_resolver', FakeResolver::class);

        Auth::shouldReceive('user')
            ->andReturn(Mockery::mock(Authenticatable::class));

        $this->formDTO = $this->createMock(FormDTO::class);
    }

    public function test_build_create_only(): void
    {
        $mapper = CrudlerMapper::make()->field('name');

        $dto = ServiceBuilder::make($this->formDTO)
            ->addCreate($mapper)
            ->build();

        $this->assertInstanceOf(CrudlerServiceDTO::class, $dto);
        $this->assertInstanceOf(ServiceCreateDTO::class, $dto->createDTO);
        $this->assertNull($dto->updateDTO);
        $this->assertNull($dto->deleteDTO);
        $this->assertNull($dto->restoreDTO);
    }

    public function test_update_requires_data(): void
    {
        $mapper = CrudlerMapper::make()->field('name');

        $builder = ServiceBuilder::make($this->formDTO)
            ->addUpdate($mapper);

        $this->assertNull($builder->buildUpdate());
    }

    public function test_update_with_data(): void
    {
        $mapper = CrudlerMapper::make()->field('name');
        $model = $this->createMock(Model::class);

        $dto = ServiceBuilder::make($this->formDTO)
            ->addUpdate($mapper)
            ->setData($model)
            ->buildUpdate();

        $this->assertInstanceOf(ServiceUpdateDTO::class, $dto);
    }

    public function test_delete_without_data_returns_null(): void
    {
        $builder = ServiceBuilder::make($this->formDTO)
            ->addDelete();

        $this->assertNull($builder->buildDelete());
    }

    public function test_delete_with_data(): void
    {
        $model = $this->createMock(Model::class);

        $dto = ServiceBuilder::make($this->formDTO)
            ->addDelete(
                modelWithSoft: true,
                alreadyDeleteMessage: 'already',
                successMessage: 'ok',
                errorMessage: 'fail',
                configOpts: ['withTransaction' => false]
            )
            ->setData($model)
            ->buildDelete();

        $this->assertInstanceOf(ServiceDeleteDTO::class, $dto);
        $this->assertFalse($dto->config->withTransaction);
    }

    public function test_restore_from_config(): void
    {
        $model = $this->createMock(Model::class);

        $config = [
            'restore' => [
                'not_delete_message' => 'not deleted',
                'success_message' => 'restored',
                'error_message' => 'error',
                'config' => ['writeErrorLog' => false],
            ],
        ];

        $dto = ServiceBuilder::make($this->formDTO)
            ->fromConfig($config)
            ->setData($model)
            ->buildRestore();

        $this->assertInstanceOf(ServiceRestoreDTO::class, $dto);
        $this->assertFalse($dto->config->writeErrorLog);
    }
}
