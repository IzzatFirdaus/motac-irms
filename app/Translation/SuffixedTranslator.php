<?php

namespace App\Translation;

use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Custom Translator for MOTAC IRMS Suffixed Language Files
 *
 * This translator extends Laravel's default translator to support suffixed language files.
 * When a translation key is requested, it automatically appends the current locale as a suffix
 * and attempts to load from the appropriate suffixed file.
 *
 * Example:
 * - Key: 'forms.label_full_name'
 * - Locale: 'en'
 * - Looks for: 'forms_en.label_full_name' in forms_en.php
 * - Fallback: 'forms.label_full_name' in forms.php (if exists)
 *
 * Features:
 * - Automatic locale suffix appending
 * - Graceful fallback to non-suffixed keys
 * - Performance caching for frequently used translations
 * - Enhanced error handling and logging
 * - Missing translation key tracking
 *
 * @package App\Translation
 * @author MOTAC ICT Team
 * @since 1.0.0
 */
class SuffixedTranslator extends Translator
{
    /**
     * Cache prefix for translation keys
     */
    protected const CACHE_PREFIX = 'motac_translation_';

    /**
     * Cache duration in minutes for translations
     */
    protected const CACHE_DURATION = 60;

    /**
     * Set to track missing translation keys to avoid repeated logging
     */
    protected array $missingKeys = [];

    /**
     * Performance metrics
     */
    protected array $metrics = [
        'total_requests' => 0,
        'cache_hits' => 0,
        'suffixed_hits' => 0,
        'fallback_hits' => 0,
        'missing_keys' => 0,
    ];

    /**
     * Get the translation for the given key using suffixed language files.
     *
     * This method implements the core functionality of the suffixed translation system.
     * It follows this priority order:
     * 1. Cached translation (if caching is enabled)
     * 2. Suffixed key in suffixed file (e.g., 'forms_en.label_full_name')
     * 3. Regular key in regular file (fallback)
     * 4. Missing key handling
     *
     * @param string $key The translation key (e.g., 'forms.label_full_name')
     * @param array $replace Array of replacements for dynamic values
     * @param string|null $locale The locale to use (default: current app locale)
     * @param bool $fallback Whether to fallback to default locale
     * @return string|array|null The translated string, array, or null if not found
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        // Track performance metrics
        $this->metrics['total_requests']++;

        // Determine the locale to use
        $locale = $locale ?? $this->locale;

        // Generate cache key for this translation request
        $cacheKey = $this->generateCacheKey($key, $locale, $replace);

        // Check cache first (if enabled and not in local environment)
        if ($this->shouldUseCache()) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $this->metrics['cache_hits']++;
                return $cached;
            }
        }

        // Attempt to get translation using suffixed approach
        $translation = $this->getSuffixedTranslation($key, $replace, $locale, $fallback);

        // Cache the result if caching is enabled
        if ($this->shouldUseCache() && $translation !== null) {
            Cache::put($cacheKey, $translation, self::CACHE_DURATION);
        }

        // Log missing keys if enabled
        if ($translation === null || $translation === $key) {
            $this->handleMissingTranslation($key, $locale);
        }

        return $translation;
    }

    /**
     * Get translation using the suffixed approach with fallback.
     *
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @param bool $fallback
     * @return string|array|null
     */
    protected function getSuffixedTranslation($key, array $replace, $locale, $fallback)
    {
        $suffix = '_' . $locale;

        // Try suffixed key first (e.g., 'forms_en.label_full_name')
        $keyWithSuffix = $key . $suffix;
        $translation = parent::get($keyWithSuffix, $replace, $locale, false);

        // If suffixed translation found, return it
        if ($translation !== null && $translation !== $keyWithSuffix) {
            $this->metrics['suffixed_hits']++;
            return $translation;
        }

        // Fallback to regular key (e.g., 'forms.label_full_name')
        $fallbackTranslation = parent::get($key, $replace, $locale, $fallback);

        if ($fallbackTranslation !== null && $fallbackTranslation !== $key) {
            $this->metrics['fallback_hits']++;
            return $fallbackTranslation;
        }

        // No translation found
        $this->metrics['missing_keys']++;
        return $fallbackTranslation;
    }

    /**
     * Handle missing translation keys.
     *
     * @param string $key
     * @param string $locale
     * @return void
     */
    protected function handleMissingTranslation($key, $locale)
    {
        $missingKeyId = $key . '.' . $locale;

        // Only log each missing key once per request cycle
        if (!in_array($missingKeyId, $this->missingKeys)) {
            $this->missingKeys[] = $missingKeyId;

            if (config('translation.log_missing_keys', true)) {
                Log::warning("Missing translation key detected", [
                    'key' => $key,
                    'locale' => $locale,
                    'suffix_attempted' => $key . '_' . $locale,
                    'request_url' => request()->url() ?? 'N/A',
                    'user_agent' => request()->userAgent() ?? 'N/A',
                ]);
            }
        }
    }

    /**
     * Generate a cache key for the translation request.
     *
     * @param string $key
     * @param string $locale
     * @param array $replace
     * @return string
     */
    protected function generateCacheKey($key, $locale, array $replace)
    {
        $replaceHash = !empty($replace) ? md5(serialize($replace)) : 'no_replace';
        return self::CACHE_PREFIX . md5($key . '.' . $locale . '.' . $replaceHash);
    }

    /**
     * Determine if translation caching should be used.
     *
     * @return bool
     */
    protected function shouldUseCache()
    {
        return config('translation.cache_translations', true) &&
               !app()->environment('local') &&
               extension_loaded('redis');
    }

    /**
     * Get performance metrics for monitoring and debugging.
     *
     * @return array
     */
    public function getMetrics()
    {
        return array_merge($this->metrics, [
            'cache_hit_rate' => $this->metrics['total_requests'] > 0
                ? round(($this->metrics['cache_hits'] / $this->metrics['total_requests']) * 100, 2)
                : 0,
            'suffixed_hit_rate' => $this->metrics['total_requests'] > 0
                ? round(($this->metrics['suffixed_hits'] / $this->metrics['total_requests']) * 100, 2)
                : 0,
            'missing_key_rate' => $this->metrics['total_requests'] > 0
                ? round(($this->metrics['missing_keys'] / $this->metrics['total_requests']) * 100, 2)
                : 0,
        ]);
    }

    /**
     * Clear translation cache.
     *
     * @return bool
     */
    public function clearCache()
    {
        try {
            $pattern = self::CACHE_PREFIX . '*';
            $keys = Cache::getRedis()->keys($pattern);

            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
                Log::info('Translation cache cleared successfully.', ['keys_cleared' => count($keys)]);
                return true;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear translation cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get list of missing translation keys for this request.
     *
     * @return array
     */
    public function getMissingKeys()
    {
        return $this->missingKeys;
    }

    /**
     * Reset metrics (useful for testing).
     *
     * @return void
     */
    public function resetMetrics()
    {
        $this->metrics = [
            'total_requests' => 0,
            'cache_hits' => 0,
            'suffixed_hits' => 0,
            'fallback_hits' => 0,
            'missing_keys' => 0,
        ];
        $this->missingKeys = [];
    }
}
