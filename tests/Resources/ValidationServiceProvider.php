<?php

declare(strict_types=1);

namespace Tests\Resources;

use Tests\Resources\Rules\BasicRule;
use Tests\Resources\Rules\DataRule;
use Tests\Resources\Rules\MessageKeyRule;
use Tests\Resources\Rules\MessageParametersRule;
use Tests\Resources\Rules\MessageRule;
use Tests\Resources\Rules\TranslationErrorRule;
use Wimski\LaravelUtils\Providers\ValidationServiceProvider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    protected array $rules = [
        BasicRule::class,
        DataRule::class,
        MessageRule::class,
        MessageKeyRule::class,
        MessageParametersRule::class,
        TranslationErrorRule::class,
    ];
}
