<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Notification Model.
 *
 * Stores notification records for notifiable entities (users, etc).
 *
 * @property string                          $id
 * @property string                          $type
 * @property string                          $notifiable_type
 * @property int                             $notifiable_id
 * @property array                           $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $notifiable
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 */
final class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'data'       => 'array',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method to handle UUID and blameable fields.
     */
    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (self $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
            if (Auth::check()) {
                $user = Auth::user();
                $model->created_by ??= $user->id;
                $model->updated_by ??= $user->id;
            }
        });

        self::updating(function (self $model): void {
            if (Auth::check() && ! $model->isDirty('updated_by')) {
                $user              = Auth::user();
                $model->updated_by = $user->id;
            }
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(self::class), true)) {
            self::deleting(function (self $model): void {
                if (Auth::check()) {
                    $user              = Auth::user();
                    $model->deleted_by = $user->id;
                }
            });

            self::restoring(function (self $model): void {
                $model->deleted_by = null;
                if (Auth::check()) {
                    $user              = Auth::user();
                    $model->updated_by = $user->id;
                }
            });
        }
    }

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    /**
     * Polymorphic relation to the notifiable model.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();

            return true;
        }

        return false;
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        if (! is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();

            return true;
        }

        return false;
    }

    /**
     * Check if the notification is read.
     */
    public function read(): bool
    {
        return ! is_null($this->read_at);
    }

    /**
     * Check if the notification is unread.
     */
    public function unread(): bool
    {
        return is_null($this->read_at);
    }

    // Query scopes

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeByType(Builder $query, string|array $type): Builder
    {
        return is_array($type) ? $query->whereIn('type', $type) : $query->where('type', $type);
    }

    public function scopeByNotifiable(Builder $query, Model $notifiableModel): Builder
    {
        return $query
            ->where('notifiable_type', $notifiableModel->getMorphClass())
            ->where('notifiable_id', $notifiableModel->getKey());
    }
}
