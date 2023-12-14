<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Routing;

use Wtsvk\CnbFxRates\Routing\Attribute\Route;

class MatchedRoute
{
    /**
     * @param array<string, string> $arguments
     */
    public function __construct(
        public readonly Route $route,
        public readonly string $path,
        public readonly array $arguments,
    ) {}
}