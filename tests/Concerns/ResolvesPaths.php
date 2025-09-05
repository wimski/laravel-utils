<?php

declare(strict_types=1);

namespace Tests\Concerns;

trait ResolvesPaths
{
    protected function resolveResourcePath(string $path): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'Resources',
            $path,
        ]);
    }
}
