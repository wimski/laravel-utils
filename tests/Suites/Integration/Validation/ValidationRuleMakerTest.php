<?php

declare(strict_types=1);

namespace Tests\Suites\Integration\Validation;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Factories\UserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\Validates;
use Tests\Resources\TestBackedEnum;
use Tests\Suites\Integration\AbstractIntegrationTestCase;
use Wimski\LaravelUtils\Validation\Dimensions;
use Wimski\LaravelUtils\Validation\ValidationRuleMaker;

#[WithMigration]
class ValidationRuleMakerTest extends AbstractIntegrationTestCase
{
    use Validates;

    protected const string TIMESTAMP = '2025-04-03 02:01:00';

    protected ValidatorFactory $factory;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(self::TIMESTAMP);

        $this->factory = $this->getApplication()
                              ->make(ValidatorFactory::class);
    }

    #[Test]
    public function it_validates_an_accepted_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'yes'],
            ['foo' => [ValidationRuleMaker::accepted()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::accepted()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be accepted.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_accepted_if_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'yes', 'bar' => 'x'],
            ['foo' => [ValidationRuleMaker::acceptedIf(
                'bar',
                'x',
            )]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null, 'bar' => 'x'],
            ['foo' => [ValidationRuleMaker::acceptedIf(
                'bar',
                'x',
            )]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be accepted when bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_active_url_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'https://google.com'],
            ['foo' => [ValidationRuleMaker::activeUrl()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::activeUrl()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid URL.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('afterDataProvider')]
    public function it_validates_an_after_rule(DateTimeInterface|string $date, string $value): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-05-03 02:01:00'],
            ['foo' => [ValidationRuleMaker::after($date)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '2025-04-02 02:01:00'],
            ['foo' => [ValidationRuleMaker::after($date)]],
        );

        self::assertSame(
            ['foo' => ["The foo field must be a date after {$value}."]],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('afterDataProvider')]
    public function it_validates_an_after_or_equal_rule(DateTimeInterface|string $date, string $value): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-05-03 02:01:00'],
            ['foo' => [ValidationRuleMaker::afterOrEqual($date)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '2025-04-02 02:01:00'],
            ['foo' => [ValidationRuleMaker::afterOrEqual($date)]],
        );

        self::assertSame(
            ['foo' => ["The foo field must be a date after or equal to {$value}."]],
            $errors,
        );
    }

    /**
     * @return array<array-key, array{0: Carbon|string, 1: string}>
     */
    public static function afterDataProvider(): array
    {
        return [
            ['tomorrow', 'tomorrow'],
            [new Carbon(self::TIMESTAMP)->addDay(), '2025-04-04'],
        ];
    }

    #[Test]
    #[DataProvider('alphaDataProvider')]
    public function it_validates_an_alpha_rule(string $value, bool $strict): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'a'],
            ['foo' => [ValidationRuleMaker::alpha($strict)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => $value],
            ['foo' => [ValidationRuleMaker::alpha($strict)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must only contain letters.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('alphaDataProvider')]
    public function it_validates_an_alpha_dash_rule(string $value, bool $strict): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '-'],
            ['foo' => [ValidationRuleMaker::alphaDash($strict)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => $value],
            ['foo' => [ValidationRuleMaker::alphaDash($strict)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must only contain letters, numbers, dashes, and underscores.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('alphaDataProvider')]
    public function it_validates_an_alpha_numeric_rule(string $value, bool $strict): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '0'],
            ['foo' => [ValidationRuleMaker::alphaNumeric($strict)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => $value],
            ['foo' => [ValidationRuleMaker::alphaNumeric($strict)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must only contain letters and numbers.']],
            $errors,
        );
    }

    /**
     * @return array<array-key, array{0: string, 1: bool}>
     */
    public static function alphaDataProvider(): array
    {
        return [
            ['%', false],
            ['é', true],
        ];
    }

    #[Test]
    public function it_validates_an_any_of_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::anyOf(['string'], ['numeric'])]],
        ));

        $errors = $this->generateErrors(
            ['foo' => true],
            ['foo' => [ValidationRuleMaker::anyOf(['string'], ['numeric'])]],
        );

        self::assertSame(
            ['foo' => ['The foo field is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_array_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => []],
            ['foo' => [ValidationRuleMaker::array()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => true],
            ['foo' => [ValidationRuleMaker::array()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be an array.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_array_rule_with_keys(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => ['bar' => 'y']],
            ['foo' => [ValidationRuleMaker::array('bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['x' => 'y']],
            ['foo' => [ValidationRuleMaker::array('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be an array.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_ascii_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'a'],
            ['foo' => [ValidationRuleMaker::ascii()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'é'],
            ['foo' => [ValidationRuleMaker::ascii()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must only contain single-byte alphanumeric characters and symbols.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_bail_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '1234567890'],
            ['foo' => [ValidationRuleMaker::bail(), 'string', 'min:10']],
        ));

        $errors = $this->generateErrors(
            ['foo' => '1'],
            ['foo' => [ValidationRuleMaker::bail(), 'string', 'min:10']],
        );

        self::assertSame(
            ['foo' => ['The foo field must be at least 10 characters.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => [ValidationRuleMaker::bail(), 'string', 'min:10']],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a string.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('beforeDataProvider')]
    public function it_validates_an_before_rule(DateTimeInterface|string $date, string $value): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-02-03 02:01:00'],
            ['foo' => [ValidationRuleMaker::before($date)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '2025-04-04 02:01:00'],
            ['foo' => [ValidationRuleMaker::before($date)]],
        );

        self::assertSame(
            ['foo' => ["The foo field must be a date before {$value}."]],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('beforeDataProvider')]
    public function it_validates_an_before_or_equal_rule(DateTimeInterface|string $date, string $value): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-02-03 02:01:00'],
            ['foo' => [ValidationRuleMaker::beforeOrEqual($date)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '2025-04-04 02:01:00'],
            ['foo' => [ValidationRuleMaker::beforeOrEqual($date)]],
        );

        self::assertSame(
            ['foo' => ["The foo field must be a date before or equal to {$value}."]],
            $errors,
        );
    }

    /**
     * @return array<array-key, array{0: Carbon|string, 1: string}>
     */
    public static function beforeDataProvider(): array
    {
        return [
            ['yesterday', 'yesterday'],
            [new Carbon(self::TIMESTAMP)->subDay(), '2025-04-02'],
        ];
    }

    #[Test]
    public function it_validates_a_between_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => ['string', ValidationRuleMaker::between(2, 4)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => ['string', ValidationRuleMaker::between(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2 and 4 characters.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => ['string', ValidationRuleMaker::between(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2 and 4 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_between_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 3],
            ['foo' => ['integer', ValidationRuleMaker::between(2, 4)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => ['integer', ValidationRuleMaker::between(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2 and 4.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 5],
            ['foo' => ['integer', ValidationRuleMaker::between(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2 and 4.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_between_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 3.3],
            ['foo' => ['numeric', ValidationRuleMaker::between(2.2, 4.4)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1.1],
            ['foo' => ['numeric', ValidationRuleMaker::between(2.2, 4.4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2.2 and 4.4.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 5.5],
            ['foo' => ['numeric', ValidationRuleMaker::between(2.2, 4.4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2.2 and 4.4.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_between_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [1,2,3]],
            ['foo' => ['array', ValidationRuleMaker::between(2, 4)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [1]],
            ['foo' => ['array', ValidationRuleMaker::between(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have between 2 and 4 items.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => [1,2,3,4,5]],
            ['foo' => ['array', ValidationRuleMaker::between(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have between 2 and 4 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_between_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 300)],
            ['foo' => ['file', ValidationRuleMaker::between(200, 400)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 100)],
            ['foo' => ['file', ValidationRuleMaker::between(200, 400)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 200 and 400 kilobytes.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 500)],
            ['foo' => ['file', ValidationRuleMaker::between(200, 400)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 200 and 400 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('booleanDataProvider')]
    public function it_validates_a_boolean_rule(bool|int $valid, ?int $invalid, bool $strict): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => $valid],
            ['foo' => [ValidationRuleMaker::boolean($strict)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => $invalid],
            ['foo' => [ValidationRuleMaker::boolean($strict)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be true or false.']],
            $errors,
        );
    }

    /**
     * @return array<array-key, array{0: bool|int, 1: ?int, 2: bool}>
     */
    public static function booleanDataProvider(): array
    {
        return [
            [1, null, false],
            [true, 1, true],
        ];
    }

    #[Test]
    public function it_validates_a_confirmed_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo'              => ['bar'],
                'foo_confirmation' => ['bar'],
            ],
            ['foo' => [ValidationRuleMaker::confirmed()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['bar']],
            ['foo' => [ValidationRuleMaker::confirmed()]],
        );

        self::assertSame(
            ['foo' => ['The foo field confirmation does not match.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo'              => ['bar'],
                'foo_confirmation' => ['x'],
            ],
            ['foo' => [ValidationRuleMaker::confirmed()]],
        );

        self::assertSame(
            ['foo' => ['The foo field confirmation does not match.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_confirmed_rule_with_custom_field_name(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo'    => ['bar'],
                'lipsum' => ['bar'],
            ],
            ['foo' => [ValidationRuleMaker::confirmed('lipsum')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['bar']],
            ['foo' => [ValidationRuleMaker::confirmed('lipsum')]],
        );

        self::assertSame(
            ['foo' => ['The foo field confirmation does not match.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo'    => ['bar'],
                'lipsum' => ['x'],
            ],
            ['foo' => [ValidationRuleMaker::confirmed('lipsum')]],
        );

        self::assertSame(
            ['foo' => ['The foo field confirmation does not match.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_contains_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => ['bar']],
            ['foo' => [ValidationRuleMaker::contains('bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => []],
            ['foo' => [ValidationRuleMaker::contains('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is missing a required value.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('currentPasswordDataProvider')]
    public function it_validates_a_current_password_rule(?string $guard): void
    {
        $this->addGuard($guard);

        /** @var User $user */
        $user = new UserFactory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user, $guard);

        self::assertEmpty($this->generateErrors(
            ['foo' => 'password'],
            ['foo' => [ValidationRuleMaker::currentPassword($guard)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::currentPassword($guard)]],
        );

        self::assertSame(
            ['foo' => ['The password is incorrect.']],
            $errors,
        );
    }

    /**
     * @return array<array-key, array{0: ?string}>
     */
    public static function currentPasswordDataProvider(): array
    {
        return [
            [null],
            ['lipsum'],
        ];
    }

    #[Test]
    public function it_validates_a_date_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-04-03'],
            ['foo' => [ValidationRuleMaker::date()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::date()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid date.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('dateEqualsDataProvider')]
    public function it_validates_a_date_equals_rule(
        DateTimeInterface|string $date,
        string $value,
        string $valid,
        string $invalid,
    ): void {
        self::assertEmpty($this->generateErrors(
            ['foo' => $valid],
            ['foo' => [ValidationRuleMaker::dateEquals($date)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => $invalid],
            ['foo' => [ValidationRuleMaker::dateEquals($date)]],
        );

        self::assertSame(
            ['foo' => ["The foo field must be a date equal to {$value}."]],
            $errors,
        );
    }

    /**
     * @return array<array-key, array{
     *     0: Carbon|string,
     *     1: string,
     *     2: string,
     *     3: string,
     * }>
     */
    public static function dateEqualsDataProvider(): array
    {
        return [
            ['yesterday', 'yesterday', '2025-04-02', '2025-04-03'],
            ['2025-04-03', '2025-04-03', '2025-04-03', '2024-03-02'],
            [new Carbon(self::TIMESTAMP), '2025-04-03', '2025-04-03', '2024-03-02'],
        ];
    }

    #[Test]
    public function it_validates_a_date_fluent_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-04-04'],
            ['foo' => [ValidationRuleMaker::dateFluent()->afterToday()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '2025-04-03'],
            ['foo' => [ValidationRuleMaker::dateFluent()->afterToday()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a date after today.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_date_format_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2025-04-04'],
            ['foo' => [ValidationRuleMaker::dateFormat('Y-m-d')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '03-04-2025'],
            ['foo' => [ValidationRuleMaker::dateFormat('Y-m-d')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must match the format Y-m-d.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_decimal_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 1.23],
            ['foo' => [ValidationRuleMaker::decimal(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1.2],
            ['foo' => [ValidationRuleMaker::decimal(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have 2 decimal places.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_decimal_rule_with_max(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 1.234],
            ['foo' => [ValidationRuleMaker::decimal(2, 4)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1.2],
            ['foo' => [ValidationRuleMaker::decimal(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have 2-4 decimal places.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 1.23456],
            ['foo' => [ValidationRuleMaker::decimal(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have 2-4 decimal places.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_declined_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'no'],
            ['foo' => [ValidationRuleMaker::declined()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::declined()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be declined.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_declined_if_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'no', 'bar' => 'x'],
            ['foo' => [ValidationRuleMaker::declinedIf(
                'bar',
                'x',
            )]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null, 'bar' => 'x'],
            ['foo' => [ValidationRuleMaker::declinedIf(
                'bar',
                'x',
            )]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be declined when bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_different_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::different('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'x',
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::different('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field and bar must be different.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_digits_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 12],
            ['foo' => [ValidationRuleMaker::digits(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => [ValidationRuleMaker::digits(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be 2 digits.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 123],
            ['foo' => [ValidationRuleMaker::digits(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be 2 digits.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_digits_between_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 123],
            ['foo' => [ValidationRuleMaker::digitsBetween(2, 4)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => [ValidationRuleMaker::digitsBetween(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2 and 4 digits.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => 12345],
            ['foo' => [ValidationRuleMaker::digitsBetween(2, 4)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be between 2 and 4 digits.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_dimensions_rule(): void
    {
        $dimensions = new Dimensions()
            ->minWidth(100)
            ->minHeight(100)
            ->ratio(16 / 9);

        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.jpg', 320, 180)],
            ['foo' => [ValidationRuleMaker::dimensions($dimensions)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.jpg', 40, 30)],
            ['foo' => [ValidationRuleMaker::dimensions($dimensions)]],
        );

        self::assertSame(
            ['foo' => ['The foo field has invalid image dimensions.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_distinct_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [['bar' => 'x'], ['bar' => 'y']]],
            ['foo.*.bar' => [ValidationRuleMaker::distinct()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [['bar' => 'x'], ['bar' => 'x']]],
            ['foo.*.bar' => [ValidationRuleMaker::distinct()]],
        );

        self::assertSame(
            [
                'foo.0.bar' => ['The foo.0.bar field has a duplicate value.'],
                'foo.1.bar' => ['The foo.1.bar field has a duplicate value.'],
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_distinct_rule_with_strict(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [['bar' => '1'], ['bar' => 1]]],
            ['foo.*.bar' => [ValidationRuleMaker::distinct(true)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [['bar' => '1'], ['bar' => '1']]],
            ['foo.*.bar' => [ValidationRuleMaker::distinct(true)]],
        );

        self::assertSame(
            [
                'foo.0.bar' => ['The foo.0.bar field has a duplicate value.'],
                'foo.1.bar' => ['The foo.1.bar field has a duplicate value.'],
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_distinct_rule_with_ignore_case(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [['bar' => 'x'], ['bar' => 'y']]],
            ['foo.*.bar' => [ValidationRuleMaker::distinct(false, true)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [['bar' => 'x'], ['bar' => 'X']]],
            ['foo.*.bar' => [ValidationRuleMaker::distinct(false, true)]],
        );

        self::assertSame(
            [
                'foo.0.bar' => ['The foo.0.bar field has a duplicate value.'],
                'foo.1.bar' => ['The foo.1.bar field has a duplicate value.'],
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_doesnt_contain_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => []],
            ['foo' => [ValidationRuleMaker::doesntContain('bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['bar']],
            ['foo' => [ValidationRuleMaker::doesntContain('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not contain any of the following: bar.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_doesnt_end_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::doesntEndWith('lip', 'bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::doesntEndWith('sum', 'bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not end with one of the following: sum, bar.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_doesnt_start_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::doesntStartWith('sum', 'bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::doesntStartWith('lip', 'bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not start with one of the following: lip, bar.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_email_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'john.doe@example.com'],
            ['foo' => [ValidationRuleMaker::email()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::email()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid email address.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_ends_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::endsWith('sum', 'bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::endsWith('lip', 'bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must end with one of the following: lip, bar.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_enum_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'foo'],
            ['foo' => [ValidationRuleMaker::enum(TestBackedEnum::class)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::enum(TestBackedEnum::class)]],
        );

        self::assertSame(
            ['foo' => ['The selected foo is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_exclude_rule(): void
    {
        self::assertEmpty($this->getValidated(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::exclude()]],
        ));
    }

    #[Test]
    public function it_validates_an_exclude_if_rule(): void
    {
        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeIf('bar', 'z')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            $data,
        );

        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'z',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeIf('bar', 'z')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            ['bar' => 'z'],
            $data,
        );
    }

    #[Test]
    public function it_validates_an_exclude_if_logic_rule(): void
    {
        self::assertEmpty($this->getValidated(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::excludeIfLogic(true)]],
        ));

        self::assertNotEmpty($this->getValidated(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::excludeIfLogic(false)]],
        ));

        self::assertEmpty($this->getValidated(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::excludeIfLogic(fn (): bool => true)]],
        ));

        self::assertNotEmpty($this->getValidated(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::excludeIfLogic(fn (): bool => false)]],
        ));
    }

    #[Test]
    public function it_validates_an_exclude_unless_rule(): void
    {
        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeUnless('bar', 'y')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            $data,
        );

        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'z',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeUnless('bar', 'y')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            ['bar' => 'z'],
            $data,
        );
    }

    #[Test]
    public function it_validates_an_exclude_with_rule(): void
    {
        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeWith('lipsum')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            $data,
        );

        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeWith('bar')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            ['bar' => 'y'],
            $data,
        );
    }

    #[Test]
    public function it_validates_an_exclude_without_rule(): void
    {
        self::assertEmpty($this->getValidated(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::excludeWithout('bar')]],
        ));

        $data = $this->getValidated(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            [
                'foo' => [ValidationRuleMaker::excludeWithout('bar')],
                'bar' => ['required'],
            ],
        );

        self::assertSame(
            [
                'foo' => 'x',
                'bar' => 'y',
            ],
            $data,
        );
    }

    #[Test]
    public function it_validates_an_exists_rule(): void
    {
        new UserFactory()->create(['email' => 'johndoe@example.com']);

        self::assertEmpty($this->generateErrors(
            ['foo' => 'johndoe@example.com'],
            ['foo' => [ValidationRuleMaker::exists('users', 'email')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'janesmith@example.com'],
            ['foo' => [ValidationRuleMaker::exists('users', 'email')]],
        );

        self::assertSame(
            ['foo' => ['The selected foo is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_extensions_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.jpg')],
            ['foo' => [ValidationRuleMaker::extensions('jpg', 'png')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.webp')],
            ['foo' => [ValidationRuleMaker::extensions('jpg', 'png')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have one of the following extensions: jpg, png.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_file_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->create('bar.pdf')],
            ['foo' => [ValidationRuleMaker::file()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::file()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a file.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_filled_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::filled()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::filled()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have a value.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'xxx',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::greaterThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'x',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::greaterThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than 2 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 3,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::greaterThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 1,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::greaterThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 3.3,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::greaterThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 1.1,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::greaterThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than 2.2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => [1,2,3],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::greaterThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => [1],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::greaterThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have more than 2 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 300),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::greaterThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 100),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::greaterThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than 200 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_or_equal_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'xxx',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'xx',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'x',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than or equal to 2 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_or_equal_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 3,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 2,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 1,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than or equal to 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_or_equal_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 3.3,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 3.2,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 1.1,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than or equal to 2.2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_or_equal_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => [1,2,3],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => [1,2],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => [1],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have 2 items or more.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_greater_than_or_equal_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 300),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 200),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 100),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::greaterThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be greater than or equal to 200 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_hex_color_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '#123456'],
            ['foo' => [ValidationRuleMaker::hexColor()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::hexColor()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid hexadecimal color.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_ip_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '127.0.0.1'],
            ['foo' => [ValidationRuleMaker::ip()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::ip()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid IP address.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_ip_v4_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '127.0.0.1'],
            ['foo' => [ValidationRuleMaker::ipV4()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::ipV4()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid IPv4 address.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_ip_v6_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '2001:DB8:130F:0:0:9C0:876A:130B'],
            ['foo' => [ValidationRuleMaker::ipV6()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '127.0.0.1'],
            ['foo' => [ValidationRuleMaker::ipV6()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid IPv6 address.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_image_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.jpg')],
            ['foo' => [ValidationRuleMaker::image()]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.svg')],
            ['foo' => [ValidationRuleMaker::image(true)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('bar.pdf')],
            ['foo' => [ValidationRuleMaker::image()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be an image.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('bar.svg')],
            ['foo' => [ValidationRuleMaker::image()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be an image.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_in_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::in('bar', 'acme')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::in('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The selected foo is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_in_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => ['bar']],
            ['foo' => ['array', ValidationRuleMaker::in('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => ['bar','acme']],
            ['foo' => ['array', ValidationRuleMaker::in('bar', 'acme')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['bar','lipsum']],
            ['foo' => ['array', ValidationRuleMaker::in('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The selected foo is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_in_array_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'sum',
                'bar' => ['lip' => 'sum'],
            ],
            ['foo' => [ValidationRuleMaker::inArray('bar.lip')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'sum',
                'bar' => ['x' => 'sum'],
            ],
            ['foo' => [ValidationRuleMaker::inArray('bar.lip')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must exist in bar.lip.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_in_array_keys_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => ['bar' => 'x']],
            ['foo' => [ValidationRuleMaker::inArrayKeys('bar', 'lipsum')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['x' => 'y']],
            ['foo' => [ValidationRuleMaker::inArrayKeys('bar', 'lipsum')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must contain at least one of the following keys: bar, lipsum.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_integer_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '1'],
            ['foo' => [ValidationRuleMaker::integer()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::integer()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be an integer.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_integer_rule_with_strict(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 1],
            ['foo' => [ValidationRuleMaker::integer(true)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '1'],
            ['foo' => [ValidationRuleMaker::integer(true)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be an integer.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_json_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '{"bar":"lipsum"}'],
            ['foo' => [ValidationRuleMaker::json()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::json()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid JSON string.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'x',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::lessThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'xxx',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::lessThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than 2 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 1,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::lessThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 3,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::lessThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 1.1,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::lessThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 3.3,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::lessThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than 2.2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => [1],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::lessThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => [1,2,3],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::lessThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have less than 2 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 100),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::lessThan('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 300),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::lessThan('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than 200 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_or_equal_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'x',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'xx',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'xxx',
                'bar' => 'yy',
            ],
            ['foo' => ['string', ValidationRuleMaker::lessThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than or equal to 2 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_or_equal_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 1,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 2,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 3,
                'bar' => 2,
            ],
            ['foo' => ['integer', ValidationRuleMaker::lessThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than or equal to 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_or_equal_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 1.1,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 2.2,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 3.3,
                'bar' => 2.2,
            ],
            ['foo' => ['numeric', ValidationRuleMaker::lessThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than or equal to 2.2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_or_equal_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => [1],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => [1,2],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => [1,2,3],
                'bar' => [1,2],
            ],
            ['foo' => ['array', ValidationRuleMaker::lessThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not have more than 2 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_less_than_or_equal_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 100),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 200),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::lessThanOrEqual('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => UploadedFile::fake()->create('file.txt', 300),
                'bar' => UploadedFile::fake()->create('file.txt', 200),
            ],
            ['foo' => ['file', ValidationRuleMaker::lessThanOrEqual('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be less than or equal to 200 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_list_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [0 => 'x', 1 => 'y']],
            ['foo' => [ValidationRuleMaker::list()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [1 => 'y']],
            ['foo' => [ValidationRuleMaker::list()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a list.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_lowercase_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'xxx'],
            ['foo' => [ValidationRuleMaker::lowercase()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'xXx'],
            ['foo' => [ValidationRuleMaker::lowercase()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be lowercase.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_mac_address_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '00:1A:2B:3C:4D:5E'],
            ['foo' => [ValidationRuleMaker::macAddress()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => [ValidationRuleMaker::macAddress()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid MAC address.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_mime_types_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.jpg')->mimeType('image/jpg')],
            ['foo' => [ValidationRuleMaker::mimeTypes('image/jpg', 'image/png')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.webp')->mimeType('image/webp')],
            ['foo' => [ValidationRuleMaker::mimeTypes('image/jpg', 'image/png')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a file of type: image/jpg, image/png.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_mimes_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.jpg')],
            ['foo' => [ValidationRuleMaker::mimes('jpg', 'png')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->image('bar.webp')],
            ['foo' => [ValidationRuleMaker::mimes('jpg', 'png')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a file of type: jpg, png.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_max_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'x'],
            ['foo' => ['string', ValidationRuleMaker::max(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'xxx'],
            ['foo' => ['string', ValidationRuleMaker::max(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not be greater than 2 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_max_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 1],
            ['foo' => ['integer', ValidationRuleMaker::max(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 3],
            ['foo' => ['integer', ValidationRuleMaker::max(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not be greater than 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_max_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 1.1],
            ['foo' => ['numeric', ValidationRuleMaker::max(2.2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 3.3],
            ['foo' => ['numeric', ValidationRuleMaker::max(2.2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not be greater than 2.2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_max_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [1]],
            ['foo' => ['array', ValidationRuleMaker::max(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [1,2,3]],
            ['foo' => ['array', ValidationRuleMaker::max(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not have more than 2 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_max_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 100)],
            ['foo' => ['file', ValidationRuleMaker::max(200)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 300)],
            ['foo' => ['file', ValidationRuleMaker::max(200)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not be greater than 200 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_max_digits_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 1],
            ['foo' => [ValidationRuleMaker::maxDigits(2)]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => 12],
            ['foo' => [ValidationRuleMaker::maxDigits(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 123],
            ['foo' => [ValidationRuleMaker::maxDigits(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must not have more than 2 digits.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_min_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'xxx'],
            ['foo' => ['string', ValidationRuleMaker::min(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => ['string', ValidationRuleMaker::min(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be at least 2 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_min_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 3],
            ['foo' => ['integer', ValidationRuleMaker::min(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => ['integer', ValidationRuleMaker::min(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be at least 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_min_rule_as_float(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 3.3],
            ['foo' => ['numeric', ValidationRuleMaker::min(2.2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1.1],
            ['foo' => ['numeric', ValidationRuleMaker::min(2.2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be at least 2.2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_min_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [1,2,3]],
            ['foo' => ['array', ValidationRuleMaker::min(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [1]],
            ['foo' => ['array', ValidationRuleMaker::min(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have at least 2 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_min_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 300)],
            ['foo' => ['file', ValidationRuleMaker::min(200)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 100)],
            ['foo' => ['file', ValidationRuleMaker::min(200)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be at least 200 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_min_digits_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 12],
            ['foo' => [ValidationRuleMaker::minDigits(2)]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => 123],
            ['foo' => [ValidationRuleMaker::minDigits(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => [ValidationRuleMaker::minDigits(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must have at least 2 digits.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_missing_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::missing()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::missing()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be missing.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_missing_if_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::missingIf('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::missingIf('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::missingIf('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be missing when bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_missing_unless_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'y'],
            ['foo' => [ValidationRuleMaker::missingUnless('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::missingUnless('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::missingUnless('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be missing unless bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_missing_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::missingWith('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::missingWith('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::missingWith('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be missing when bar is present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_missing_with_all_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::missingWithAll('bar', 'lipsum')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::missingWithAll('bar', 'lipsum')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo'    => null,
                'lipsum' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::missingWithAll('bar', 'lipsum')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo'    => null,
                'bar'    => 'x',
                'lipsum' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::missingWithAll('bar', 'lipsum')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be missing when bar / lipsum are present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_multiple_of_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 4],
            ['foo' => [ValidationRuleMaker::multipleOf(2)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 3],
            ['foo' => [ValidationRuleMaker::multipleOf(2)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a multiple of 2.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_not_in_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::notIn('bar', 'acme')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::notIn('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The selected foo is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_not_regular_expression_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'Bar1'],
            ['foo' => [ValidationRuleMaker::notRegularExpression('/^[a-z]+$/')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::notRegularExpression('/^[a-z]+$/')]],
        );

        self::assertSame(
            ['foo' => ['The foo field format is invalid.']],
            $errors,
        );
    }

    #[Test]
    #[DataProvider('nullableDataProvider')]
    public function it_validates_a_nullable_rule(mixed $value): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => $value],
            ['foo' => [ValidationRuleMaker::nullable()]],
        ));
    }

    /**
     * @return array<array-key, array{0: mixed}>
     */
    public static function nullableDataProvider(): array
    {
        return [
            [null],
            ['bar'],
            [123],
            [1.23],
            [true],
            [false],
        ];
    }

    #[Test]
    public function it_validates_a_numeric_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => '123'],
            ['foo' => [ValidationRuleMaker::numeric()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => true],
            ['foo' => [ValidationRuleMaker::numeric()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a number.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_numeric_rule_with_strict(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 123],
            ['foo' => [ValidationRuleMaker::numeric(true)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => '123'],
            ['foo' => [ValidationRuleMaker::numeric(true)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a number.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_present_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::present()]],
        ));

        $errors = $this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::present()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_present_if_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::presentIf('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'y'],
            ['foo' => [ValidationRuleMaker::presentIf('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::presentIf('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be present when bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_present_unless_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::presentUnless('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::presentUnless('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => 'y'],
            ['foo' => [ValidationRuleMaker::presentUnless('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be present unless bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_present_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::presentWith('bar')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::presentWith('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be present when bar is present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_present_with_all_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::presentWithAll('bar', 'lipsum')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['lipsum' => 'y'],
            ['foo' => [ValidationRuleMaker::presentWithAll('bar', 'lipsum')]],
        ));

        $errors = $this->generateErrors(
            [
                'bar'    => 'x',
                'lipsum' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::presentWithAll('bar', 'lipsum')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be present when bar / lipsum are present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibited_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::prohibited()]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::prohibited()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::prohibited()]],
        );

        self::assertSame(
            ['foo' => ['The foo field is prohibited.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibited_if_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::prohibitedIf('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::prohibitedIf('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIf('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIf('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is prohibited when bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibited_if_logic_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::prohibitedIfLogic(fn (): bool => true)]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::prohibitedIfLogic(fn (): bool => true)]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::prohibitedIfLogic(fn (): bool => false)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::prohibitedIfLogic(fn (): bool => true)]],
        );

        self::assertSame(
            ['foo' => ['The foo field is prohibited.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibited_if_accepted_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => true],
            ['foo' => [ValidationRuleMaker::prohibitedIfAccepted('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => false,
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIfAccepted('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => true,
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIfAccepted('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => true,
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIfAccepted('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is prohibited when bar is accepted.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibited_if_declined_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => false],
            ['foo' => [ValidationRuleMaker::prohibitedIfDeclined('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => true,
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIfDeclined('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => false,
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIfDeclined('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => false,
            ],
            ['foo' => [ValidationRuleMaker::prohibitedIfDeclined('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is prohibited when bar is declined.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibited_unless_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'y'],
            ['foo' => [ValidationRuleMaker::prohibitedUnless('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::prohibitedUnless('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::prohibitedUnless('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::prohibitedUnless('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is prohibited unless bar is in x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_prohibits_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::prohibits('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::prohibits('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => null,
            ],
            ['foo' => [ValidationRuleMaker::prohibits('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::prohibits('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field prohibits bar from being present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_regular_expression_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::regularExpression('/^[a-z]+$/')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'Bar1'],
            ['foo' => [ValidationRuleMaker::regularExpression('/^[a-z]+$/')]],
        );

        self::assertSame(
            ['foo' => ['The foo field format is invalid.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::required()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::required()]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::required()]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_array_keys_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => ['bar' => 'x', 'acme' => 'y']],
            ['foo' => [ValidationRuleMaker::requiredArrayKeys('bar', 'acme')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => ['bar' => 'x', 'lipsum' => 'y']],
            ['foo' => [ValidationRuleMaker::requiredArrayKeys('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must contain entries for: bar, acme.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_if_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredIf('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredIf('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'y'],
            ['foo' => [ValidationRuleMaker::requiredIf('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::requiredIf('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is x.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredIf('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_if_logic_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::requiredIfLogic(fn (): bool => true)]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::requiredIfLogic(fn (): bool => false)]],
        ));

        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::requiredIfLogic(fn (): bool => false)]],
        ));

        $errors = $this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::requiredIfLogic(fn (): bool => true)]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::requiredIfLogic(fn (): bool => true)]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_if_accepted_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => true,
            ],
            ['foo' => [ValidationRuleMaker::requiredIfAccepted('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => false,
            ],
            ['foo' => [ValidationRuleMaker::requiredIfAccepted('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => false],
            ['foo' => [ValidationRuleMaker::requiredIfAccepted('bar')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => true],
            ['foo' => [ValidationRuleMaker::requiredIfAccepted('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is accepted.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => true,
            ],
            ['foo' => [ValidationRuleMaker::requiredIfAccepted('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is accepted.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_if_declined_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => false,
            ],
            ['foo' => [ValidationRuleMaker::requiredIfDeclined('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => true,
            ],
            ['foo' => [ValidationRuleMaker::requiredIfDeclined('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => true],
            ['foo' => [ValidationRuleMaker::requiredIfDeclined('bar')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => false],
            ['foo' => [ValidationRuleMaker::requiredIfDeclined('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is declined.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => false,
            ],
            ['foo' => [ValidationRuleMaker::requiredIfDeclined('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is declined.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_unless_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredUnless('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredUnless('bar', 'x')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::requiredUnless('bar', 'x')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => 'y'],
            ['foo' => [ValidationRuleMaker::requiredUnless('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required unless bar is in x.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredUnless('bar', 'x')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required unless bar is in x.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredWith('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::requiredWith('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::requiredWith('bar')]],
        ));

        $errors = $this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::requiredWith('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is present.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredWith('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_with_all_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo'  => 'lipsum',
                'bar'  => 'x',
                'acme' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo'  => null,
                'acme' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['acme' => 'y'],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        ));

        $errors = $this->generateErrors(
            [
                'bar'  => 'x',
                'acme' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar / acme are present.']],
            $errors,
        );

        $errors = $this->generateErrors(
            [
                'foo'  => null,
                'bar'  => 'x',
                'acme' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithAll('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar / acme are present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_without_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::requiredWithout('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithout('bar')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::requiredWithout('bar')]],
        ));

        $errors = $this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::requiredWithout('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is not present.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::requiredWithout('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when bar is not present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_required_without_all_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo' => null,
                'bar' => 'x',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            [
                'foo'  => null,
                'acme' => 'y',
            ],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['bar' => 'x'],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        ));

        self::assertEmpty($this->generateErrors(
            ['acme' => 'y'],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        ));

        $errors = $this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when none of bar / acme are present.']],
            $errors,
        );

        $errors = $this->generateErrors(
            ['foo' => null],
            ['foo' => [ValidationRuleMaker::requiredWithoutAll('bar', 'acme')]],
        );

        self::assertSame(
            ['foo' => ['The foo field is required when none of bar / acme are present.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_same_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'lipsum',
            ],
            ['foo' => [ValidationRuleMaker::same('bar')]],
        ));

        $errors = $this->generateErrors(
            [
                'foo' => 'lipsum',
                'bar' => 'acme',
            ],
            ['foo' => [ValidationRuleMaker::same('bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must match bar.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_size_rule_as_string(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => ['string', ValidationRuleMaker::size(3)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'x'],
            ['foo' => ['string', ValidationRuleMaker::size(3)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be 3 characters.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_size_rule_as_integer(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 3],
            ['foo' => ['integer', ValidationRuleMaker::size(3)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1],
            ['foo' => ['integer', ValidationRuleMaker::size(3)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be 3.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_size_rule_as_number(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 3.3],
            ['foo' => ['numeric', ValidationRuleMaker::size(3.3)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 1.1],
            ['foo' => ['numeric', ValidationRuleMaker::size(3.3)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be 3.3.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_size_rule_as_array(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => [1, 2, 3]],
            ['foo' => ['array', ValidationRuleMaker::size(3)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => [1]],
            ['foo' => ['array', ValidationRuleMaker::size(3)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must contain 3 items.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_size_rule_as_file(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 300)],
            ['foo' => ['file', ValidationRuleMaker::size(300)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => UploadedFile::fake()->create('file.txt', 100)],
            ['foo' => ['file', ValidationRuleMaker::size(300)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be 300 kilobytes.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_sometimes_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            [],
            ['foo' => [ValidationRuleMaker::sometimes(), 'string']],
        ));

        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::sometimes(), 'string']],
        ));

        $errors = $this->generateErrors(
            ['foo' => true],
            ['foo' => [ValidationRuleMaker::sometimes(), 'string']],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a string.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_starts_with_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::startsWith('lip', 'bar')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'lipsum'],
            ['foo' => [ValidationRuleMaker::startsWith('sum', 'bar')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must start with one of the following: sum, bar.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_string_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::string()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 123],
            ['foo' => [ValidationRuleMaker::string()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a string.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_timezone_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'Europe/Amsterdam'],
            ['foo' => [ValidationRuleMaker::timezone()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::timezone()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid timezone.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_timezone_rule_with_timezone(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'Europe/Amsterdam'],
            ['foo' => [ValidationRuleMaker::timezone('Europe')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'America/New_York'],
            ['foo' => [ValidationRuleMaker::timezone('Europe')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid timezone.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_timezone_rule_with_country(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'Europe/Amsterdam'],
            ['foo' => [ValidationRuleMaker::timezone('per_country,NL')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'America/New_York'],
            ['foo' => [ValidationRuleMaker::timezone('per_country,NL')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid timezone.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_ulid_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => Str::ulid()->toString()],
            ['foo' => [ValidationRuleMaker::ulid()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::ulid()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid ULID.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_url_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'https://example.com'],
            ['foo' => [ValidationRuleMaker::url()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::url()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid URL.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_url_rule_with_protocol(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'https://example.com'],
            ['foo' => [ValidationRuleMaker::url('https')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'http://example.com'],
            ['foo' => [ValidationRuleMaker::url('https')]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid URL.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_uuid_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => Str::uuid()->toString()],
            ['foo' => [ValidationRuleMaker::uuid()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'bar'],
            ['foo' => [ValidationRuleMaker::uuid()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid UUID.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_uuid_rule_with_version(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => Str::uuid7()->toString()],
            ['foo' => [ValidationRuleMaker::uuid(7)]],
        ));

        $errors = $this->generateErrors(
            ['foo' => Str::uuid()->toString()],
            ['foo' => [ValidationRuleMaker::uuid(7)]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be a valid UUID.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_a_unique_rule(): void
    {
        new UserFactory()->create(['email' => 'johndoe@example.com']);

        self::assertEmpty($this->generateErrors(
            ['foo' => 'janesmith@example.com'],
            ['foo' => [ValidationRuleMaker::unique('users', 'email')]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'johndoe@example.com'],
            ['foo' => [ValidationRuleMaker::unique('users', 'email')]],
        );

        self::assertSame(
            ['foo' => ['The foo has already been taken.']],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_uppercase_rule(): void
    {
        self::assertEmpty($this->generateErrors(
            ['foo' => 'XXX'],
            ['foo' => [ValidationRuleMaker::uppercase()]],
        ));

        $errors = $this->generateErrors(
            ['foo' => 'XxX'],
            ['foo' => [ValidationRuleMaker::uppercase()]],
        );

        self::assertSame(
            ['foo' => ['The foo field must be uppercase.']],
            $errors,
        );
    }

    protected function addGuard(?string $guard): void
    {
        if (! $guard) {
            return;
        }

        /** @var array<string, mixed> $guards */
        $guards = config('auth.guards');

        $guards[$guard] = current($guards);

        config(['auth.guards' => $guards]);
    }

    protected function getValidatorFactory(): ValidatorFactory
    {
        return $this->factory;
    }
}
