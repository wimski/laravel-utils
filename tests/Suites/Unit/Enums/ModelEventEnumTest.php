<?php

declare(strict_types=1);

namespace Tests\Suites\Unit\Enums;

use Illuminate\Foundation\Auth\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Unit\AbstractUnitTestCase;
use Wimski\LaravelUtils\Enums\ModelEventEnum;

class ModelEventEnumTest extends AbstractUnitTestCase
{
    #[Test]
    #[DataProvider('eventDataProvider')]
    public function it_makes_string_versions_of_events(ModelEventEnum $enum): void
    {
        self::assertSame(
            "eloquent.{$enum->value}: Illuminate\Foundation\Auth\User",
            $enum->stringForClass(User::class),
        );
    }

    /**
     * @return array<array-key, array{0: ModelEventEnum}>
     */
    public static function eventDataProvider(): array
    {
        return collect(ModelEventEnum::cases())
            ->map(fn (ModelEventEnum $enum): array => [$enum])
            ->all();
    }
}
