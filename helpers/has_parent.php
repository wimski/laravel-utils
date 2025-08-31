<?php

declare(strict_types=1);

if (! function_exists('has_parent')) { // @codeCoverageIgnore
    /**
     * @param object|class-string $class
     * @param class-string        $parent
     */
    function has_parent(object|string $class, string $parent, bool $autoload = true): bool
    {
        $parents = class_parents($class, $autoload);

        if (! $parents) {
            return false;
        }

        return in_array($parent, $parents, true);
    }
}
