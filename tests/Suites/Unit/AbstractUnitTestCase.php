<?php

declare(strict_types=1);

namespace Wimski\LaravelPackageTemplate\Tests\Suites\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class AbstractUnitTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
}
