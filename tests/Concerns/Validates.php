<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Closure;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;

trait Validates
{
    abstract protected function getValidatorFactory(): ValidatorFactory;

    /**
     * @param  array<string, mixed>                                   $data
     * @param  array<string, array<array-key, Closure|object|string>> $rules
     * @return array<string, array<array-key, string>>
     */
    protected function generateErrors(array $data, array $rules): array
    {
        /** @var array<string, array<array-key, string>> $errors */
        $errors = $this->makeValidator($data, $rules)
                       ->errors()
                       ->toArray();

        return $errors;
    }

    /**
     * @param  array<string, mixed>                                   $data
     * @param  array<string, array<array-key, Closure|object|string>> $rules
     * @return array<string, mixed>
     */
    protected function getValidated(array $data, array $rules): array
    {
        /** @var array<string, mixed> $data */
        $data = $this->makeValidator($data, $rules)->validated();

        return $data;
    }

    /**
     * @param array<string, mixed>                                   $data
     * @param array<string, array<array-key, Closure|object|string>> $rules
     */
    protected function makeValidator(array $data, array $rules): Validator
    {
        return $this->getValidatorFactory()->make($data, $rules);
    }
}
