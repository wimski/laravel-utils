<?php

declare(strict_types=1);

namespace Tests\Suites\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Resources\HasParentA;
use Tests\Resources\HasParentB;
use Tests\Suites\Unit\AbstractUnitTestCase;

class HasParentTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_returns_if_a_class_has_a_certain_parent(): void
    {
        $class = new class extends HasParentB
        {
        };

        self::assertTrue(has_parent($class, HasParentB::class));
        self::assertTrue(has_parent($class, HasParentA::class));
        self::assertFalse(has_parent($class, self::class));
    }
}
