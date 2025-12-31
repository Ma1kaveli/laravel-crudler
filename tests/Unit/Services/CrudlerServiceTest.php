<?php

namespace Tests\Unit\Crudler;

use Crudler\Services\CrudlerService;
use Crudler\Services\DTO\CrudlerServiceDTO;
use Crudler\Services\DTO\Parts\ServiceCreateDTO;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class CrudlerServiceTest extends TestCase
{
    public function test_crudler_create_throws_without_dto(): void
    {
        $this->expectException(\Exception::class);

        $service = new CrudlerService(Model::class);
        $service->crudlerCreate(CrudlerServiceDTO::start());
    }

    public function test_crudler_create_calls_internal_create(): void
    {
        $modelMock = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceMock = $this->getMockBuilder(CrudlerService::class)
            ->onlyMethods(['_create'])
            ->setConstructorArgs([Model::class])
            ->getMock();

        $serviceMock->expects($this->once())
            ->method('_create')
            ->willReturn($modelMock); // явный возврат

        $createDTO = $this->getMockBuilder(ServiceCreateDTO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dto = CrudlerServiceDTO::start(createDTO: $createDTO);

        $result = $serviceMock->crudlerCreate($dto);

        $this->assertSame($modelMock, $result);
    }

}
