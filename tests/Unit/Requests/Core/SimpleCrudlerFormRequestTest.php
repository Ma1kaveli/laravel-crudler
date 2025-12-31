<?php

namespace Tests\Unit\Requests\Core;

use Crudler\Requests\Core\SimpleCrudlerFormRequest;
use Tests\TestCase;

/**
 * SimpleCrudlerFormRequest:
 * - rules()
 * - messages()
 * - authorize()
 */
class SimpleCrudlerFormRequestTest extends TestCase
{
    public function test_rules_and_messages(): void
    {
        $request = new SimpleCrudlerFormRequest([
            'rules' => ['name' => ['required']],
            'messages' => ['name.required' => 'Required'],
            'authorize' => false,
        ]);

        $this->assertSame(['name' => ['required']], $request->rules());
        $this->assertSame(['name.required' => 'Required'], $request->messages());
        $this->assertFalse($request->authorize());
    }
}
