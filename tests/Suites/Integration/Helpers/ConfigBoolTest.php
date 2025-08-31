<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use UnexpectedValueException;

class ConfigBoolTest extends AbstractIntegrationTestCase
{
    #[Test]
    public function it_returns_a_boolean(): void
    {
        $this->setConfig('foo', true);

        self::assertSame(true, config_bool('foo'));
    }

    #[Test]
    public function it_returns_a_default_boolean(): void
    {
        self::assertSame(true, config_bool('foo', true));
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_key_does_not_exist(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be a boolean.");

        config_bool('foo');
    }

    #[Test]
    public function it_throws_an_exception_if_the_config_value_is_not_a_boolean(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The config value for key 'foo' is expected to be a boolean.");

        $this->setConfig('foo', 'bar');

        config_bool('foo');
    }
}
