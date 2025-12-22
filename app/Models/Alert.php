<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    const TYPE_BUDGET = 'budget';
    const TYPE_EXPENSE = 'expense';
    const TYPE_LOAN = 'loan';

    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_CRITICAL = 'critical';

    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'title',
        'message',
        'alertable_type',
        'alertable_id',
        'metadata',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => $this->freshTimestamp(),
        ]);
    }

    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public static function createBudgetAlert($userId, $severity, $title, $message, $alertable = null, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_BUDGET,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'alertable_type' => $alertable ? get_class($alertable) : null,
            'alertable_id' => $alertable ? $alertable->id : null,
            'metadata' => $metadata,
        ]);
    }

    public static function createExpenseAlert($userId, $severity, $title, $message, $alertable = null, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_EXPENSE,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'alertable_type' => $alertable ? get_class($alertable) : null,
            'alertable_id' => $alertable ? $alertable->id : null,
            'metadata' => $metadata,
        ]);
    }

    public static function createLoanAlert($userId, $title, $message, $alertable = null, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_LOAN,
            'severity' => self::SEVERITY_CRITICAL,
            'title' => $title,
            'message' => $message,
            'alertable_type' => $alertable ? get_class($alertable) : null,
            'alertable_id' => $alertable ? $alertable->id : null,
            'metadata' => $metadata,
        ]);
    }
}
