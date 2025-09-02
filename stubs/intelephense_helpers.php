<?php

declare(strict_types=1);

// Lightweight helper stubs to help static analyzers (Intelephense/PHPStan/PSalm)
// recognise common Laravel global helpers used in route files and tests.
// These are wrapped in function_exists guards so they never collide at runtime.

if (! function_exists('view')) {
    /**
     * @return \Illuminate\Contracts\View\View|string
     */
    function view(string $view, array $data = [])
    {
        // stub for static analysis only
        return '';
    }
}

if (! function_exists('resource_path')) {
    function resource_path(string $path = ''): string
    {
        return '';
    }
}

if (! function_exists('app')) {
    /** @return \Illuminate\Contracts\Container\Container|mixed */
    function app($abstract = null)
    {
        return null;
    }
}

if (! function_exists('__')) {
    function __(?string $key = null, array $replace = [], $locale = null)
    {
        return (string) $key;
    }
}

if (! function_exists('config')) {
    function config($key = null, $default = null)
    {
        return $default;
    }
}

if (! function_exists('redirect')) {
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        return null;
    }
}

if (! function_exists('url')) {
    /** @return string */
    function url($path = null)
    {
        return (string) $path;
    }
}

if (! function_exists('response')) {
    function response($content = '', $status = 200, array $headers = [])
    {
        return null;
    }
}

// file_exists is a core PHP function; declare a noop if analyzer complains
if (! function_exists('file_exists')) {
    function file_exists(string $filename): bool
    {
        return false;
    }
}

if (! function_exists('route')) {
    function route($name, $parameters = [], $absolute = true)
    {
        return (string) $name;
    }
}
