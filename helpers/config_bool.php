<?php

declare(strict_types=1);

if (! function_exists('config_bool')) { // @codeCoverageIgnore
    function config_bool(string $key, ?bool $default = null): bool
    {
        $value = config($key, $default);

        if (! is_bool($value)) {
            throw new UnexpectedValueException("The config value for key '{$key}' is expected to be a boolean.");
        }

        return $value;
    }
}
