<?php

namespace Crudler\Routing;

use Illuminate\Support\Facades\Route;

class CrudlerRoute
{
    public static function get(string $uri, array $target)    { return self::register('get', $uri, $target); }
    public static function post(string $uri, array $target)   { return self::register('post', $uri, $target); }
    public static function put(string $uri, array $target)    { return self::register('put', $uri, $target); }
    public static function patch(string $uri, array $target)  { return self::register('patch', $uri, $target); }
    public static function delete(string $uri, array $target) { return self::register('delete', $uri, $target); }
    public static function options(string $uri, array $target){ return self::register('options', $uri, $target); }
    public static function any(string $uri, array $target)    { return self::register('any', $uri, $target); }

    protected static function register(string $method, string $uri, array $target)
    {
        [$controller, $action, $dtoFunc] = $target;

        // Просто вызываем метод контроллера с DTO
        $route = Route::$method($uri, function () use ($controller, $action, $dtoFunc) {
            return app($controller)->$action($dtoFunc());
        });

        return $route;
    }
}
