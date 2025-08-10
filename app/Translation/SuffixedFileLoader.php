<?php

namespace App\Translation;

use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

/**
 * Custom FileLoader to support suffixed translation files, e.g. app_ms.php, dashboard_en.php.
 * Falls back to standard files if suffixed ones do not exist.
 */
class SuffixedFileLoader extends FileLoader
{
    /**
     * The base path to the language files.
     * @var string
     */
    protected $basePath;

    /**
     * Constructor for SuffixedFileLoader.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param string $path
     */
    public function __construct(Filesystem $files, $path)
    {
        parent::__construct($files, $path);
        $this->basePath = $path; // Store the language path for internal use.
    }

    /**
     * Load the messages for the given locale and group.
     *
     * This method first attempts to load the suffixed file (e.g. app_ms.php),
     * then falls back to the standard Laravel convention (e.g. app.php).
     */
    public function load($locale, $group, $namespace = null)
    {
        // Debug log to trace loader calls and parameters
        Log::debug("[SuffixedFileLoader] load() called: locale={$locale}, group={$group}, namespace=" . ($namespace ?? 'null'));

        // Attempt to load suffixed translation file
        $lines = $this->loadSuffixed($locale, $group, $namespace);

        if (is_null($lines)) {
            Log::debug("[SuffixedFileLoader] Fallback to default for group: '{$group}', locale: '{$locale}'");
            // Fallback to standard file if suffixed file not found
            $lines = parent::load($locale, $group, $namespace);
        } else {
            Log::debug("[SuffixedFileLoader] Found suffixed file for group: '{$group}', locale: '{$locale}'");
        }

        return $lines ?? [];
    }

    /**
     * Attempt to load a suffixed language file, like app_ms.php or dashboard_en.php.
     *
     * @param string $locale The locale (e.g. 'en' or 'ms').
     * @param string $group  The translation group/file (e.g. 'app', 'dashboard').
     * @param string|null $namespace The optional namespace.
     * @return array|null The loaded lines if file exists, otherwise null.
     */
    protected function loadSuffixed($locale, $group, $namespace = null)
    {
        $path = $this->basePath;
        if ($namespace && $namespace !== '*') {
            $path = $this->addNamespacePath($namespace, $path);
        }

        // Construct expected suffixed filename
        $file = "{$path}/{$locale}/{$group}_{$locale}.php";
        Log::debug("[SuffixedFileLoader] Attempting to load file: {$file}");

        if ($this->files->exists($file)) {
            Log::debug("[SuffixedFileLoader] File exists: {$file}");
            return $this->files->getRequire($file);
        }
        Log::debug("[SuffixedFileLoader] File does not exist: {$file}");
        return null;
    }

    /**
     * Support for vendor/namespace translations if needed.
     *
     * @param string $namespace
     * @param string $path
     * @return string
     */
    protected function addNamespacePath($namespace, $path)
    {
        if (isset($this->hints[$namespace])) {
            return $this->hints[$namespace];
        }
        return $path;
    }
}
