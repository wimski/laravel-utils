<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use UnexpectedValueException;

class ConfigStringTest extends AbstractIntegrationTestCase
{
    #[Test]
    public function it_returns_a_string(): void
    {
        $this->setConfig('foo', 'bar');

        self::assertSame('bar', config_string('foo'));
    }

    #[Test]
    public function it_returns_a_default_string(): void
    {
        self::assertSame('bar', config_string('foo', 'bar'));
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_key_does_not_exist(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be a string.");

        config_string('foo');
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_value_is_not_a_string(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be a string.");

        $this->setConfig('foo', 123);

        config_string('foo');
    }
}
