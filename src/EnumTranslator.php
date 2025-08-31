<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils;

use Illuminate\Contracts\Translation\Translator;
use UnitEnum;
use Wimski\LaravelUtils\Contracts\EnumTranslatorInterface;

readonly class EnumTranslator implements EnumTranslatorInterface
{
    public function __construct(
        protected Translator $translator,
    ) {
    }

    public function translate(UnitEnum $enum, bool $plural = false): string
    {
        $enumClass = get_class($enum);

        return $this->translator->choice("enum.{$enumClass}.{$enum->name}", $plural ? 2 : 1);
    }
}
