<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Validation;

use Closure;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use UnexpectedValueException;
use Wimski\LaravelUtils\Contracts\ValidationRuleIdentifierInterface;

abstract class AbstractValidationRule implements DataAwareRule, ValidationRule
{
    /**
     * @var array<array-key, mixed>
     */
    protected array $data = [];

    public function __construct(
        protected readonly Translator $translator,
    ) {
    }

    abstract public static function getIdentifier(): ValidationRuleIdentifierInterface;
    abstract protected function isValid(mixed $value): bool;

    /**
     * @param array<array-key, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $this->parseData($data);

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->isValid($value)) {
            $fail($this->getMessage($attribute));
        }
    }

    /**
     * @param  array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    protected function parseData(array $data): array
    {
        return $data;
    }

    protected function getMessage(string $attribute): string
    {
        $message = $this->translator->get(
            $this->getMessageKey(),
            $this->getMessageParameters($attribute),
        );

        if (! is_string($message)) {
            throw new UnexpectedValueException("The translation value for key '{$this->getMessageKey()}' is expected to be a string.");
        }

        return $message;
    }

    protected function getMessageKey(): string
    {
        return 'validation.' . static::getIdentifier()->getValue();
    }

    /**
     * @return array{attribute: string}
     */
    protected function getMessageParameters(string $attribute): array
    {
        return [
            'attribute' => $attribute,
        ];
    }
}
