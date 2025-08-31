<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Providers;

use Illuminate\Support\ServiceProvider;
use Wimski\LaravelUtils\Contracts\EnumTranslatorInterface;
use Wimski\LaravelUtils\EnumTranslator;

class LaravelUtilsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EnumTranslatorInterface::class, EnumTranslator::class);
    }
}
