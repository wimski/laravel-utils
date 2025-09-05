<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Concerns;

use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;

trait AddsParamsToValidationRule
{
    abstract protected function getIdentifier(): ValidationRuleIdentifierInterface;

    public function withParams(mixed ...$value): string
    {
        return $this->getIdentifier()->getValue() . ':' . implode(',', $value);
    }
}
