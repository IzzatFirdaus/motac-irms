<?php

namespace App\Translation;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\Translator;

/**
 * Custom Translator for MOTAC IRMS Suffixed Language Files.
 * Adds support for suffixed translation files, caching, and logging.
 */
class SuffixedTranslator extends Translator
{
    protected const CACHE_PREFIX = 'motac_translation_';

    protected const CACHE_DURATION = 60; // minutes

    protected array $missingKeys = [];

    protected array $metrics = [
        'total_requests' => 0,
        'cache_hits'     => 0,
        'suffixed_hits'  => 0, // For parity with old metrics
        'fallback_hits'  => 0,
        'missing_keys'   => 0,
    ];

    /**
     * Get translation for the given key using suffixed language files & fallback.
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $this->metrics['total_requests']++;
        $locale   = $locale ?? $this->locale;
        $cacheKey = $this->generateCacheKey($key, $locale, $replace);

        if ($this->shouldUseCache()) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $this->metrics['cache_hits']++;

                return $cached;
            }
        }

        // Fetch the key as-is, because the loader already loads the correct file (app_ms.php), so just ask for 'system_name'
        $translation = parent::get($key, $replace, $locale, $fallback);

        if ($this->shouldUseCache() && $translation !== null) {
            Cache::put($cacheKey, $translation, self::CACHE_DURATION);
        }

        if ($translation === null || $translation === $key) {
            $this->handleMissingTranslation($key, $locale);
        }

        return $translation;
    }

    /**
     * Log missing translation key (once per request cycle).
     */
    protected function handleMissingTranslation($key, $locale)
    {
        $missingKeyId = $key . '.' . $locale;
        if (! in_array($missingKeyId, $this->missingKeys)) {
            $this->missingKeys[] = $missingKeyId;
            if (config('translation.log_missing_keys', true)) {
                Log::warning('Missing translation key detected', [
                    'key'         => $key,
                    'locale'      => $locale,
                    'request_url' => request()->url()       ?? 'N/A',
                    'user_agent'  => request()->userAgent() ?? 'N/A',
                ]);
            }
        }
    }

    /**
     * Generate a cache key for the translation request.
     */
    protected function generateCacheKey($key, $locale, array $replace)
    {
        $replaceHash = ! empty($replace) ? md5(serialize($replace)) : 'no_replace';

        return self::CACHE_PREFIX . md5($key . '.' . $locale . '.' . $replaceHash);
    }

    /**
     * Should translation caching be used?
     */
    protected function shouldUseCache()
    {
        return config('translation.cache_translations', true) && ! app()->environment('local') && extension_loaded('redis');
    }

    /** Get translation performance metrics for debugging. */
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

    /** Clear translation cache. */
    public function clearCache()
    {
        try {
            $pattern = self::CACHE_PREFIX . '*';
            // Use cache store to access Redis connection when available
            $store = Cache::store();
            if (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                $keys  = $redis->keys($pattern);
                if (! empty($keys)) {
                    // phpcs:ignore Generic.Commenting.Todo.Found
                    // Some Redis clients expect individual deletes
                    $redis->del($keys);
                    Log::info('Translation cache cleared successfully.', ['keys_cleared' => count($keys)]);

                    return true;
                }

                return true;
            }

            // Fallback: if Redis not available, nothing to clear here
            Log::info('Translation cache clear skipped: Redis store not available.');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear translation cache: ' . $e->getMessage());

            return false;
        }
    }

    public function getMissingKeys()
    {
        return $this->missingKeys;
    }

    public function resetMetrics()
    {
        $this->metrics = [
            'total_requests' => 0,
            'cache_hits'     => 0,
            'suffixed_hits'  => 0,
            'fallback_hits'  => 0,
            'missing_keys'   => 0,
        ];
        $this->missingKeys = [];
    }
}
