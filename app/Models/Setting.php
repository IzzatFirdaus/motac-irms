<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
// Removed: use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Setting Model.
 * Manages application-wide settings, typically as a single row in the database.
 * Assumes a global BlameableObserver handles created_by, updated_by, deleted_by.
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
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
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
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected const SETTINGS_CACHE_KEY = 'application_settings';

    protected $table = 'settings';

    protected $fillable = [
        'site_name', 'site_logo_path', 'default_notification_email_from',
        'default_notification_email_name', 'sms_api_sender', 'sms_api_username',
        'sms_api_password', 'terms_and_conditions_loan', 'terms_and_conditions_email',
        // created_by, updated_by are handled by BlameableObserver
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function getSettingsRecord(): ?self
    {
        return Cache::rememberForever(self::SETTINGS_CACHE_KEY, function (): ?self {
            Log::debug('Cache miss for application settings. Fetching from database.');
            $settings = self::first();
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

    public static function set(string $key, mixed $value): bool
    {
        DB::beginTransaction();
        try {
            $settings = self::firstOrNew([]);
            if (! (in_array($key, $settings->getFillable()) || Schema::hasColumn($settings->getTable(), $key))) {
                Log::error("Attempted to set unknown or non-fillable setting key: {$key}.");
                DB::rollBack();
                return false;
            }
            $settings->{$key} = $value;
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
            throw $e; // Re-throw after logging and rollback
        }
    }

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

    public static function getSmsApiUsername(): ?string { $value = self::get('sms_api_username'); return is_string($value) || $value === null ? $value : null; }
    public static function getSmsApiPassword(): ?string { $value = self::get('sms_api_password'); return is_string($value) || $value === null ? $value : null; }
    public static function getSmsApiSender(): ?string { $value = self::get('sms_api_sender'); return is_string($value) || $value === null ? $value : null; }

    protected static function boot(): void
    {
        parent::boot();
        // Cache clearing logic
        static::saved(function (self $model): void {
            self::clearCache();
        });
        static::deleted(function (self $model): void {
            self::clearCache();
        });
        static::restored(function (self $model): void {
            self::clearCache();
        });
        // Blameable fields (created_by, updated_by, deleted_by) assumed to be handled by a global BlameableObserver.
    }

    protected static function newFactory(): SettingFactory { return SettingFactory::new(); }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }
}
