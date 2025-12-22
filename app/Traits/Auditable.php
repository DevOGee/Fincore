<?php

namespace App\Traits;

use App\Services\AuditService;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            AuditService::logCreate($model);
        });

        static::updated(function ($model) {
            $oldValues = $model->getOriginal();
            AuditService::logUpdate($model, $oldValues);
        });

        static::deleted(function ($model) {
            AuditService::logDelete($model);
        });
    }
}
