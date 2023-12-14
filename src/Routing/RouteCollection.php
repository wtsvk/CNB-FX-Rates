<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Routing;

use Wtsvk\CnbFxRates\Routing\Attribute\Route;

class RouteCollection
{
    /**
     * @var array<value-of<Method>, array<string, Route>>
     */
    private array $byName = [];

    /**
     * @var array<value-of<Method>, array<int, Route>>
     */
    private array $byPriority = [];

    public function add(Route $route): void
    {
        $method = Method::from($route->method)->value;

        $this->byName[$method][$route->name] = $route;
        $this->byPriority[$method][] = $route;

        usort($this->byPriority[$method], static fn (Route $a, Route $b) => $a->priority <=> $b->priority);
    }

    public function getByName(string $name, ?Method $method = null): ?Route
    {
        $method ??= Method::GET;
        return $this->byName[$method->value][$name] ?? null;
    }

    /**
     * @return Route[]
     */
    public function getByMethod(Method $method): array
    {
        return $this->byPriority[$method->value] ?? [];
    }

    public function findByPath(string $path, ?Method $method = null): ?MatchedRoute
    {
        $method ??= Method::GET;

        foreach ($this->getByMethod($method) as $route) {
            $arguments = $route->match($path);

            if ($arguments !== null) {
                $foundPath = (string) array_shift($arguments);
                return new MatchedRoute($route, $foundPath, $arguments);
            }
        }

        return null;
    }
}