<?php

declare(strict_types=1);

namespace Tests\Suites\Unit\Validation;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\Suites\Unit\AbstractUnitTestCase;
use Wimski\LaravelUtils\Validation\Dimensions;
use Wimski\LaravelUtils\Validation\ValidationRuleMaker;

class ValidationRuleMakerTest extends AbstractUnitTestCase
{
    #[Test]
    public function it_makes_an_accepted_rule(): void
    {
        self::assertSame('accepted', ValidationRuleMaker::accepted());
    }

    #[Test]
    public function it_makes_an_accepted_if_rule(): void
    {
        self::assertSame('accepted_if:foo,bar', ValidationRuleMaker::acceptedIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_an_active_url_rule(): void
    {
        self::assertSame('active_url', ValidationRuleMaker::activeUrl());
    }

    #[Test]
    public function it_makes_an_after_rule(): void
    {
        self::assertSame('after:foo', ValidationRuleMaker::after('foo'));
        self::assertSame('after:2025-04-03', ValidationRuleMaker::after(new DateTimeImmutable('2025-04-03')));
    }

    #[Test]
    public function it_makes_an_after_or_equal_rule(): void
    {
        self::assertSame('after_or_equal:foo', ValidationRuleMaker::afterOrEqual('foo'));
        self::assertSame('after_or_equal:2025-04-03', ValidationRuleMaker::afterOrEqual(new DateTimeImmutable('2025-04-03')));
    }

    #[Test]
    public function it_makes_an_alpha_rule(): void
    {
        self::assertSame('alpha', ValidationRuleMaker::alpha());
        self::assertSame('alpha:ascii', ValidationRuleMaker::alpha(true));
    }

    #[Test]
    public function it_makes_an_alpha_dash_rule(): void
    {
        self::assertSame('alpha_dash', ValidationRuleMaker::alphaDash());
        self::assertSame('alpha_dash:ascii', ValidationRuleMaker::alphaDash(true));
    }

    #[Test]
    public function it_makes_an_alpha_numeric_rule(): void
    {
        self::assertSame('alpha_num', ValidationRuleMaker::alphaNumeric());
        self::assertSame('alpha_num:ascii', ValidationRuleMaker::alphaNumeric(true));
    }

    #[Test]
    public function it_makes_an_any_of_rule(): void
    {
        self::markTestSkipped('Untestable');
    }

    #[Test]
    public function it_makes_an_array_rule(): void
    {
        self::assertSame('array', ValidationRuleMaker::array());
        self::assertSame('array:foo,bar', ValidationRuleMaker::array('foo', 'bar'));
    }

    #[Test]
    public function it_makes_an_ascii_rule(): void
    {
        self::assertSame('ascii', ValidationRuleMaker::ascii());
    }

    #[Test]
    public function it_makes_a_bail_rule(): void
    {
        self::assertSame('bail', ValidationRuleMaker::bail());
    }

    #[Test]
    public function it_makes_a_before_rule(): void
    {
        self::assertSame('before:foo', ValidationRuleMaker::before('foo'));
        self::assertSame('before:2025-04-03', ValidationRuleMaker::before(new DateTimeImmutable('2025-04-03')));
    }

    #[Test]
    public function it_makes_a_before_or_equal_rule(): void
    {
        self::assertSame('before_or_equal:foo', ValidationRuleMaker::beforeOrEqual('foo'));
        self::assertSame('before_or_equal:2025-04-03', ValidationRuleMaker::beforeOrEqual(new DateTimeImmutable('2025-04-03')));
    }

    #[Test]
    public function it_makes_a_between_rule(): void
    {
        self::assertSame('between:1,2.2', ValidationRuleMaker::between(1, 2.2));
    }

    #[Test]
    public function it_makes_a_boolean_rule(): void
    {
        self::assertSame('boolean', ValidationRuleMaker::boolean());
        self::assertSame('boolean:strict', ValidationRuleMaker::boolean(true));
    }

    #[Test]
    public function it_makes_a_confirmed_rule(): void
    {
        self::assertSame('confirmed', ValidationRuleMaker::confirmed());
        self::assertSame('confirmed:foo', ValidationRuleMaker::confirmed('foo'));
    }

    #[Test]
    public function it_makes_a_contains_rule(): void
    {
        self::assertSame('contains:"foo","123"', ValidationRuleMaker::contains('foo', 123)->__toString());
    }

    #[Test]
    public function it_makes_a_current_password_rule(): void
    {
        self::assertSame('current_password', ValidationRuleMaker::currentPassword());
        self::assertSame('current_password:foo', ValidationRuleMaker::currentPassword('foo'));
    }

    #[Test]
    public function it_makes_a_date_rule(): void
    {
        self::assertSame('date', ValidationRuleMaker::date());
    }

    #[Test]
    public function it_makes_a_date_equals_rule(): void
    {
        self::assertSame('date_equals:foo', ValidationRuleMaker::dateEquals('foo'));
        self::assertSame('date_equals:2025-04-03', ValidationRuleMaker::dateEquals(new DateTimeImmutable('2025-04-03')));
    }

    #[Test]
    public function it_makes_a_date_fluent_rule(): void
    {
        self::assertSame('date', ValidationRuleMaker::dateFluent()->__toString());
    }

    #[Test]
    public function it_makes_a_date_format_rule(): void
    {
        self::assertSame('date_format:foo', ValidationRuleMaker::dateFormat('foo'));
    }

    #[Test]
    public function it_makes_a_decimal_rule(): void
    {
        self::assertSame('decimal:1', ValidationRuleMaker::decimal(1));
        self::assertSame('decimal:1,2', ValidationRuleMaker::decimal(1, 2));
    }

    #[Test]
    public function it_makes_a_declined_rule(): void
    {
        self::assertSame('declined', ValidationRuleMaker::declined());
    }

    #[Test]
    public function it_makes_a_declined_if_rule(): void
    {
        self::assertSame('declined_if:foo,bar', ValidationRuleMaker::declinedIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_different_rule(): void
    {
        self::assertSame('different:foo', ValidationRuleMaker::different('foo'));
    }

    #[Test]
    public function it_makes_a_digits_rule(): void
    {
        self::assertSame('digits:123', ValidationRuleMaker::digits(123));
    }

    #[Test]
    public function it_makes_a_digits_between_rule(): void
    {
        self::assertSame('digits_between:1,2', ValidationRuleMaker::digitsBetween(1, 2));
    }

    #[Test]
    public function it_makes_a_dimensions_rule(): void
    {
        $dimensions = new Dimensions()
            ->minWidth(1)
            ->minHeight(2)
            ->maxWidth(3)
            ->maxHeight(4)
            ->width(5)
            ->height(6)
            ->ratio(7);

        self::assertSame(
            'dimensions:min_width=1,min_height=2,max_width=3,max_height=4,width=5,height=6,ratio=7',
            ValidationRuleMaker::dimensions($dimensions),
        );
    }

    #[Test]
    public function it_makes_a_distinct_rule(): void
    {
        self::assertSame('distinct', ValidationRuleMaker::distinct());
        self::assertSame('distinct:strict', ValidationRuleMaker::distinct(true));
        self::assertSame('distinct:ignore_case', ValidationRuleMaker::distinct(false, true));
        self::assertSame('distinct:strict,ignore_case', ValidationRuleMaker::distinct(true, true));
    }

    #[Test]
    public function it_makes_a_doesnt_contain_rule(): void
    {
        self::assertSame('doesnt_contain:"foo","123"', ValidationRuleMaker::doesntContain('foo', 123)->__toString());
    }

    #[Test]
    public function it_makes_a_doesnt_end_with_rule(): void
    {
        self::assertSame('doesnt_end_with:foo', ValidationRuleMaker::doesntEndWith('foo'));
    }

    #[Test]
    public function it_makes_a_doesnt_start_with_rule(): void
    {
        self::assertSame('doesnt_start_with:foo', ValidationRuleMaker::doesntStartWith('foo'));
    }

    #[Test]
    public function it_makes_an_email_rule(): void
    {
        self::assertSame('email:rfc', ValidationRuleMaker::email());
        self::assertSame('email', ValidationRuleMaker::email(false));
        self::assertSame(
            'email:strict,dns,spoof,filter,filter_unicode',
            ValidationRuleMaker::email(false, true, true, true, true, true),
        );
    }

    #[Test]
    public function it_makes_an_ends_with_rule(): void
    {
        self::assertSame('ends_with:foo', ValidationRuleMaker::endsWith('foo'));
    }

    #[Test]
    public function it_makes_an_enum_rule(): void
    {
        self::markTestSkipped('Untestable');
    }

    #[Test]
    public function it_makes_an_exclude_rule(): void
    {
        self::assertSame('exclude', ValidationRuleMaker::exclude());
    }

    #[Test]
    public function it_makes_an_exclude_if_rule(): void
    {
        self::assertSame('exclude_if:foo,bar', ValidationRuleMaker::excludeIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_an_exclude_if_logic_rule(): void
    {
        self::assertSame('exclude', ValidationRuleMaker::excludeIfLogic(fn (): bool => true)->__toString());
        self::assertSame('', ValidationRuleMaker::excludeIfLogic(fn (): bool => false)->__toString());
    }

    #[Test]
    public function it_makes_an_exclude_unless_rule(): void
    {
        self::assertSame('exclude_unless:foo,bar', ValidationRuleMaker::excludeUnless('foo', 'bar'));
    }

    #[Test]
    public function it_makes_an_exclude_with_rule(): void
    {
        self::assertSame('exclude_with:foo', ValidationRuleMaker::excludeWith('foo'));
    }

    #[Test]
    public function it_makes_an_exclude_without_rule(): void
    {
        self::assertSame('exclude_without:foo', ValidationRuleMaker::excludeWithout('foo'));
    }

    #[Test]
    public function it_makes_an_exists_rule(): void
    {
        self::assertSame('exists:foo,bar', ValidationRuleMaker::exists('foo', 'bar')->__toString());
    }

    #[Test]
    public function it_makes_an_extensions_rule(): void
    {
        self::assertSame('extensions:foo,bar', ValidationRuleMaker::extensions('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_file_rule(): void
    {
        self::assertSame('file', ValidationRuleMaker::file());
    }

    #[Test]
    public function it_makes_a_filled_rule(): void
    {
        self::assertSame('filled', ValidationRuleMaker::filled());
    }

    #[Test]
    public function it_makes_a_greater_than_rule(): void
    {
        self::assertSame('gt:foo', ValidationRuleMaker::greaterThan('foo'));
    }

    #[Test]
    public function it_makes_a_greater_than_or_equal_rule(): void
    {
        self::assertSame('gte:foo', ValidationRuleMaker::greaterThanOrEqual('foo'));
    }

    #[Test]
    public function it_makes_a_hex_color_rule(): void
    {
        self::assertSame('hex_color', ValidationRuleMaker::hexColor());
    }

    #[Test]
    public function it_makes_an_ip_rule(): void
    {
        self::assertSame('ip', ValidationRuleMaker::ip());
    }

    #[Test]
    public function it_makes_an_ip_v4_rule(): void
    {
        self::assertSame('ipv4', ValidationRuleMaker::ipV4());
    }

    #[Test]
    public function it_makes_an_ip_v6_rule(): void
    {
        self::assertSame('ipv6', ValidationRuleMaker::ipV6());
    }

    #[Test]
    public function it_makes_an_image_rule(): void
    {
        self::assertSame('image', ValidationRuleMaker::image());
        self::assertSame('image:allow_svg', ValidationRuleMaker::image(true));
    }

    #[Test]
    public function it_makes_an_in_rule(): void
    {
        self::assertSame('in:"foo","bar"', ValidationRuleMaker::in('foo', 'bar')->__toString());
    }

    #[Test]
    public function it_makes_an_in_array_rule(): void
    {
        self::assertSame('in_array:foo', ValidationRuleMaker::inArray('foo'));
    }

    #[Test]
    public function it_makes_an_in_array_keys_rule(): void
    {
        self::assertSame('in_array_keys:foo,bar', ValidationRuleMaker::inArrayKeys('foo', 'bar'));
    }

    #[Test]
    public function it_makes_an_integer_rule(): void
    {
        self::assertSame('integer', ValidationRuleMaker::integer());
        self::assertSame('integer:strict', ValidationRuleMaker::integer(true));
    }

    #[Test]
    public function it_makes_a_json_rule(): void
    {
        self::assertSame('json', ValidationRuleMaker::json());
    }

    #[Test]
    public function it_makes_a_less_than_rule(): void
    {
        self::assertSame('lt:foo', ValidationRuleMaker::lessThan('foo'));
    }

    #[Test]
    public function it_makes_a_less_than_or_equal_rule(): void
    {
        self::assertSame('lte:foo', ValidationRuleMaker::lessThanOrEqual('foo'));
    }

    #[Test]
    public function it_makes_a_list_rule(): void
    {
        self::assertSame('list', ValidationRuleMaker::list());
    }

    #[Test]
    public function it_makes_a_lowercase_rule(): void
    {
        self::assertSame('lowercase', ValidationRuleMaker::lowercase());
    }

    #[Test]
    public function it_makes_a_mac_address_rule(): void
    {
        self::assertSame('mac_address', ValidationRuleMaker::macAddress());
    }

    #[Test]
    public function it_makes_a_mime_types_rule(): void
    {
        self::assertSame('mimetypes:foo,bar', ValidationRuleMaker::mimeTypes('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_mimes_rule(): void
    {
        self::assertSame('mimes:foo,bar', ValidationRuleMaker::mimes('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_max_rule(): void
    {
        self::assertSame('max:123', ValidationRuleMaker::max(123));
    }

    #[Test]
    public function it_makes_a_max_digits_rule(): void
    {
        self::assertSame('max_digits:123', ValidationRuleMaker::maxDigits(123));
    }

    #[Test]
    public function it_makes_a_min_rule(): void
    {
        self::assertSame('min:123', ValidationRuleMaker::min(123));
    }

    #[Test]
    public function it_makes_a_min_digits_rule(): void
    {
        self::assertSame('min_digits:123', ValidationRuleMaker::minDigits(123));
    }

    #[Test]
    public function it_makes_a_missing_rule(): void
    {
        self::assertSame('missing', ValidationRuleMaker::missing());
    }

    #[Test]
    public function it_makes_a_missing_if_rule(): void
    {
        self::assertSame('missing_if:foo,bar', ValidationRuleMaker::missingIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_missing_unless_rule(): void
    {
        self::assertSame('missing_unless:foo,bar', ValidationRuleMaker::missingUnless('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_missing_with_rule(): void
    {
        self::assertSame('missing_with:foo,bar', ValidationRuleMaker::missingWith('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_missing_with_all_rule(): void
    {
        self::assertSame('missing_with_all:foo,bar', ValidationRuleMaker::missingWithAll('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_multiple_of_rule(): void
    {
        self::assertSame('multiple_of:123', ValidationRuleMaker::multipleOf(123));
    }

    #[Test]
    public function it_makes_a_not_in_rule(): void
    {
        self::assertSame('not_in:"foo","bar"', ValidationRuleMaker::notIn('foo', 'bar')->__toString());
    }

    #[Test]
    public function it_makes_a_not_regular_expression_rule(): void
    {
        self::assertSame('not_regex:/^.+$/', ValidationRuleMaker::notRegularExpression('/^.+$/'));
    }

    #[Test]
    public function it_throws_an_exception_when_making_a_not_regular_expression_rule_if_the_pattern_is_invalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage("Pattern 'foo' is not a valid regular expression");

        ValidationRuleMaker::notRegularExpression('foo');
    }

    #[Test]
    public function it_makes_a_nullable_rule(): void
    {
        self::assertSame('nullable', ValidationRuleMaker::nullable());
    }

    #[Test]
    public function it_makes_a_numeric_rule(): void
    {
        self::assertSame('numeric', ValidationRuleMaker::numeric());
        self::assertSame('numeric:strict', ValidationRuleMaker::numeric(true));
    }

    #[Test]
    public function it_makes_a_present_rule(): void
    {
        self::assertSame('present', ValidationRuleMaker::present());
    }

    #[Test]
    public function it_makes_a_present_if_rule(): void
    {
        self::assertSame('present_if:foo,bar', ValidationRuleMaker::presentIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_present_unless_rule(): void
    {
        self::assertSame('present_unless:foo,bar', ValidationRuleMaker::presentUnless('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_present_with_rule(): void
    {
        self::assertSame('present_with:foo,bar', ValidationRuleMaker::presentWith('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_present_with_all_rule(): void
    {
        self::assertSame('present_with_all:foo,bar', ValidationRuleMaker::presentWithAll('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_prohibited_rule(): void
    {
        self::assertSame('prohibited', ValidationRuleMaker::prohibited());
    }

    #[Test]
    public function it_makes_a_prohibited_if_rule(): void
    {
        self::assertSame('prohibited_if:foo,bar', ValidationRuleMaker::prohibitedIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_prohibited_if_logic_rule(): void
    {
        self::assertSame('prohibited', ValidationRuleMaker::prohibitedIfLogic(fn (): bool => true)->__toString());
        self::assertSame('', ValidationRuleMaker::prohibitedIfLogic(fn (): bool => false)->__toString());
    }

    #[Test]
    public function it_makes_a_prohibited_if_accepted_rule(): void
    {
        self::assertSame('prohibited_if_accepted:foo', ValidationRuleMaker::prohibitedIfAccepted('foo'));
    }

    #[Test]
    public function it_makes_a_prohibited_if_declined_rule(): void
    {
        self::assertSame('prohibited_if_declined:foo', ValidationRuleMaker::prohibitedIfDeclined('foo'));
    }

    #[Test]
    public function it_makes_a_prohibited_unless_rule(): void
    {
        self::assertSame('prohibited_unless:foo,bar', ValidationRuleMaker::prohibitedUnless('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_prohibits_rule(): void
    {
        self::assertSame('prohibits:foo', ValidationRuleMaker::prohibits('foo'));
    }

    #[Test]
    public function it_makes_a_regular_expression_rule(): void
    {
        self::assertSame('regex:/^.+$/', ValidationRuleMaker::regularExpression('/^.+$/'));
    }

    #[Test]
    public function it_throws_an_exception_when_making_a_regular_expression_rule_if_the_pattern_is_invalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage("Pattern 'foo' is not a valid regular expression");

        ValidationRuleMaker::regularExpression('foo');
    }

    #[Test]
    public function it_makes_a_required_rule(): void
    {
        self::assertSame('required', ValidationRuleMaker::required());
    }

    #[Test]
    public function it_makes_a_required_array_keys_rule(): void
    {
        self::assertSame('required_array_keys:foo,bar', ValidationRuleMaker::requiredArrayKeys('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_required_if_rule(): void
    {
        self::assertSame('required_if:foo,bar', ValidationRuleMaker::requiredIf('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_required_if_logic_rule(): void
    {
        self::assertSame('required', ValidationRuleMaker::requiredIfLogic(fn (): bool => true)->__toString());
        self::assertSame('', ValidationRuleMaker::requiredIfLogic(fn (): bool => false)->__toString());
    }

    #[Test]
    public function it_makes_a_required_if_accepted_rule(): void
    {
        self::assertSame('required_if_accepted:foo', ValidationRuleMaker::requiredIfAccepted('foo'));
    }

    #[Test]
    public function it_makes_a_required_if_declined_rule(): void
    {
        self::assertSame('required_if_declined:foo', ValidationRuleMaker::requiredIfDeclined('foo'));
    }

    #[Test]
    public function it_makes_a_required_unless_rule(): void
    {
        self::assertSame('required_unless:foo,bar', ValidationRuleMaker::requiredUnless('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_required_with_rule(): void
    {
        self::assertSame('required_with:foo,bar', ValidationRuleMaker::requiredWith('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_required_with_all_rule(): void
    {
        self::assertSame('required_with_all:foo,bar', ValidationRuleMaker::requiredWithAll('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_required_without_rule(): void
    {
        self::assertSame('required_without:foo,bar', ValidationRuleMaker::requiredWithout('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_required_without_all_rule(): void
    {
        self::assertSame('required_without_all:foo,bar', ValidationRuleMaker::requiredWithoutAll('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_same_rule(): void
    {
        self::assertSame('same:foo', ValidationRuleMaker::same('foo'));
    }

    #[Test]
    public function it_makes_a_size_rule(): void
    {
        self::assertSame('size:123', ValidationRuleMaker::size(123));
    }

    #[Test]
    public function it_makes_a_sometimes_rule(): void
    {
        self::assertSame('sometimes', ValidationRuleMaker::sometimes());
    }

    #[Test]
    public function it_makes_a_starts_with_rule(): void
    {
        self::assertSame('starts_with:foo,bar', ValidationRuleMaker::startsWith('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_string_rule(): void
    {
        self::assertSame('string', ValidationRuleMaker::string());
    }

    #[Test]
    public function it_makes_a_timezone_rule(): void
    {
        self::assertSame('timezone:foo', ValidationRuleMaker::timezone('foo'));
    }

    #[Test]
    public function it_makes_a_ulid_rule(): void
    {
        self::assertSame('ulid', ValidationRuleMaker::ulid());
    }

    #[Test]
    public function it_makes_a_url_rule(): void
    {
        self::assertSame('url:foo,bar', ValidationRuleMaker::url('foo', 'bar'));
    }

    #[Test]
    public function it_makes_a_uuid_rule(): void
    {
        self::assertSame('uuid', ValidationRuleMaker::uuid());
        self::assertSame('uuid:1', ValidationRuleMaker::uuid(1));
    }

    #[Test]
    public function it_makes_an_unique_rule(): void
    {
        self::assertSame('unique:foo,bar,NULL,id', ValidationRuleMaker::unique('foo', 'bar')->__toString());
    }

    #[Test]
    public function it_makes_an_uppercase_rule(): void
    {
        self::assertSame('uppercase', ValidationRuleMaker::uppercase());
    }
}
