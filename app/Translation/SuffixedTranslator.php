<?php

namespace App\Translation;

use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Custom Translator for MOTAC IRMS Suffixed Language Files.
 * Adds support for suffixed translation keys and fallback, plus caching and logging.
 */
class SuffixedTranslator extends Translator
{
    protected const CACHE_PREFIX = 'motac_translation_';
    protected const CACHE_DURATION = 60; // minutes
    protected array $missingKeys = [];
    protected array $metrics = [
        'total_requests' => 0,
        'cache_hits' => 0,
        'suffixed_hits' => 0,
        'fallback_hits' => 0,
        'missing_keys' => 0,
    ];

    /**
     * Get translation for the given key using suffixed language files & fallback.
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $this->metrics['total_requests']++;
        $locale = $locale ?? $this->locale;
        $cacheKey = $this->generateCacheKey($key, $locale, $replace);

        if ($this->shouldUseCache()) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $this->metrics['cache_hits']++;
                return $cached;
            }
        }

        $translation = $this->getSuffixedTranslation($key, $replace, $locale, $fallback);

        if ($this->shouldUseCache() && $translation !== null) {
            Cache::put($cacheKey, $translation, self::CACHE_DURATION);
        }

        if ($translation === null || $translation === $key) {
            $this->handleMissingTranslation($key, $locale);
        }

        return $translation;
    }

    /**
     * Try to get translation from a suffixed file, fallback to regular key.
     */
    protected function getSuffixedTranslation($key, array $replace, $locale, $fallback)
    {
        $suffix = '_' . $locale;
        $keyWithSuffix = $key . $suffix;
        $translation = parent::get($keyWithSuffix, $replace, $locale, false);

        if ($translation !== null && $translation !== $keyWithSuffix) {
            $this->metrics['suffixed_hits']++;
            return $translation;
        }

        $fallbackTranslation = parent::get($key, $replace, $locale, $fallback);

        if ($fallbackTranslation !== null && $fallbackTranslation !== $key) {
            $this->metrics['fallback_hits']++;
            return $fallbackTranslation;
        }

        $this->metrics['missing_keys']++;
        return $fallbackTranslation;
    }

    /**
     * Log missing translation key (once per request cycle).
     */
    protected function handleMissingTranslation($key, $locale)
    {
        $missingKeyId = $key . '.' . $locale;
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
     */
    protected function generateCacheKey($key, $locale, array $replace)
    {
        $replaceHash = !empty($replace) ? md5(serialize($replace)) : 'no_replace';
        return self::CACHE_PREFIX . md5($key . '.' . $locale . '.' . $replaceHash);
    }

    /**
     * Should translation caching be used?
     */
    protected function shouldUseCache()
    {
        return config('translation.cache_translations', true) &&
            !app()->environment('local') &&
            extension_loaded('redis');
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

    public function getMissingKeys()
    {
        return $this->missingKeys;
    }

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
