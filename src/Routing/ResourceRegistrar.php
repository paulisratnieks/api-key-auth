<?php

declare(strict_types=1);

namespace PaulisRatnieks\ApiKeyAuth\Routing;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Override;

class ResourceRegistrar extends \Illuminate\Routing\ResourceRegistrar
{
    #[Override]
    public function register($name, $controller, array $options = []): RouteCollection
    {
        $routes = parent::register($name, $controller, $options)
            ->getRoutes();

        $collection = new RouteCollection();
        collect($routes)->each(function (Route $route) use ($collection, $options): void {
            if (isset($options['scopes'])) {
                $route->scopes($options['scopes']);
            }
            $collection->add($route);
        });

        return $collection;
    }
}
