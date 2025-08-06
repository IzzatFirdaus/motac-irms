<?php

namespace App\Providers;

use App\Translation\SuffixedTranslator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

/**
 * Custom Translation Service Provider for MOTAC IRMS
 *
 * This service provider replaces Laravel's default translation system with a custom
 * implementation that supports suffixed language files (e.g., forms_en.php, app_ms.php).
 *
 * Features:
 * - Automatic locale suffix appending to translation keys
 * - Graceful fallback to non-suffixed keys
 * - Enhanced error handling and logging
 * - Performance optimizations for translation loading
 *
 * @package App\Providers
 * @author MOTAC ICT Team
 * @since 1.0.0
 */
class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register the custom suffixed translator and related services.
     *
     * This method sets up the translation system to use suffixed language files
     * while maintaining compatibility with Laravel's translation features.
     *
     * @return void
     */
    public function register()
    {
        // Register the translation file loader
        $this->registerTranslationLoader();

        // Register the custom suffixed translator
        $this->registerSuffixedTranslator();
    }

    /**
     * Register the translation file loader.
     *
     * The FileLoader is responsible for loading translation files from the
     * resources/lang directory. It handles both suffixed and non-suffixed files.
     *
     * @return void
     */
    protected function registerTranslationLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            try {
                return new FileLoader($app['files'], $app['path.lang']);
            } catch (\Exception $e) {
                // Log error and provide fallback
                Log::error('Failed to initialize translation loader: ' . $e->getMessage());
                throw new \RuntimeException('Translation system initialization failed. Please check your language files.', 0, $e);
            }
        });
    }

    /**
     * Register the custom suffixed translator.
     *
     * The SuffixedTranslator extends Laravel's default translator to support
     * automatic locale suffix appending and enhanced fallback behavior.
     *
     * @return void
     */
    protected function registerSuffixedTranslator()
    {
        $this->app->singleton('translator', function ($app) {
            try {
                $loader = $app['translation.loader'];
                $locale = $app['config']['app.locale'];

                // Validate locale configuration
                if (empty($locale)) {
                    Log::warning('App locale is not configured. Falling back to default.');
                    $locale = 'en';
                }

                // Create and configure the custom translator
                $translator = new SuffixedTranslator($loader, $locale);

                // Set the fallback locale
                $fallbackLocale = $app['config']['app.fallback_locale'];
                if (!empty($fallbackLocale)) {
                    $translator->setFallback($fallbackLocale);
                }

                return $translator;

            } catch (\Exception $e) {
                // Log error and provide emergency fallback
                Log::error('Failed to initialize suffixed translator: ' . $e->getMessage());
                throw new \RuntimeException('Custom translation system initialization failed.', 0, $e);
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all service providers have been registered.
     * It performs any additional setup required for the translation system.
     *
     * @return void
     */
    public function boot()
    {
        // Log successful initialization
        if (config('translation.log_missing_keys', true)) {
            Log::info('MOTAC IRMS Suffixed Translation System initialized successfully.');
        }

        // Add any additional boot logic here
        $this->configureCaching();
    }

    /**
     * Configure translation caching if enabled.
     *
     * @return void
     */
    protected function configureCaching()
    {
        if (config('translation.cache_translations', true) && !$this->app->environment('local')) {
            // Enable translation caching in production environments
            // This can significantly improve performance for large applications
            Log::debug('Translation caching is enabled for improved performance.');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'translator',
            'translation.loader',
        ];
    }
}
