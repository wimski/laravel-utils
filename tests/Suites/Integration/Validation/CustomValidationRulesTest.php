<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Validation;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Translation\Translator;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\ResolvesPaths;
use Tests\Concerns\Validates;
use Tests\Resources\Rules\TranslationErrorRule;
use Tests\Resources\ValidationRuleEnum;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use UnexpectedValueException;

class CustomValidationRulesTest extends AbstractIntegrationTestCase
{
    use ResolvesPaths;
    use Validates;

    protected ValidatorFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->getApplication()->make(ValidatorFactory::class);

        $translator = $this->getApplication()->make(Translator::class);

        $translator->addPath($this->resolveResourcePath('lang'));
        $translator->getLoader()->load('en', 'validation');
    }

    #[Test]
    public function it_validates_a_basic_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => true],
            ['foo' => [ValidationRuleEnum::BASIC->getValue()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => false],
            ['foo' => [ValidationRuleEnum::BASIC->getValue()]],
        );

        self::assertSame(
            ['foo' => ['basic error for foo']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_data_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'xxx_bar'],
            ['foo' => [ValidationRuleEnum::DATA->withParams('bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleEnum::DATA->withParams('bar')]],
        );

        self::assertSame(
            ['foo' => ['data error for foo']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_message_rule(): void
    {
        $errors = $this->generateErrors(
            ['foo' => false],
            ['foo' => [ValidationRuleEnum::MESSAGE->getValue()]],
        );

        self::assertSame(
            ['foo' => ['custom error']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_message_key_rule(): void
    {
        $errors = $this->generateErrors(
            ['foo' => false],
            ['foo' => [ValidationRuleEnum::MESSAGE_KEY->getValue()]],
        );

        self::assertSame(
            ['foo' => ['message key error for foo']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_message_parameters_rule(): void
    {
        $errors = $this->generateErrors(
            ['foo' => false],
            ['foo' => [ValidationRuleEnum::MESSAGE_PARAMETERS->getValue()]],
        );

        self::assertSame(
            ['foo' => ['message parameters error for foo with 3']],
            $errors,
        );
    }

    #[Test]
    public function it_throws_an_exception_if_the_translation_is_not_a_string(): void
    {
        self::expectException(UnexpectedValueException::class);
        self::expectExceptionMessage("The translation value for key 'validation.translation_error' is expected to be a string.");

        $translator = Mockery::mock(Translator::class);
        $translator
            ->shouldReceive('get')
            ->once()
            ->with('validation.translation_error', ['attribute' => 'foo'])
            ->andReturn([]);

        $this->generateErrors(
            ['foo' => false],
            ['foo' => [new TranslationErrorRule($translator)]],
        );
    }

    protected function getValidatorFactory(): ValidatorFactory
    {
        return $this->factory;
    }
}
