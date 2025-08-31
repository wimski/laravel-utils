<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use UnexpectedValueException;

class ConfigIntTest extends AbstractIntegrationTestCase
{
    #[Test]
    public function it_returns_an_integer(): void
    {
        $this->setConfig('foo', 123);

        self::assertSame(123, config_int('foo'));
    }

    #[Test]
    public function it_returns_a_default_integer(): void
    {
        self::assertSame(123, config_int('foo', 123));
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_key_does_not_exist(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be an integer.");

        config_int('foo');
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_value_is_not_an_integer(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be an integer.");

        $this->setConfig('foo', 'bar');

        config_int('foo');
    }
}
