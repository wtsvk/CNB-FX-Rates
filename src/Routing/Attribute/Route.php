<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Routing\Attribute;

use Attribute;
use Wtsvk\CnbFxRates\Routing\Method;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    private mixed $callback;

    /**
     * @param value-of<Method> $method
     */
    public function __construct(
        public readonly string $path,
        public readonly string $method = 'GET',
        public readonly string|null $name = null,
        public readonly int $priority = 10,
    ) {
        Method::from($method);
    }

    /**
     * @return array<string, string>|null
     */
    public function match(string $path): ?array
    {
        $matches = [];
        $regex = sprintf('~^%s$~', $this->path);
        return preg_match($regex, $path, $matches) === 1 ? $matches : null;
    }

    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }
}