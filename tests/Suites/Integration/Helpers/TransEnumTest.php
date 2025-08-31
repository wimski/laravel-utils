<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Helpers;

use Illuminate\Translation\Translator;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\ResolvesPaths;
use Tests\Resources\TransEnum;
use Tests\Suites\Integration\AbstractIntegrationTestCase;

class TransEnumTest extends AbstractIntegrationTestCase
{
    use ResolvesPaths;

    #[Test]
    public function it_returns_an_enum_translation(): void
    {
        $translator = $this->getApplication()->make(Translator::class);

        $translator->addPath($this->resolveResourcePath('lang'));
        $translator->getLoader()->load('en', 'enum');

        self::assertSame('enum-singular', trans_enum(TransEnum::KEY));
        self::assertSame('enum-plural', trans_enum(TransEnum::KEY, true));
    }
}
