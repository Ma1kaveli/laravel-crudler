<?php

namespace Tests\Unit\Actions\Builders;

use Crudler\Actions\Builders\ActionBuilder;
use Crudler\Actions\DTO\CrudlerActionDTO;
use Crudler\Repositories\Interfaces\IRepositoryFunction;
use Crudler\Services\Interfaces\IServiceFunction;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ActionBuilderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('crudler.actions.error_create_message', 'ERROR CREATE MESSAGE');
        Config::set('crudler.actions.error_update_message', 'ERROR UPDATE MESSAGE');
        Config::set('crudler.actions.error_delete_message', 'ERROR DELETE MESSAGE');
        Config::set('crudler.actions.error_restore_message', 'ERROR RESTORE MESSAGE');
    }

    public function test_build_without_repository_throws(): void
    {
        $builder = ActionBuilder::make();
        $builder->serviceFunction($this->createMock(IServiceFunction::class));

        $this->expectException(\LogicException::class);
        $builder->build();
    }

    public function test_build_without_service_throws(): void
    {
        $builder = ActionBuilder::make();
        $builder->repositoryFunction($this->createMock(IRepositoryFunction::class));

        $this->expectException(\LogicException::class);
        $builder->build();
    }

    public function test_build_returns_crudler_action_dto(): void
    {
        $repo = $this->createMock(IRepositoryFunction::class);
        $service = $this->createMock(IServiceFunction::class);

        $builder = ActionBuilder::make()
            ->repositoryFunction($repo)
            ->serviceFunction($service);

        $dto = $builder->build();

        $this->assertInstanceOf(CrudlerActionDTO::class, $dto);
        $this->assertSame($repo, $dto->repositoryFunc);
        $this->assertSame($service, $dto->serviceFunc);
    }
}
