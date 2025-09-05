<?php

declare(strict_types=1);

namespace Tests\Resources\Rules;

use Tests\Resources\ValidationRuleEnum;
use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;
use Wimski\LaravelUtils\Validation\AbstractValidationRule;

class MessageKeyRule extends AbstractValidationRule
{
    public static function getIdentifier(): ValidationRuleIdentifierInterface
    {
        return ValidationRuleEnum::MESSAGE_KEY;
    }

    protected function isValid(mixed $value): bool
    {
        return false;
    }

    protected function getMessageKey(): string
    {
        return parent::getMessageKey() . '.nested';
    }
}
