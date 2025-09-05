<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Providers;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\PotentiallyTranslatedString;
use Wimski\LaravelUtils\Validation\AbstractValidationRule;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @var array<array-key, class-string<AbstractValidationRule>>
     */
    protected array $rules = [
        //
    ];

    public function boot(): void
    {
        foreach ($this->rules as $rule) {
            ValidatorFacade::extend(
                $rule::getIdentifier()->getValue(),
                function (string $attribute, mixed $value, array $parameters, Validator $validator) use ($rule): bool {
                    /** @var AbstractValidationRule $instance */
                    $instance = $this->app->make($rule);

                    $instance
                        ->setData($parameters)
                        ->validate($attribute, $value, function (string $message) use ($attribute, $validator): PotentiallyTranslatedString {
                            $validator->errors()->add($attribute, $message);

                            return new PotentiallyTranslatedString('', $this->app->make(Translator::class));
                        });

                    // Always return true so the message 'validation.{identifier}'
                    // is not added to the validator's error bag.
                    // The validate method of the rule will handle adding the message when invalid.
                    return true;
                },
            );
        }
    }
}
