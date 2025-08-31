<?php

declare(strict_types=1);

use Wimski\LaravelUtils\Contracts\EnumTranslatorInterface;

if (! function_exists('trans_enum')) { // @codeCoverageIgnore
    function trans_enum(UnitEnum $enum, bool $plural = false): string
    {
        return app(EnumTranslatorInterface::class)->translate($enum, $plural);
    }
}
