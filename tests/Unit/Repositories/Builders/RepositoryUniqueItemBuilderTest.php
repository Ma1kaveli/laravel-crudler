<?php

namespace Tests\Unit\Repositories\Builders;

use Crudler\Repositories\Builders\RepositoryUniqueItemBuilder;
use Exception;
use Tests\TestCase;

class RepositoryUniqueItemBuilderTest extends TestCase
{
    public function test_missing_field_throws(): void
    {
        $this->expectException(Exception::class);

        RepositoryUniqueItemBuilder::make()
            ->column('email')
            ->build();
    }

    public function test_missing_column_throws(): void
    {
        $this->expectException(Exception::class);

        RepositoryUniqueItemBuilder::make()
            ->field('email')
            ->build();
    }
}
