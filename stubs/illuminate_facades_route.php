<?php

declare(strict_types=1);

namespace Illuminate\Support\Facades;

/**
 * Lightweight facade stub for static analysis only.
 *
 * @method static \Illuminate\Routing\RouteRegistrar get(string $uri, $action = null)
 * @method static \Illuminate\Routing\RouteRegistrar post(string $uri, $action = null)
 * @method static \Illuminate\Routing\RouteRegistrar put(string $uri, $action = null)
 * @method static \Illuminate\Routing\RouteRegistrar patch(string $uri, $action = null)
 * @method static \Illuminate\Routing\RouteRegistrar delete(string $uri, $action = null)
 * @method static \Illuminate\Routing\RouteRegistrar view(string $uri, string $view, array $data = [])
 * @method static \Illuminate\Routing\RouteRegistrar resource(string $name, string $controller)
 * @method static \Illuminate\Routing\RouteRegistrar prefix(string $prefix)
 * @method static \Illuminate\Routing\RouteRegistrar middleware($middleware)
 * @method static \Illuminate\Routing\RouteRegistrar name(string $name)
 * @method static \Illuminate\Routing\RouteRegistrar group($callable)
 * @method static \Illuminate\Routing\RouteRegistrar fallback($callable)
 */
class Route
{
    // Intentionally empty. This file exists for static analyzers (Intelephense/Psalm).
}

namespace Illuminate\Routing;

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
