<?php

declare(strict_types=1);

namespace Tests\Suites\Unit\Helpers;

use Illuminate\Support\Traits\Dumpable;
use PHPUnit\Framework\Attributes\Test;
use Tests\Resources\HasTraitA;
use Tests\Resources\HasTraitB;
use Tests\Suites\Unit\AbstractUnitTestCase;

class HasTraitTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_returns_if_a_class_has_a_certain_trait(): void
    {
        $class = new class
        {
            use HasTraitB;
        };

        self::assertTrue(has_trait($class, HasTraitB::class));
        self::assertTrue(has_trait($class, HasTraitA::class));
        self::assertFalse(has_trait($class, Dumpable::class));
    }
}
