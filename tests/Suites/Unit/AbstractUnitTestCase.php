<?php

declare(strict_types=1);

namespace Tests\Suites\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class AbstractUnitTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
}
