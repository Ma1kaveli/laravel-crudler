<?php

namespace Tests\Unit\Crudler\Actions\Runners;

use Crudler\Actions\Runners\ActionExecutionRunner;
use Core\DTO\ExecutionOptionsDTO;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;

class ActionExecutionRunnerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::shouldReceive('user')->andReturn(new class implements Authenticatable {
            public function getAuthIdentifierName() { return 'id'; }
            public function getAuthIdentifier() { return 1; }
            public function getAuthPassword() { return ''; }
            public function getRememberToken() { return null; }
            public function setRememberToken($value) {}
            public function getRememberTokenName() { return 'remember_token'; }
        });
    }

    public function testRunReturnsCallableWhenGetFuncIsTrue()
    {
        $runner = new ActionExecutionRunner();
        $action = fn() => 'test';
        $config = ExecutionOptionsDTO::make()->appendGetFunc();

        $result = $runner->run($action, $config, null, null, null);

        $this->assertSame($action, $result);
    }

    public function testRunExecutesActionDirectlyWithoutTransaction()
    {
        $runner = new ActionExecutionRunner();
        $action = fn() => 'done';
        $config = ExecutionOptionsDTO::make()->withoutTransaction();

        $result = $runner->run($action, $config, null, null, null);

        $this->assertSame('done', $result);
    }

    public function testRunWithTransactionExecutesAction()
    {
        $called = false;
        $action = function() use (&$called) {
            $called = true;
            return 'ok';
        };

        $config = ExecutionOptionsDTO::make();

        // Вместо наследования мы можем мокнуть методы трейта через отдельный объект
        $runner = $this->getObjectForTrait(\Crudler\Traits\DBTransaction::class);
        $runner = new ActionExecutionRunner();

        $result = $runner->run($action, $config, 'Error message', null, null);

        $this->assertTrue($called);
        $this->assertSame('ok', $result);
    }
}

