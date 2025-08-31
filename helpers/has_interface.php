<?php

declare(strict_types=1);

if (! function_exists('has_interface')) { // @codeCoverageIgnore
    /**
     * @param object|class-string $class
     * @param class-string        $interface
     */
    function has_interface(object|string $class, string $interface, bool $autoload = true): bool
    {
        $interfaces = class_implements($class, $autoload);

        if (! $interfaces) {
            return false;
        }

        return in_array($interface, $interfaces, true);
    }
}
