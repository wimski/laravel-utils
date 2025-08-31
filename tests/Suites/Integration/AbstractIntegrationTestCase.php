<?php

declare(strict_types=1);

namespace Tests\Suites\Integration;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase;
use RuntimeException;
use Tests\Resources\ValidationServiceProvider;

abstract class AbstractIntegrationTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
    use RefreshDatabase;

    protected function getApplication(): Application
    {
        if (! $this->app) {
            throw new RuntimeException('Application not initialized.');
        }

        return $this->app;
    }

    protected function getPackageProviders($app): array
    {
        return [
            ValidationServiceProvider::class,
        ];
    }

    protected function setConfig(string $key, mixed $value): void
    {
        config([$key => $value]);
    }
}
