# Helpers

This packages contains several helper functions
that simplify common tasks.

## Config

Although the majority of the time you should inject
`Illuminate\Contracts\Config\Repository`, but
the `config()` helper can be useful in certain
situations such as service providers or tests.
The problem is though that the return value is always mixed.
This package makes the following helpers available
that guarantee a specific return type and throws an exception
if the configured value is not of the requested type.

### Array
```php
/**
 * @param  array<array-key, mixed>|null $default
 * @return array<array-key, mixed>
 */
function config_array(string $key, ?array $default = null): array;
```

### Boolean
```php
function config_bool(string $key, ?bool $default = null): bool;
```

### Float
```php
function config_float(string $key, ?float $default = null): float;
```

### Integer
```php
function config_int(string $key, ?int $default = null): int;
```

### String
```php
function config_string(string $key, ?string $default = null): string;
```
