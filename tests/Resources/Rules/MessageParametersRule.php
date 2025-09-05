<?php

declare(strict_types=1);

namespace Tests\Resources\Rules;

use Tests\Resources\ValidationRuleEnum;
use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;
use Wimski\LaravelUtils\Validation\AbstractValidationRule;

class MessageParametersRule extends AbstractValidationRule
{
    public static function getIdentifier(): ValidationRuleIdentifierInterface
    {
        return ValidationRuleEnum::MESSAGE_PARAMETERS;
    }

    protected function isValid(mixed $value): bool
    {
        return false;
    }

    protected function getMessageParameters(string $attribute): array
    {
        return array_merge(parent::getMessageParameters($attribute), [
            'length' => strlen($attribute),
        ]);
    }
}
