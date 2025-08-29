<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Validation;

use BackedEnum;
use Closure;
use DateTimeInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\AnyOf;
use Illuminate\Validation\Rules\Contains;
use Illuminate\Validation\Rules\Date;
use Illuminate\Validation\Rules\DoesntContain;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\ExcludeIf;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\NotIn;
use Illuminate\Validation\Rules\ProhibitedIf;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;
use InvalidArgumentException;
use Throwable;

/**
 * @link https://laravel.com/docs/12.x/validation#available-validation-rules
 */
readonly class ValidationRuleMaker
{
    public static function accepted(): string
    {
        return 'accepted';
    }

    public static function acceptedIf(string $anotherField, float|int|string $value): string
    {
        return "accepted_if:{$anotherField},{$value}";
    }

    public static function activeUrl(): string
    {
        return 'active_url';
    }

    public static function after(DateTimeInterface|string $date): string
    {
        return 'after:' . self::parseDate($date);
    }

    public static function afterOrEqual(DateTimeInterface|string $date): string
    {
        return 'after_or_equal:' . self::parseDate($date);
    }

    public static function alpha(bool $restrictToAscii = false): string
    {
        return self::alphaRule('alpha', $restrictToAscii);
    }

    public static function alphaDash(bool $restrictToAscii = false): string
    {
        return self::alphaRule('alpha_dash', $restrictToAscii);
    }

    public static function alphaNumeric(bool $restrictToAscii = false): string
    {
        return self::alphaRule('alpha_num', $restrictToAscii);
    }

    /**
     * @param array<array-key, string|Rule|Closure> ...$ruleSet
     */
    public static function anyOf(array ...$ruleSet): AnyOf
    {
        return Rule::anyOf($ruleSet);
    }

    public static function array(string ...$key): string
    {
        $rule = 'array';

        if (! empty($key)) {
            $rule .= ':' . implode(',', $key);
        }

        return $rule;
    }

    public static function ascii(): string
    {
        return 'ascii';
    }

    public static function bail(): string
    {
        return 'bail';
    }

    public static function before(DateTimeInterface|string $date): string
    {
        return 'before:' . self::parseDate($date);
    }

    public static function beforeOrEqual(DateTimeInterface|string $date): string
    {
        return 'before_or_equal:' . self::parseDate($date);
    }

    public static function between(float|int $min, float|int $max): string
    {
        return "between:{$min},{$max}";
    }

    public static function boolean(bool $strict = false): string
    {
        $rule = 'boolean';

        if ($strict) {
            $rule .= ':strict';
        }

        return $rule;
    }

    public static function confirmed(?string $customFieldName = null): string
    {
        $rule = 'confirmed';

        if ($customFieldName) {
            $rule .= ":{$customFieldName}";
        }

        return $rule;
    }

    public static function contains(mixed ...$value): Contains
    {
        return Rule::contains($value);
    }

    public static function currentPassword(?string $guard = null): string
    {
        $rule = 'current_password';

        if ($guard) {
            $rule .= ":{$guard}";
        }

        return $rule;
    }

    public static function date(): string
    {
        return 'date';
    }

    public static function dateEquals(DateTimeInterface|string $date): string
    {
        return 'date_equals:' . self::parseDate($date);
    }

    public static function dateFluent(): Date
    {
        return Rule::date();
    }

    public static function dateFormat(string $format): string
    {
        return "date_format:{$format}";
    }

    public static function decimal(int $min, ?int $max = null): string
    {
        $rule = "decimal:{$min}";

        if ($max !== null) {
            $rule .= ",{$max}";
        }

        return $rule;
    }

    public static function declined(): string
    {
        return 'declined';
    }

    public static function declinedIf(string $anotherField, float|int|string $value): string
    {
        return "declined_if:{$anotherField},{$value}";
    }

    public static function different(string $field): string
    {
        return "different:{$field}";
    }

    public static function digits(int $value): string
    {
        return "digits:{$value}";
    }

    public static function digitsBetween(int $min, int $max): string
    {
        return "digits_between:{$min},{$max}";
    }

    public static function dimensions(Dimensions $dimensions): string
    {
        return "dimensions:{$dimensions->getValue()}";
    }

    public static function distinct(bool $strict = false, bool $ignoreCase = false): string
    {
        $rule = 'distinct';

        $flags = self::serializeFlags([
            'strict'      => $strict,
            'ignore_case' => $ignoreCase,
        ]);

        if ($flags) {
            $rule .= ":{$flags}";
        }

        return $rule;
    }

    public static function doesntContain(mixed ...$value): DoesntContain
    {
        return Rule::doesntContain($value);
    }

    public static function doesntEndWith(string ...$value): string
    {
        return 'doesnt_end_with:' . implode(',', $value);
    }

    public static function doesntStartWith(string ...$value): string
    {
        return 'doesnt_start_with:' . implode(',', $value);
    }

    public static function email(
        bool $rfc = true,
        bool $strict = false,
        bool $dns = false,
        bool $spoof = false,
        bool $filter = false,
        bool $filterUnicode = false,
    ): string {
        $rule = 'email';

        $flags = self::serializeFlags([
            'rfc'            => $rfc,
            'strict'         => $strict,
            'dns'            => $dns,
            'spoof'          => $spoof,
            'filter'         => $filter,
            'filter_unicode' => $filterUnicode,
        ]);

        if ($flags) {
            $rule .= ":{$flags}";
        }

        return $rule;
    }

    public static function endsWith(string ...$value): string
    {
        return 'ends_with:' . implode(',', $value);
    }

    /**
     * @param class-string<BackedEnum> $class
     */
    public static function enum(string $class): Enum
    {
        return Rule::enum($class);
    }

    public static function exclude(): string
    {
        return 'exclude';
    }

    public static function excludeIf(string $anotherField, float|int|string $value): string
    {
        return "exclude_if:{$anotherField},{$value}";
    }

    /**
     * @param Closure():bool|bool $value
     */
    public static function excludeIfLogic(Closure|bool $value): ExcludeIf
    {
        return Rule::excludeIf($value);
    }

    public static function excludeUnless(string $anotherField, float|int|string $value): string
    {
        return "exclude_unless:{$anotherField},{$value}";
    }

    public static function excludeWith(string $anotherField): string
    {
        return "exclude_with:{$anotherField}";
    }

    public static function excludeWithout(string $anotherField): string
    {
        return "exclude_without:{$anotherField}";
    }

    public static function exists(string $table, string $column = 'NULL'): Exists
    {
        return Rule::exists($table, $column);
    }

    public static function extensions(string ...$extension): string
    {
        return 'extensions:' . implode(',', $extension);
    }

    public static function file(): string
    {
        return 'file';
    }

    public static function filled(): string
    {
        return 'filled';
    }

    public static function greaterThan(string $field): string
    {
        return "gt:{$field}";
    }

    public static function greaterThanOrEqual(string $field): string
    {
        return "gte:{$field}";
    }

    public static function hexColor(): string
    {
        return 'hex_color';
    }

    public static function ip(): string
    {
        return 'ip';
    }

    public static function ipV4(): string
    {
        return 'ipv4';
    }

    public static function ipV6(): string
    {
        return 'ipv6';
    }

    public static function image(bool $allowSvg = false): string
    {
        $rule = 'image';

        if ($allowSvg) {
            $rule .= ":allow_svg";
        }

        return $rule;
    }

    public static function in(mixed ...$value): In
    {
        return Rule::in($value);
    }

    public static function inArray(string $anotherField): string
    {
        return "in_array:{$anotherField}";
    }

    public static function inArrayKeys(string ...$value): string
    {
        return 'in_array_keys:' . implode(',', $value);
    }

    public static function integer(bool $strict = false): string
    {
        $rule = 'integer';

        if ($strict) {
            $rule .= ":strict";
        }

        return $rule;
    }

    public static function json(): string
    {
        return 'json';
    }

    public static function lessThan(string $field): string
    {
        return "lt:{$field}";
    }

    public static function lessThanOrEqual(string $field): string
    {
        return "lte:{$field}";
    }

    public static function list(): string
    {
        return 'list';
    }

    public static function lowercase(): string
    {
        return 'lowercase';
    }

    public static function macAddress(): string
    {
        return 'mac_address';
    }

    public static function mimeTypes(string ...$mimeType): string
    {
        return 'mimetypes:' . implode(',', $mimeType);
    }

    public static function mimes(string ...$extension): string
    {
        return 'mimes:' . implode(',', $extension);
    }

    public static function max(float|int $value): string
    {
        return "max:{$value}";
    }

    public static function maxDigits(int $value): string
    {
        return "max_digits:{$value}";
    }

    public static function min(float|int $value): string
    {
        return "min:{$value}";
    }

    public static function minDigits(int $value): string
    {
        return "min_digits:{$value}";
    }

    public static function missing(): string
    {
        return 'missing';
    }

    public static function missingIf(string $anotherField, float|int|string $value): string
    {
        return "missing_if:{$anotherField},{$value}";
    }

    public static function missingUnless(string $anotherField, float|int|string $value): string
    {
        return "missing_unless:{$anotherField},{$value}";
    }

    public static function missingWith(string ...$field): string
    {
        return 'missing_with:' . implode(',', $field);
    }

    public static function missingWithAll(string ...$field): string
    {
        return 'missing_with_all:' . implode(',', $field);
    }

    public static function multipleOf(float|int $value): string
    {
        return "multiple_of:{$value}";
    }

    public static function notIn(mixed ...$value): NotIn
    {
        return Rule::notIn($value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function notRegularExpression(string $pattern): string
    {
        self::validateRegularExpression($pattern);

        return "not_regex:{$pattern}";
    }

    public static function nullable(): string
    {
        return 'nullable';
    }

    public static function numeric(bool $strict = false): string
    {
        $rule = 'numeric';

        if ($strict) {
            $rule .= ":strict";
        }

        return $rule;
    }

    public static function present(): string
    {
        return 'present';
    }

    public static function presentIf(string $anotherField, float|int|string $value): string
    {
        return "present_if:{$anotherField},{$value}";
    }

    public static function presentUnless(string $anotherField, float|int|string $value): string
    {
        return "present_unless:{$anotherField},{$value}";
    }

    public static function presentWith(string ...$field): string
    {
        return 'present_with:' . implode(',', $field);
    }

    public static function presentWithAll(string ...$field): string
    {
        return 'present_with_all:' . implode(',', $field);
    }

    public static function prohibited(): string
    {
        return 'prohibited';
    }

    public static function prohibitedIf(string $anotherField, float|int|string $value): string
    {
        return "prohibited_if:{$anotherField},{$value}";
    }

    /**
     * @param Closure():bool|bool $value
     */
    public static function prohibitedIfLogic(Closure|bool $value): ProhibitedIf
    {
        return Rule::prohibitedIf($value);
    }

    public static function prohibitedIfAccepted(string $anotherField): string
    {
        return "prohibited_if_accepted:{$anotherField}";
    }

    public static function prohibitedIfDeclined(string $anotherField): string
    {
        return "prohibited_if_declined:{$anotherField}";
    }

    public static function prohibitedUnless(string $anotherField, float|int|string $value): string
    {
        return "prohibited_unless:{$anotherField},{$value}";
    }

    public static function prohibits(string $anotherField): string
    {
        return "prohibits:{$anotherField}";
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function regularExpression(string $pattern): string
    {
        self::validateRegularExpression($pattern);

        return "regex:{$pattern}";
    }

    public static function required(): string
    {
        return 'required';
    }

    public static function requiredArrayKeys(string ...$key): string
    {
        return 'required_array_keys:' . implode(',', $key);
    }

    public static function requiredIf(string $anotherField, float|int|string $value): string
    {
        return "required_if:{$anotherField},{$value}";
    }

    /**
     * @param Closure():bool|bool $value
     */
    public static function requiredIfLogic(Closure|bool $value): RequiredIf
    {
        return Rule::requiredIf($value);
    }

    public static function requiredIfAccepted(string $anotherField): string
    {
        return "required_if_accepted:{$anotherField}";
    }

    public static function requiredIfDeclined(string $anotherField): string
    {
        return "required_if_declined:{$anotherField}";
    }

    public static function requiredUnless(string $anotherField, float|int|string $value): string
    {
        return "required_unless:{$anotherField},{$value}";
    }

    public static function requiredWith(string ...$field): string
    {
        return 'required_with:' . implode(',', $field);
    }

    public static function requiredWithAll(string ...$field): string
    {
        return 'required_with_all:' . implode(',', $field);
    }

    public static function requiredWithout(string ...$field): string
    {
        return 'required_without:' . implode(',', $field);
    }

    public static function requiredWithoutAll(string ...$field): string
    {
        return 'required_without_all:' . implode(',', $field);
    }

    public static function same(string $field): string
    {
        return "same:{$field}";
    }

    public static function size(float|int $value): string
    {
        return "size:{$value}";
    }

    public static function sometimes(): string
    {
        return 'sometimes';
    }

    public static function startsWith(string ...$value): string
    {
        return 'starts_with:' . implode(',', $value);
    }

    public static function string(): string
    {
        return 'string';
    }

    public static function timezone(?string $value = null): string
    {
        $rule = 'timezone';

        if (! empty($value)) {
            $rule .= ":{$value}";
        }

        return $rule;
    }

    public static function ulid(): string
    {
        return 'ulid';
    }

    public static function url(string ...$value): string
    {
        $rule = 'url';

        if (! empty($value)) {
            $rule .= ':' . implode(',', $value);
        }

        return $rule;
    }

    public static function uuid(?int $version = null): string
    {
        $rule = 'uuid';

        if ($version) {
            $rule .= ":{$version}";
        }

        return $rule;
    }

    public static function unique(string $table, string $column = 'NULL'): Unique
    {
        return Rule::unique($table, $column);
    }

    public static function uppercase(): string
    {
        return 'uppercase';
    }

    protected static function alphaRule(string $rule, bool $restrictToAscii): string
    {
        return $restrictToAscii ? "{$rule}:ascii" : $rule;
    }

    protected static function parseDate(DateTimeInterface|string $date): string
    {
        return $date instanceof DateTimeInterface ? $date->format('Y-m-d') : $date;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected static function validateRegularExpression(string $pattern): void
    {
        try {
            if (preg_match($pattern, '') === false) {
                throw new Exception();
            }
        } catch (Throwable) {
            throw new InvalidArgumentException("Pattern '{$pattern}' is not a valid regular expression");
        }
    }

    /**
     * @param array<string, bool> $flags
     */
    protected static function serializeFlags(array $flags): string
    {
        return new Collection($flags)
            ->filter()
            ->keys()
            ->implode(',');
    }
}
