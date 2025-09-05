<?php

declare(strict_types=1);

namespace Tests\Resources\Rules;

use Tests\Resources\ValidationRuleEnum;
use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;
use Wimski\LaravelUtils\Validation\AbstractValidationRule;

class DataRule extends AbstractValidationRule
{
    public static function getIdentifier(): ValidationRuleIdentifierInterface
    {
        return ValidationRuleEnum::DATA;
    }

    protected function isValid(mixed $value): bool
    {
        $data = $this->data[0] ?? null;

        if ($data === null) {
            return false;
        }

        return $value === $data;
    }

    protected function parseData(array $data): array
    {
        return array_map(fn (string $item): string => "xxx_{$item}", $data);
    }
}
