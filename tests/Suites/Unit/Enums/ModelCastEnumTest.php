<?php

declare(strict_types=1);

namespace Tests\Suites\Unit\Enums;

use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Unit\AbstractUnitTestCase;
use Wimski\LaravelUtils\Enums\ModelCastEnum;

class ModelCastEnumTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_makes_a_date_cast(): void
    {
        self::assertSame('date', ModelCastEnum::DATE());
        self::assertSame('date:foo', ModelCastEnum::DATE('foo'));
    }

    #[Test]
    public function it_makes_a_datetime_cast(): void
    {
        self::assertSame('datetime', ModelCastEnum::DATETIME());
        self::assertSame('datetime:foo', ModelCastEnum::DATETIME('foo'));
    }

    #[Test]
    public function it_makes_a_decimal_cast(): void
    {
        self::assertSame('decimal:1', ModelCastEnum::DECIMAL(1));
    }

    #[Test]
    public function it_makes_an_immutable_date_cast(): void
    {
        self::assertSame('immutable_date', ModelCastEnum::IMMUTABLE_DATE());
        self::assertSame('immutable_date:foo', ModelCastEnum::IMMUTABLE_DATE('foo'));
    }

    #[Test]
    public function it_makes_an_immutable_datetime_cast(): void
    {
        self::assertSame('immutable_datetime', ModelCastEnum::IMMUTABLE_DATETIME());
        self::assertSame('immutable_datetime:foo', ModelCastEnum::IMMUTABLE_DATETIME('foo'));
    }
}
