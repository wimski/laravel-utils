<?php

declare(strict_types=1);

if (! function_exists('config_array')) { // @codeCoverageIgnore
    /**
     * @param  array<array-key, mixed>|null $default
     * @return array<array-key, mixed>
     */
    function config_array(string $key, ?array $default = null): array
    {
        $value = config($key, $default);

        if (! is_array($value)) {
            throw new UnexpectedValueException("The config value for key '{$key}' is expected to be an array.");
        }

        return $value;
    }
}
