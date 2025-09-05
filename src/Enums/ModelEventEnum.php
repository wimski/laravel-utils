<?php

declare(strict_types=1);

namespace Wimski\LaravelUtils\Enums;

use Illuminate\Database\Eloquent\Model;

enum ModelEventEnum: string
{
    /**
     * @see \Illuminate\Database\Eloquent\Concerns\HasEvents::getObservableEvents()
     */
    case CREATED        = 'created';
    case CREATING       = 'creating';
    case DELETED        = 'deleted';
    case DELETING       = 'deleting';
    case FORCE_DELETED  = 'forceDeleted';
    case FORCE_DELETING = 'forceDeleting';
    case REPLICATING    = 'replicating';
    case RESTORED       = 'restored';
    case RESTORING      = 'restoring';
    case RETRIEVED      = 'retrieved';
    case SAVED          = 'saved';
    case SAVING         = 'saving';
    case UPDATED        = 'updated';
    case UPDATING       = 'updating';

    /**
     * @param class-string<Model> $class
     */
    public function stringForClass(string $class): string
    {
        return "eloquent.{$this->value}: {$class}";
    }
}
