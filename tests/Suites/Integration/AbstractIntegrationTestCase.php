<?php

declare(strict_types=1);

namespace Wimski\LaravelPackageTemplate\Tests\Suites\Integration;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase;

abstract class AbstractIntegrationTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
}
