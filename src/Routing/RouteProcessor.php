<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Routing;

use Symfony\Component\HttpFoundation\Response;
use Wtsvk\CnbFxRates\Routing\Attribute\Route;

class RouteProcessor
{
    public function __construct(
        private RouteCollection $routeCollection,
    ) {}

    public function run(string $method, string $path): Response
    {
        $matchedRoute = $this->find($method, $path);
        $callback = $matchedRoute->route->getCallback();
        return $callback(...$matchedRoute->arguments);
    }

    public function find(string $method, string $path): MatchedRoute
    {
        $method = Method::from($method);
        $path = (string) preg_replace('~/+~', '/', $path);
        $path = (string) parse_url($path, PHP_URL_PATH);
        $path = rtrim($path, '/');
        $path = $path === '' ? '/' : $path;

        $route = $this->routeCollection->findByPath($path, $method);

        if ($route === null) {
            /** @var Route $route */
            $route = $this->routeCollection->getByName('not-found', Method::GET);
            $route = new MatchedRoute($route, $path, []);
        }

        return $route;
    }
}