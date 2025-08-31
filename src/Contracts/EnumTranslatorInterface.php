<?php

namespace Wimski\LaravelUtils\Contracts;

use UnitEnum;

interface EnumTranslatorInterface
{
    public function translate(UnitEnum $enum, bool $plural = false): string;
}
