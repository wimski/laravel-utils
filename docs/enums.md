# Enums

This packages contains several enums
that represent fixed collections
of hard-coded values that are used in
the Laravel framework.

## `ModelCastEnum`

This enum contains all cast types
for model attributes.
Simple casts are just cases,
where more complex ones (with parameters)
have static methods.

https://laravel.com/docs/12.x/eloquent-mutators#attribute-casting

### Usage

```php
use Illuminate\Database\Eloquent\Model
use Wimski\LaravelUtils\Enums\ModelCastEnum;

/**
 * @property int   $count
 * @property float $percentage
 */
class MyModel extends Model
{
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'count'      => ModelCastEnum::INTEGER->value,
            'percentage' => ModelCastEnum::DECIMAL(2),
        ]);
    }
}
```
