<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Concerns;

use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;

trait AddsParamsToValidationRule
{
    abstract protected function getIdentifier(): ValidationRuleIdentifierInterface;

    public function withParams(bool|float|int|string ...$value): string
    {
        $values = array_map(function (bool|float|int|string $var): string {
            if (is_string($var)) {
                return $var;
            }

            if (is_float($var) || is_int($var)) {
                return (string) $var;
            }

            return $var === true ? 'true' : 'false';
        }, $value);

        return $this->getIdentifier()->getValue() . ':' . implode(',', $values);
    }
}
