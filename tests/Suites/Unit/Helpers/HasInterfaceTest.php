<?php

declare(strict_types=1);

namespace Tests\Suites\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Stringable;
use Tests\Resources\HasInterfaceA;
use Tests\Resources\HasInterfaceB;
use Tests\Suites\Unit\AbstractUnitTestCase;

class HasInterfaceTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_returns_if_a_class_has_a_certain_interface(): void
    {
        $class = new class implements HasInterfaceB
        {
        };

        self::assertTrue(has_interface($class, HasInterfaceB::class));
        self::assertTrue(has_interface($class, HasInterfaceA::class));
        self::assertFalse(has_interface($class, Stringable::class));
    }
}
