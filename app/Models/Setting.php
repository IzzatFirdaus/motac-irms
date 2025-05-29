<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

// Explicit User import

/**
 * 
 *
 * @property int $id
 * @property string $site_name
 * @property string|null $site_logo_path
 * @property string|null $default_notification_email_from
 * @property string|null $default_notification_email_name
 * @property string|null $sms_api_sender
 * @property string|null $sms_api_username
 * @property string|null $sms_api_password
 * @property string|null $terms_and_conditions_loan
 * @property string|null $terms_and_conditions_email
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\SettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultNotificationEmailFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultNotificationEmailName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSiteLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTermsAndConditionsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTermsAndConditionsLoan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withoutTrashed()
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected const SETTINGS_CACHE_KEY = 'application_settings';

    protected $table = 'settings'; // Corrected: Removed string type

    protected $fillable = [
        'site_name', 'site_logo_path', 'default_notification_email_from',
        'default_notification_email_name', 'sms_api_sender', 'sms_api_username',
        'sms_api_password', 'terms_and_conditions_loan', 'terms_and_conditions_email',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function getSettingsRecord(): ?self // Renamed for clarity from getSmsApiSettingsRecord
    {
        return Cache::rememberForever(self::SETTINGS_CACHE_KEY, function (): ?self {
            Log::debug('Cache miss for application settings. Fetching from database.');
            /** @var self|null $settings */
            $settings = self::first(); // Assuming a single row for all settings
            if (! $settings) {
                Log::warning('Application settings record not found in database.');
            }

            return $settings;
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::getSettingsRecord();
        if ($settings && (property_exists($settings, $key) || array_key_exists($key, $settings->getAttributes()))) {
            return $settings->{$key} ?? $default;
        }

        return $default;
    }

    /** @throws \Throwable */
    public static function set(string $key, mixed $value): bool
    {
        DB::beginTransaction();
        try {
            /** @var self $settings */
            $settings = self::firstOrNew([]); // Get the single settings record or create if not exists

            if (! (in_array($key, $settings->getFillable()) || Schema::hasColumn($settings->getTable(), $key))) {
                Log::error("Attempted to set unknown or non-fillable setting key: {$key}.");
                DB::rollBack();

                return false;
            }

            $settings->{$key} = $value;
            /** @var bool $saved */
            $saved = $settings->save();
            DB::commit();

            if ($saved) {
                self::clearCache();
                Log::info("Setting '{$key}' updated successfully.");
            } else {
                Log::error("Failed to save setting '{$key}'.");
            }

            return $saved;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error setting setting '{$key}': ".$e->getMessage(), ['exception' => $e, 'key' => $key, 'value' => $value]);
            throw $e;
        }
    }

    /** @throws \Throwable */
    public static function forget(string $key): bool
    {
        $settings = self::getSettingsRecord();
        if (! $settings) {
            Log::warning("Attempted to forget setting '{$key}', but no settings record exists.");

            return false;
        }
        if (! (in_array($key, $settings->getFillable()) || Schema::hasColumn($settings->getTable(), $key))) {
            Log::warning("Attempted to forget unknown or non-fillable setting key: {$key}");

            return false;
        }

        $settings->{$key} = null;
        /** @var bool $saved */
        $saved = $settings->save();

        if ($saved) {
            self::clearCache();
            Log::info("Setting '{$key}' set to null successfully.");
        } else {
            Log::error("Failed to set setting '{$key}' to null.");
        }

        return $saved;
    }

    public static function clearCache(): void
    {
        Cache::forget(self::SETTINGS_CACHE_KEY);
        Log::debug('Application settings cache cleared.');
    }

    // Specific getters remain useful
    public static function getSmsApiUsername(): ?string
    { /* ... from your file ... */ $value = self::get('sms_api_username');

        return is_string($value) || $value === null ? $value : null;
    }

    public static function getSmsApiPassword(): ?string
    { /* ... from your file ... */ $value = self::get('sms_api_password');

        return is_string($value) || $value === null ? $value : null;
    }

    public static function getSmsApiSender(): ?string
    { /* ... from your file ... */ $value = self::get('sms_api_sender');

        return is_string($value) || $value === null ? $value : null;
    }

    protected static function boot(): void
    {
        parent::boot();
        // ... (Standard boot logic for audit stamps and cache clearing from your file) ...
        static::creating(function (self $model): void {
            if (Auth::check()) {
                /** @var User $currentUser */
                $currentUser = Auth::user();
                if (is_null($model->created_by)) {
                    $model->created_by = $currentUser->id;
                }
                if (is_null($model->updated_by)) {
                    $model->updated_by = $currentUser->id;
                }
            }
        });
        static::saved(function (self $model): void { // Changed to saved to catch create and update
            self::clearCache();
        });
        static::deleted(function (self $model): void { // Changed to deleted
            self::clearCache();
        });
        static::restored(function (self $model): void {
            self::clearCache();
        });

        // Audit stamps for update, delete, restore
        static::updating(function (self $model): void {
            if (Auth::check() && ! $model->isDirty('updated_by')) {
                /** @var User $currentUser */
                $currentUser = Auth::user();
                $model->updated_by = $currentUser->id;
            }
        });
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function (self $model): void {
                if (Auth::check() && property_exists($model, 'deleted_by') && ! $model->isDirty('deleted_by')) {
                    /** @var User $currentUser */
                    $currentUser = Auth::user();
                    $model->deleted_by = $currentUser->id;
                    $model->saveQuietly();
                }
            });
            static::restoring(function (self $model): void {
                if (property_exists($model, 'deleted_by')) {
                    $model->deleted_by = null;
                }
                if (Auth::check() && ! $model->isDirty('updated_by')) {
                    /** @var User $currentUser */
                    $currentUser = Auth::user();
                    $model->updated_by = $currentUser->id;
                }
            });
        }
    }

    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }

    /** @return BelongsTo<User, Setting> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, Setting> */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** @return BelongsTo<User, Setting> */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
