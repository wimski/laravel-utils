<?php

declare(strict_types=1);

if (! function_exists('config_float')) { // @codeCoverageIgnore
    function config_float(string $key, ?float $default = null): float
    {
        $value = config($key, $default);

        if (! is_float($value)) {
            throw new UnexpectedValueException("The config value for key '{$key}' is expected to be a float.");
        }

        return $value;
    }
}
