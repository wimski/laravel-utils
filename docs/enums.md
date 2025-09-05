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

## `ModelEventEnum`

This enum contains all model event types as defined in
[`Illuminate\Database\Eloquent\Concerns\HasEvents::getObservableEvents()`](https://github.com/laravel/framework/blob/12.x/src/Illuminate/Database/Eloquent/Concerns/HasEvents.php#L131-L141).
It also has a method to create a string version
of automatically generated events.

https://laravel.com/docs/12.x/eloquent#events

## Usage

```php
use App\Events\MyModelCreatedEvent;
use Illuminate\Database\Eloquent\Model
use Wimski\LaravelUtils\Enums\ModelEventEnum;

class MyModel extends Model
{
    protected $dispatchesEvents = [
        ModelEventEnum::CREATED->value => MyModelCreatedEvent::class,
    ];
}
```

```php
use App\Listeners\DoSomethingOnMyModelSaveListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Wimski\LaravelUtils\Enums\ModelEventEnum;

class MyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            ModelEventEnum::SAVED->stringForClass(MyModel::class), // "eloquent.saved: App\Models\MyModel"
            DoSomethingOnMyModelSaveListener:class,
        );
    }
}
```
