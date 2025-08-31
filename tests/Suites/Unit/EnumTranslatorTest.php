<?php

declare(strict_types=1);

namespace Tests\Suites\Unit;

use Illuminate\Contracts\Translation\Translator;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\Resources\TransEnum;
use Wimski\LaravelUtils\EnumTranslator;

class EnumTranslatorTest extends AbstractUnitTestCase
{
    protected EnumTranslator $enumTranslator;
    protected Translator&MockInterface $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = Mockery::mock(Translator::class);

        $this->enumTranslator = new EnumTranslator(
            $this->translator,
        );
    }

    #[Test]
    public function it_returns_a_singular_enum_translation(): void
    {
        $this->translator
            ->shouldReceive('choice')
            ->once()
            ->with('enum.' . TransEnum::class . '.KEY', 1)
            ->andReturn('singular');

        self::assertSame(
            'singular',
            $this->enumTranslator->translate(TransEnum::KEY),
        );
    }

    #[Test]
    public function it_returns_a_plural_enum_translation(): void
    {
        $this->translator
            ->shouldReceive('choice')
            ->once()
            ->with('enum.' . TransEnum::class . '.KEY', 2)
            ->andReturn('plural');

        self::assertSame(
            'plural',
            $this->enumTranslator->translate(TransEnum::KEY, true),
        );
    }
}
