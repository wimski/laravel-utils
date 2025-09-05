<?php

namespace Wimski\LaravelUtils\Contracts;

interface ValidationRuleIdentifierInterface
{
    public function getValue(): string;
    public function withParams(mixed ...$value): string;
}
