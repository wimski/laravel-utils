<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use UnexpectedValueException;

class ConfigArrayTest extends AbstractIntegrationTestCase
{
    #[Test]
    public function it_returns_an_array(): void
    {
        $this->setConfig('foo', ['bar']);

        self::assertSame(['bar'], config_array('foo'));
    }

    #[Test]
    public function it_returns_a_default_array(): void
    {
        self::assertSame(['bar'], config_array('foo', ['bar']));
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_key_does_not_exist(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be an array.");

        config_array('foo');
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_value_is_not_an_array(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be an array.");

        $this->setConfig('foo', 'bar');

        config_array('foo');
    }
}
