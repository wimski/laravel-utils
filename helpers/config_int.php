<?php

declare(strict_types=1);

if (! function_exists('config_int')) { // @codeCoverageIgnore
    function config_int(string $key, ?int $default = null): int
    {
        $value = config($key, $default);

        if (! is_int($value)) {
            throw new UnexpectedValueException("The config value for key '{$key}' is expected to be an integer.");
        }

        return $value;
    }
}
