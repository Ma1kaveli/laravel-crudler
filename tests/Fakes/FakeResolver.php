<?php

namespace Tests\Fakes;

class FakeResolver {
    public function resolve($user, array $context = []): array {
        return [
            'organization_id' => 1,
            'role_id' => 1,
        ];
    }
}
