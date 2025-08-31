<?php

declare(strict_types=1);

if (! function_exists('config_string')) { // @codeCoverageIgnore
    function config_string(string $key, ?string $default = null): string
    {
        $value = config($key, $default);

        if (! is_string($value)) {
            throw new UnexpectedValueException("The config value for key '{$key}' is expected to be a string.");
        }

        return $value;
    }
}
