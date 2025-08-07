<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Setting Model.
 *
 * Manages application-wide settings, typically as a single row in the database.
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
 * @property string $application_name
 * @property string|null $default_system_email
 * @property int $default_loan_period_days
 * @property int $max_loan_items_per_application
 * @property string|null $contact_us_email
 * @property bool $system_maintenance_mode
 * @property string|null $system_maintenance_message
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 */
class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected const SETTINGS_CACHE_KEY = 'application_settings';

    protected $table = 'settings';

    protected $fillable = [
        'site_name', 'site_logo_path',
        'application_name',
        'default_notification_email_from', 'default_notification_email_name',
        'default_system_email',
        'default_loan_period_days',
        'max_loan_items_per_application',
        'contact_us_email',
        'system_maintenance_mode',
        'system_maintenance_message',
        'sms_api_sender', 'sms_api_username', 'sms_api_password',
        'terms_and_conditions_loan', 'terms_and_conditions_email',
    ];

    protected $casts = [
        'default_loan_period_days' => 'integer',
        'max_loan_items_per_application' => 'integer',
        'system_maintenance_mode' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the singleton settings record from cache or DB.
     */
    public static function getSettingsRecord(): ?self
    {
        return Cache::rememberForever(self::SETTINGS_CACHE_KEY, function (): ?self {
            Log::debug('Cache miss for application settings. Fetching from database.');
            $settings = self::first();
            if (!$settings) {
                Log::warning('Application settings record not found in database. Consider running SettingsSeeder.');
            }
            return $settings;
        });
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::getSettingsRecord();
        if ($settings instanceof self && (property_exists($settings, $key) || array_key_exists($key, $settings->getAttributes()) || method_exists($settings, $key))) {
            return $settings->{$key} ?? $default;
        }
        return $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): bool
    {
        DB::beginTransaction();
        try {
            $settings = self::firstOrNew([]);
            if (!in_array($key, $settings->getFillable()) && !Schema::hasColumn($settings->getTable(), $key)) {
                Log::error(sprintf('Attempted to set unknown or non-fillable/non-column setting key: %s.', $key));
                DB::rollBack();
                return false;
            }
            $settings->{$key} = $value;
            $saved = $settings->save();
            DB::commit();
            if ($saved) {
                self::clearCache();
                Log::info(sprintf("Setting '%s' updated successfully.", $key));
            } else {
                Log::error(sprintf("Failed to save setting '%s'.", $key));
            }
            return $saved;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error(sprintf("Error setting setting '%s': ", $key) . $throwable->getMessage(), ['exception' => $throwable, 'key' => $key, 'value' => $value]);
            throw $throwable;
        }
    }

    /**
     * Nullify a setting value.
     */
    public static function forget(string $key): bool
    {
        $settings = self::getSettingsRecord();
        if (!$settings instanceof self) {
            Log::warning(sprintf("Attempted to forget setting '%s', but no settings record exists.", $key));
            return false;
        }
        if (!in_array($key, $settings->getFillable()) && !Schema::hasColumn($settings->getTable(), $key)) {
            Log::warning('Attempted to forget unknown or non-fillable/non-column setting key: ' . $key);
            return false;
        }
        $settings->{$key} = null;
        $saved = $settings->save();
        if ($saved) {
            self::clearCache();
            Log::info(sprintf("Setting '%s' set to null successfully.", $key));
        } else {
            Log::error(sprintf("Failed to set setting '%s' to null.", $key));
        }
        return $saved;
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::SETTINGS_CACHE_KEY);
        Log::debug('Application settings cache cleared.');
    }

    // Useful convenience accessors for specific settings
    public static function getApplicationName(): ?string
    {
        return self::get('application_name', 'MOTAC RMS');
    }
    public static function getDefaultSystemEmail(): ?string
    {
        return self::get('default_system_email');
    }
    public static function getDefaultLoanPeriodDays(): int
    {
        return (int) self::get('default_loan_period_days', 7);
    }
    public static function getMaxLoanItemsPerApplication(): int
    {
        return (int) self::get('max_loan_items_per_application', 5);
    }
    public static function getContactUsEmail(): ?string
    {
        return self::get('contact_us_email');
    }
    public static function isSystemInMaintenanceMode(): bool
    {
        return (bool) self::get('system_maintenance_mode', false);
    }
    public static function getSystemMaintenanceMessage(): ?string
    {
        return self::get('system_maintenance_message');
    }
    public static function getSmsApiUsername(): ?string
    {
        $value = self::get('sms_api_username');
        return is_string($value) || $value === null ? $value : null;
    }
    public static function getSmsApiPassword(): ?string
    {
        $value = self::get('sms_api_password');
        return is_string($value) || $value === null ? $value : null;
    }
    public static function getSmsApiSender(): ?string
    {
        $value = self::get('sms_api_sender');
        return is_string($value) || $value === null ? $value : null;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::saved(function (self $model): void {
            self::clearCache();
        });
        static::deleted(function (self $model): void {
            self::clearCache();
        });
        static::restored(function (self $model): void {
            self::clearCache();
        });
    }

    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
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
}
