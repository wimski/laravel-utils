<?php

declare(strict_types=1);

if (! function_exists('has_trait')) { // @codeCoverageIgnore
    /**
     * @param object|class-string $class
     * @param class-string        $trait
     */
    function has_trait(object|string $class, string $trait): bool
    {
        $traits = class_uses_recursive($class);

        return in_array($trait, $traits, true);
    }
}
