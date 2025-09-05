<?php

declare(strict_types=1);

namespace Tests\Resources;

use Wimski\LaravelUtils\Concerns\AddsParamsToValidationRule;
use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;

enum ValidationRuleEnum: string implements ValidationRuleIdentifierInterface
{
    use AddsParamsToValidationRule;

    case BASIC              = 'basic';
    case DATA               = 'data';
    case MESSAGE            = 'message';
    case MESSAGE_KEY        = 'message_key';
    case MESSAGE_PARAMETERS = 'message_parameters';
    case TRANSLATION_ERROR  = 'translation_error';

    protected function getIdentifier(): ValidationRuleIdentifierInterface
    {
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
