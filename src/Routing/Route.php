<?php

namespace Trochilidae\Routing;

use FastRoute\RouteCollector;

class Route
{
    /**
     * @var RouteCollector
     */
    protected static $route;

    protected static $routeOption = [
        'middleware' => [],
        'prefix' => '',
        'namespace' => '\\App\\Http\\Controllers\\'
    ];

    public static function setRoute(RouteCollector $route)
    {
        static::$route = $route;
    }

    public static function get($route, $handler)
    {
        static::addRoute('GET', $route, $handler);
    }

    public static function group(array $option, callable $callback)
    {
        $defaultRouteOption = static::$routeOption;
        if (isset($option['prefix'])) {
            static::$routeOption['prefix'] = $option['prefix'];
        }

        if (isset($option['namespace'])) {
            static::$routeOption['namespace'] = $option['namespace'];
        }
        $callback();

        static::$routeOption = $defaultRouteOption;
    }

    private static function addRoute($httpMethod, $route, $handler): void
    {
        $routeOption = static::$routeOption;

        list($routeOption['controller'], $routeOption['action']) = explode('@', $handler);
        $route = '/' . rtrim($routeOption['prefix'], '/') . $route;
        static::$route->addRoute($httpMethod, $route, $routeOption);
    }
}