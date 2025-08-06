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
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array<array-key, mixed> $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Model $notifiable
 * @property-read \App\Models\User|null $updater
 *
 * @method static Builder<static>|Notification byNotifiable(\Illuminate\Database\Eloquent\Model $notifiableModel)
 * @method static Builder<static>|Notification byType(array|string $type)
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Notification newModelQuery()
 * @method static Builder<static>|Notification newQuery()
 * @method static Builder<static>|Notification onlyTrashed()
 * @method static Builder<static>|Notification query()
 * @method static Builder<static>|Notification read()
 * @method static Builder<static>|Notification unread()
 * @method static Builder<static>|Notification whereCreatedAt($value)
 * @method static Builder<static>|Notification whereCreatedBy($value)
 * @method static Builder<static>|Notification whereData($value)
 * @method static Builder<static>|Notification whereDeletedAt($value)
 * @method static Builder<static>|Notification whereDeletedBy($value)
 * @method static Builder<static>|Notification whereId($value)
 * @method static Builder<static>|Notification whereNotifiableId($value)
 * @method static Builder<static>|Notification whereNotifiableType($value)
 * @method static Builder<static>|Notification whereReadAt($value)
 * @method static Builder<static>|Notification whereType($value)
 * @method static Builder<static>|Notification whereUpdatedAt($value)
 * @method static Builder<static>|Notification whereUpdatedBy($value)
 * @method static Builder<static>|Notification withTrashed()
 * @method static Builder<static>|Notification withoutTrashed()
 *
 * @mixin \Eloquent
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
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (self $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }

            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                $model->created_by ??= $user->id;
                $model->updated_by ??= $user->id;
            }
        });

        self::updating(function (self $model): void {
            if (Auth::check() && ! $model->isDirty('updated_by')) {
                /** @var User $user */
                $user = Auth::user();
                $model->updated_by = $user->id;
            }
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(self::class))) {
            self::deleting(function (self $model): void {
                if (Auth::check() && property_exists($model, 'deleted_by') && is_null($model->deleted_by)) {
                    /** @var User $user */
                    $user = Auth::user();
                    $model->deleted_by = $user->id;
                }
            });

            self::restoring(function (self $model): void {
                if (property_exists($model, 'deleted_by')) {
                    $model->deleted_by = null;
                }

                if (Auth::check() && ! $model->isDirty('updated_by')) {
                    /** @var User $user */
                    $user = Auth::user();
                    $model->updated_by = $user->id;
                }
            });
        }
    }

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    /** @return MorphTo<\Illuminate\Database\Eloquent\Model, \App\Models\Notification> */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<\App\Models\User, \App\Models\Notification> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<\App\Models\User, \App\Models\Notification> */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return BelongsTo<\App\Models\User, \App\Models\Notification> */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
            return true;
        }

        return false;
    }

    public function markAsUnread(): bool
    {
        if (! is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
            return true;
        }

        return false;
    }

    public function read(): bool
    {
        return ! is_null($this->read_at);
    }

    public function unread(): bool
    {
        return is_null($this->read_at);
    }

    /** @param Builder<Notification> $query */
    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    /** @param Builder<Notification> $query */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /** @param Builder<Notification> $query */
    public function scopeByType(Builder $query, string|array $type): Builder
    {
        return is_array($type) ? $query->whereIn('type', $type) : $query->where('type', $type);
    }

    /** @param Builder<Notification> $query */
    public function scopeByNotifiable(Builder $query, Model $notifiableModel): Builder
    {
        return $query
            ->where('notifiable_type', $notifiableModel->getMorphClass())
            ->where('notifiable_id', $notifiableModel->getKey());
    }
}
