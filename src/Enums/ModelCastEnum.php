<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Enums;

/**
 * @link https://laravel.com/docs/12.x/eloquent-mutators#attribute-casting
 */
enum ModelCastEnum: string
{
    case ARRAY                = 'array';
    case BOOLEAN              = 'boolean';
    case COLLECTION           = 'collection';
    case DOUBLE               = 'double';
    case ENCRYPTED            = 'encrypted';
    case ENCRYPTED_ARRAY      = 'encrypted:array';
    case ENCRYPTED_COLLECTION = 'encrypted:collection';
    case ENCRYPTED_OBJECT     = 'encrypted:object';
    case FLOAT                = 'float';
    case HASHED               = 'hashed';
    case INTEGER              = 'integer';
    case JSON_UNICODE         = 'json:unicode';
    case OBJECT               = 'object';
    case REAL                 = 'real';
    case STRING               = 'string';
    case TIMESTAMP            = 'timestamp';

    public static function DATE(?string $format = null): string
    {
        return $format ? "date:{$format}" : 'date';
    }

    public static function DATETIME(?string $format = null): string
    {
        return $format ? "datetime:{$format}" : 'datetime';
    }

    public static function DECIMAL(int $precision): string
    {
        return "decimal:{$precision}";
    }

    public static function IMMUTABLE_DATE(?string $format = null): string
    {
        return $format ? "immutable_date:{$format}" : 'immutable_date';
    }

    public static function IMMUTABLE_DATETIME(?string $format = null): string
    {
        return $format ? "immutable_datetime:{$format}" : 'immutable_datetime';
    }
}
