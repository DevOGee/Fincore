<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public static function log(
        string $actionType,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logCreate(Model $model): AuditLog
    {
        return self::log(
            'create',
            class_basename($model),
            $model->id,
            null,
            $model->toArray()
        );
    }

    public static function logUpdate(Model $model, array $oldValues): AuditLog
    {
        return self::log(
            'update',
            class_basename($model),
            $model->id,
            $oldValues,
            $model->toArray()
        );
    }

    public static function logDelete(Model $model): AuditLog
    {
        return self::log(
            'delete',
            class_basename($model),
            $model->id,
            $model->toArray(),
            null
        );
    }

    public static function logLogin(): AuditLog
    {
        return self::log('login', 'User', auth()->id());
    }

    public static function logLogout(): AuditLog
    {
        return self::log('logout', 'User', auth()->id());
    }
}
