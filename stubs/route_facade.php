<?php

declare(strict_types=1);

// Minimal Route facade and registrar stubs for static analysis (Intelephense/PHPStan).
// These are safe editor-only stubs: wrapped in class_exists checks so they never override
// the real framework classes at runtime.

if (! class_exists('Route')) {
    /**
     * @psalm-suppress UnusedClass
     */
    class Route
    {
        public static function get($uri, $action = null): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function post($uri, $action = null): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function put($uri, $action = null): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function patch($uri, $action = null): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function delete($uri, $action = null): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function view($uri, $view, $data = []): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function resource($name, $controller): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function prefix($prefix): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function middleware($middleware): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function name($name): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function group($callable): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function fallback($callable): RouteRegistrar
        {
            return new RouteRegistrar;
        }

        public static function any($uri, $action = null): RouteRegistrar
        {
            return new RouteRegistrar;
        }
    }

    class RouteRegistrar
    {
        public function name(string $name): self
        {
            return $this;
        }

        public function middleware($middleware): self
        {
            return $this;
        }

        public function where($name, $pattern): self
        {
            return $this;
        }

        public function whereNumber($name): self
        {
            return $this;
        }

        public function group($callable): self
        {
            return $this;
        }

        public function prefix($prefix): self
        {
            return $this;
        }

        public function only(array $methods): self
        {
            return $this;
        }

        public function get($uri, $action = null): self
        {
            return $this;
        }

        public function post($uri, $action = null): self
        {
            return $this;
        }

        public function put($uri, $action = null): self
        {
            return $this;
        }

        public function patch($uri, $action = null): self
        {
            return $this;
        }

        public function delete($uri, $action = null): self
        {
            return $this;
        }

        public function view($uri, $view, $data = []): self
        {
            return $this;
        }
    }
}
