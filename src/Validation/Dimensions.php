<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Validation;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Dimensions
{
    protected ?int $minWidth           = null;
    protected ?int $minHeight          = null;
    protected ?int $maxWidth           = null;
    protected ?int $maxHeight          = null;
    protected ?int $width              = null;
    protected ?int $height             = null;
    protected float|string|null $ratio = null;

    public function minWidth(int $value): Dimensions
    {
        $this->minWidth = $value;

        return $this;
    }

    public function minHeight(int $value): Dimensions
    {
        $this->minHeight = $value;

        return $this;
    }

    public function maxWidth(int $value): Dimensions
    {
        $this->maxWidth = $value;

        return $this;
    }

    public function maxHeight(int $value): Dimensions
    {
        $this->maxHeight = $value;

        return $this;
    }

    public function width(int $value): Dimensions
    {
        $this->width = $value;

        return $this;
    }

    public function height(int $value): Dimensions
    {
        $this->height = $value;

        return $this;
    }

    public function ratio(float|string $value): Dimensions
    {
        $this->ratio = $value;

        return $this;
    }

    public function getValue(): string
    {
        /** @var array<string, float|int|null> $vars */
        $vars = get_object_vars($this);

        /** @var Collection<string, float|int> $nonNullVars */
        $nonNullVars = new Collection($vars)->filter();

        return $nonNullVars
            ->map(fn (float|int $value, string $key): string => Str::snake($key) . "={$value}")
            ->implode(',');
    }
}
