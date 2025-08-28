<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Wimski\LaravelPackageTemplate\Providers\PlaceholderServiceProvider;

Route::get('/' . PlaceholderServiceProvider::PACKAGE, function (): View {
    return view(PlaceholderServiceProvider::PACKAGE . '::test');
});
