# Validation

## `ValidationRuleMaker`

The `ValidationRuleMaker` class provides a unified way
to define validation rules using method references.

* See the [class](./../src/Validation/ValidationRuleMaker.php) for all available methods.
* See the [Laravel documentation](https://laravel.com/docs/12.x/validation#available-validation-rules) for all available rules.

### Example

```php
use Illuminate\Foundation\Http\FormRequest;
use Wimski\LaravelUtils\Validation\ValidationRuleMaker;

class MyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'input_a' => [
                ValidationRuleMaker::required(),
                ValidationRuleMaker::string(),
                ValidationRuleMaker::max(255),
            ],
            'input_b' => [
                ValidationRuleMaker::requiredIf('input_a', 'foo'),
                ValidationRuleMaker::numeric(true),
                ValidationRuleMaker::between(1, 10),
            ],
        ];
    }
}
```

### Reference

| Method                                                                      | Rule                                                                                           |
|:----------------------------------------------------------------------------|:-----------------------------------------------------------------------------------------------|
| `accepted(): string`                                                        | [accepted](https://laravel.com/docs/12.x/validation#rule-accepted)                             |
| `acceptedIf(string $anotherField, float\|int\|string $value): string`       | [accepted_if](https://laravel.com/docs/12.x/validation#rule-accepted-if)                       |
| `activeUrl(): string`                                                       | [active_url](https://laravel.com/docs/12.x/validation#rule-active-url)                         |
| `after(DateTimeInterface\|string $date): string`                            | [after](https://laravel.com/docs/12.x/validation#rule-after)                                   |
| `afterOrEqual(DateTimeInterface\|string $date): string`                     | [after_or_equal](https://laravel.com/docs/12.x/validation#rule-after-or-equal)                 |
| `alpha(bool $restrictToAscii = false): string`                              | [alpha](https://laravel.com/docs/12.x/validation#rule-alpha)                                   |
| `alphaDash(bool $restrictToAscii = false): string`                          | [alpha_dash](https://laravel.com/docs/12.x/validation#rule-alpha-dash)                         |
| `alphaNumeric(bool $restrictToAscii = false): string`                       | [alpha_numeric](https://laravel.com/docs/12.x/validation#rule-alpha-num)                       |
| `anyOf(array ...$ruleSet): AnyOf`                                           | [anyOf](https://laravel.com/docs/12.x/validation#rule-anyof)                                   |
| `array(string ...$key): string`                                             | [array](https://laravel.com/docs/12.x/validation#rule-array)                                   |
| `ascii(): string`                                                           | [ascii](https://laravel.com/docs/12.x/validation#rule-ascii)                                   |
| `bail(): string`                                                            | [bail](https://laravel.com/docs/12.x/validation#rule-bail)                                     |
| `before(DateTimeInterface\|string $date): string`                           | [before](https://laravel.com/docs/12.x/validation#rule-before)                                 |
| `beforeOrEqual(DateTimeInterface\|string $date): string`                    | [before_or_equal](https://laravel.com/docs/12.x/validation#rule-before-or-equal)               |
| `between(float\|int $min, float\|int $max): string`                         | [between](https://laravel.com/docs/12.x/validation#rule-between)                               |
| `boolean(bool $strict = false): string`                                     | [boolean](https://laravel.com/docs/12.x/validation#rule-boolean)                               |
| `confirmed(?string $customFieldName = null): string`                        | [confirmed](https://laravel.com/docs/12.x/validation#rule-confirmed)                           |
| `contains(mixed ...$value): Contains`                                       | [contains](https://laravel.com/docs/12.x/validation#rule-contains)                             |
| `currentPassword(?string $guard = null): string`                            | [current_password](https://laravel.com/docs/12.x/validation#rule-current-password)             |
| `date(): string`                                                            | [date](https://laravel.com/docs/12.x/validation#rule-date)                                     |
| `dateEquals(DateTimeInterface\|string $date): string`                       | [date_equals](https://laravel.com/docs/12.x/validation#rule-date-equals)                       |
| `dateFluent(): Date`                                                        | [Rule::date()](https://laravel.com/docs/12.x/validation#rule-date-format)                      |
| `dateFormat(string $format): string`                                        | [date_format](https://laravel.com/docs/12.x/validation#rule-date-format)                       |
| `decimal(int $min, ?int $max = null): string`                               | [decimal](https://laravel.com/docs/12.x/validation#rule-decimal)                               |
| `declined(): string`                                                        | [declined](https://laravel.com/docs/12.x/validation#rule-declined)                             |
| `declinedIf(string $anotherField, float\|int\|string $value): string`       | [declined_if](https://laravel.com/docs/12.x/validation#rule-declined-if)                       |
| `different(string $field): string`                                          | [different](https://laravel.com/docs/12.x/validation#rule-different)                           |
| `digits(int $value): string`                                                | [digits](https://laravel.com/docs/12.x/validation#rule-digits)                                 |
| `digitsBetween(int $min, int $max): string`                                 | [digits_between](https://laravel.com/docs/12.x/validation#rule-digits-between)                 |
| `dimensions(Dimensions $dimensions): string`                                | [dimensions](https://laravel.com/docs/12.x/validation#rule-dimensions)                         |
| `distinct(bool $strict = false, bool $ignoreCase = false): string`          | [distinct](https://laravel.com/docs/12.x/validation#rule-distinct)                             |
| `doesntContain(mixed ...$value): DoesntContain`                             | [doesnt_contain](https://laravel.com/docs/12.x/validation#rule-doesnt-contain)                 |
| `doesntEndWith(string ...$value): string`                                   | [doesnt_end_with](https://laravel.com/docs/12.x/validation#rule-doesnt-end-with)               |
| `doesntStartWith(string ...$value): string`                                 | [doesnt_start_with](https://laravel.com/docs/12.x/validation#rule-doesnt-start-with)           |
| `email(`                                                                    | [email](https://laravel.com/docs/12.x/validation#rule-email)                                   |
| `endsWith(string ...$value): string`                                        | [ends_with](https://laravel.com/docs/12.x/validation#rule-ends-with)                           |
| `enum(string $class): Enum`                                                 | [enum](https://laravel.com/docs/12.x/validation#rule-enum)                                     |
| `exclude(): string`                                                         | [exclude](https://laravel.com/docs/12.x/validation#rule-exclude)                               |
| `excludeIf(string $anotherField, float\|int\|string $value): string`        | [exclude_if](https://laravel.com/docs/12.x/validation#rule-exclude-if)                         |
| `excludeIfLogic(Closure\|bool $value): ExcludeIf`                           | [Rule::excludeIf()](https://laravel.com/docs/12.x/validation#rule-exclude-if)                  |
| `excludeUnless(string $anotherField, float\|int\|string $value): string`    | [exclude_unless](https://laravel.com/docs/12.x/validation#rule-exclude-unless)                 |
| `excludeWith(string $anotherField): string`                                 | [exclude_with](https://laravel.com/docs/12.x/validation#rule-exclude-with)                     |
| `excludeWithout(string $anotherField): string`                              | [exclude_without](https://laravel.com/docs/12.x/validation#rule-exclude-without)               |
| `exists(string $table, string $column = 'NULL'): Exists`                    | [exists](https://laravel.com/docs/12.x/validation#rule-exists)                                 |
| `extensions(string ...$extension): string`                                  | [extensions](https://laravel.com/docs/12.x/validation#rule-extensions)                         |
| `file(): string`                                                            | [file](https://laravel.com/docs/12.x/validation#rule-file)                                     |
| `filled(): string`                                                          | [filled](https://laravel.com/docs/12.x/validation#rule-filled)                                 |
| `greaterThan(string $field): string`                                        | [gt](https://laravel.com/docs/12.x/validation#rule-gt)                                         |
| `greaterThanOrEqual(string $field): string`                                 | [gte](https://laravel.com/docs/12.x/validation#rule-gte)                                       |
| `hexColor(): string`                                                        | [hex_color](https://laravel.com/docs/12.x/validation#rule-hex-color)                           |
| `ip(): string`                                                              | [ip](https://laravel.com/docs/12.x/validation#rule-ip)                                         |
| `ipV4(): string`                                                            | [ipv4](https://laravel.com/docs/12.x/validation#ipv4)                                          |
| `ipV6(): string`                                                            | [ipv6](https://laravel.com/docs/12.x/validation#ipv6)                                          |
| `image(bool $allowSvg = false): string`                                     | [image](https://laravel.com/docs/12.x/validation#rule-image)                                   |
| `in(mixed ...$value): In`                                                   | [in](https://laravel.com/docs/12.x/validation#rule-in)                                         |
| `inArray(string $anotherField): string`                                     | [in_array](https://laravel.com/docs/12.x/validation#rule-in-array)                             |
| `inArrayKeys(string ...$value): string`                                     | [in_array_keys](https://laravel.com/docs/12.x/validation#rule-in-array-keys)                   |
| `integer(bool $strict = false): string`                                     | [integer](https://laravel.com/docs/12.x/validation#rule-integer)                               |
| `json(): string`                                                            | [json](https://laravel.com/docs/12.x/validation#rule-json)                                     |
| `lessThan(string $field): string`                                           | [lt](https://laravel.com/docs/12.x/validation#rule-lt)                                         |
| `lessThanOrEqual(string $field): string`                                    | [lte](https://laravel.com/docs/12.x/validation#rule-lte)                                       |
| `list(): string`                                                            | [list](https://laravel.com/docs/12.x/validation#rule-list)                                     |
| `lowercase(): string`                                                       | [lowercase](https://laravel.com/docs/12.x/validation#rule-lowercase)                           |
| `macAddress(): string`                                                      | [mac_address](https://laravel.com/docs/12.x/validation#rule-mac)                               |
| `mimeTypes(string ...$mimeType): string`                                    | [mimetypes](https://laravel.com/docs/12.x/validation#rule-mimetypes)                           |
| `mimes(string ...$extension): string`                                       | [mimes](https://laravel.com/docs/12.x/validation#rule-mimes)                                   |
| `max(float\|int $value): string`                                            | [max](https://laravel.com/docs/12.x/validation#rule-max)                                       |
| `maxDigits(int $value): string`                                             | [max_digits](https://laravel.com/docs/12.x/validation#rule-max-digits)                         |
| `min(float\|int $value): string`                                            | [min](https://laravel.com/docs/12.x/validation#rule-min)                                       |
| `minDigits(int $value): string`                                             | [min_digits](https://laravel.com/docs/12.x/validation#rule-min-digits)                         |
| `missing(): string`                                                         | [missing](https://laravel.com/docs/12.x/validation#rule-missing)                               |
| `missingIf(string $anotherField, float\|int\|string $value): string`        | [missing_if](https://laravel.com/docs/12.x/validation#rule-missing-if)                         |
| `missingUnless(string $anotherField, float\|int\|string $value): string`    | [missing_unless](https://laravel.com/docs/12.x/validation#rule-missing-unless)                 |
| `missingWith(string ...$field): string`                                     | [missing_with](https://laravel.com/docs/12.x/validation#rule-missing-with)                     |
| `missingWithAll(string ...$field): string`                                  | [missing_with_all](https://laravel.com/docs/12.x/validation#rule-missing-with-all)             |
| `multipleOf(float\|int $value): string`                                     | [multiple_of](https://laravel.com/docs/12.x/validation#rule-multiple-of)                       |
| `notIn(mixed ...$value): NotIn`                                             | [not_in](https://laravel.com/docs/12.x/validation#rule-not-in)                                 |
| `notRegularExpression(string $pattern): string`                             | [not_regex](https://laravel.com/docs/12.x/validation#rule-not-regex)                           |
| `nullable(): string`                                                        | [nullable](https://laravel.com/docs/12.x/validation#rule-nullable)                             |
| `numeric(bool $strict = false): string`                                     | [numeric](https://laravel.com/docs/12.x/validation#rule-numeric)                               |
| `present(): string`                                                         | [present](https://laravel.com/docs/12.x/validation#rule-present)                               |
| `presentIf(string $anotherField, float\|int\|string $value): string`        | [present_if](https://laravel.com/docs/12.x/validation#rule-present-if)                         |
| `presentUnless(string $anotherField, float\|int\|string $value): string`    | [present_unless](https://laravel.com/docs/12.x/validation#rule-present-unless)                 |
| `presentWith(string ...$field): string`                                     | [present_with](https://laravel.com/docs/12.x/validation#rule-present-with)                     |
| `presentWithAll(string ...$field): string`                                  | [present_with_all](https://laravel.com/docs/12.x/validation#rule-present-with-all)             |
| `prohibited(): string`                                                      | [prohibited](https://laravel.com/docs/12.x/validation#rule-prohibited)                         |
| `prohibitedIf(string $anotherField, float\|int\|string $value): string`     | [prohibited_if](https://laravel.com/docs/12.x/validation#rule-prohibited-if)                   |
| `prohibitedIfLogic(Closure\|bool $value): ProhibitedIf`                     | [Rule::prohibitedIf()](https://laravel.com/docs/12.x/validation#rule-prohibited-if)            |
| `prohibitedIfAccepted(string $anotherField): string`                        | [prohibited_if_accepted](https://laravel.com/docs/12.x/validation#rule-prohibited-if-accepted) |
| `prohibitedIfDeclined(string $anotherField): string`                        | [prohibited_if_declined](https://laravel.com/docs/12.x/validation#rule-prohibited-if-declined) |
| `prohibitedUnless(string $anotherField, float\|int\|string $value): string` | [prohibited_unless](https://laravel.com/docs/12.x/validation#rule-prohibited-unless)           |
| `prohibits(string $anotherField): string`                                   | [prohibits](https://laravel.com/docs/12.x/validation#rule-prohibits)                           |
| `regularExpression(string $pattern): string`                                | [regex](https://laravel.com/docs/12.x/validation#rule-regex)                                   |
| `required(): string`                                                        | [required](https://laravel.com/docs/12.x/validation#rule-required)                             |
| `requiredArrayKeys(string ...$key): string`                                 | [required_array_keys](https://laravel.com/docs/12.x/validation#rule-required-array-keys)       |
| `requiredIf(string $anotherField, float\|int\|string $value): string`       | [required_if](https://laravel.com/docs/12.x/validation#rule-required-if)                       |
| `requiredIfLogic(Closure\|bool $value): RequiredIf`                         | [Rule::requiredIf()](https://laravel.com/docs/12.x/validation#rule-required-if)                |
| `requiredIfAccepted(string $anotherField): string`                          | [required_if_accepted](https://laravel.com/docs/12.x/validation#rule-required-if-accepted)     |
| `requiredIfDeclined(string $anotherField): string`                          | [required_if_declined](https://laravel.com/docs/12.x/validation#rule-required-if-declined)     |
| `requiredUnless(string $anotherField, float\|int\|string $value): string`   | [required_unless](https://laravel.com/docs/12.x/validation#rule-required-unless)               |
| `requiredWith(string ...$field): string`                                    | [required_with](https://laravel.com/docs/12.x/validation#rule-required-with)                   |
| `requiredWithAll(string ...$field): string`                                 | [required_with_all](https://laravel.com/docs/12.x/validation#rule-required-with-all)           |
| `requiredWithout(string ...$field): string`                                 | [required_without](https://laravel.com/docs/12.x/validation#rule-required-without)             |
| `requiredWithoutAll(string ...$field): string`                              | [required_without_all](https://laravel.com/docs/12.x/validation#rule-required-without-all)     |
| `same(string $field): string`                                               | [same](https://laravel.com/docs/12.x/validation#rule-same)                                     |
| `size(float\|int $value): string`                                           | [size](https://laravel.com/docs/12.x/validation#rule-size)                                     |
| `sometimes(): string`                                                       | [sometimes](https://laravel.com/docs/12.x/validation#validating-when-present)                  |
| `startsWith(string ...$value): string`                                      | [starts_with](https://laravel.com/docs/12.x/validation#rule-starts-with)                       |
| `string(): string`                                                          | [string](https://laravel.com/docs/12.x/validation#rule-string)                                 |
| `timezone(?string $value = null): string`                                   | [timezone](https://laravel.com/docs/12.x/validation#rule-timezone)                             |
| `ulid(): string`                                                            | [ulid](https://laravel.com/docs/12.x/validation#rule-ulid)                                     |
| `url(string ...$value): string`                                             | [url](https://laravel.com/docs/12.x/validation#rule-url)                                       |
| `uuid(?int $version = null): string`                                        | [uuid](https://laravel.com/docs/12.x/validation#rule-uuid)                                     |
| `unique(string $table, string $column = 'NULL'): Unique`                    | [unique](https://laravel.com/docs/12.x/validation#rule-unique)                                 |
| `uppercase(): string`                                                       | [uppercase](https://laravel.com/docs/12.x/validation#rule-uppercase)                           |
