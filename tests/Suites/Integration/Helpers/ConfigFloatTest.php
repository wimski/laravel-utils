<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use UnexpectedValueException;

class ConfigFloatTest extends AbstractIntegrationTestCase
{
    #[Test]
    public function it_returns_a_float(): void
    {
        $this->setConfig('foo', 1.23);

        self::assertSame(1.23, config_float('foo'));
    }

    #[Test]
    public function it_returns_a_default_float(): void
    {
        self::assertSame(1.23, config_float('foo', 1.23));
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_key_does_not_exist(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be a float.");

        config_float('foo');
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_value_is_not_a_float(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be a float.");

        $this->setConfig('foo', 'bar');

        config_float('foo');
    }
}
