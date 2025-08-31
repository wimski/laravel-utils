# Enum Translations

```php
enum MyEnum
{
    case SOME_CASE;
}
```

```php
// /lang/{code}/enum.php

return [
    MyEnum::class => [
        MyEnum::SOME_CASE->name => 'singular|plural',    
    ],
];
```
