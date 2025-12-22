<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'format',
        'file_path',
        'parameters',
        'generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parameters' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the download URL for the report.
     *
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('reports.download', $this->id);
    }

    /**
     * Scope a query to only include reports of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include reports in a specific format.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $format
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfFormat($query, $format)
    {
        return $query->where('format', $format);
    }

    /**
     * Scope a query to only include reports generated within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $from
     * @param  mixed  $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGeneratedBetween($query, $from, $to = null)
    {
        if (is_null($to)) {
            $to = now();
        }

        return $query->whereBetween('generated_at', [$from, $to]);
    }
}
