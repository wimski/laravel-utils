<?php

declare(strict_types=1);

namespace Tests\Resources\Rules;

use Tests\Resources\ValidationRuleEnum;
use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;
use Wimski\LaravelUtils\Validation\AbstractValidationRule;

class BasicRule extends AbstractValidationRule
{
    public static function getIdentifier(): ValidationRuleIdentifierInterface
    {
        return ValidationRuleEnum::BASIC;
    }

    protected function isValid(mixed $value): bool
    {
        return $value === true;
    }
}
