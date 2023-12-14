<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Routing\Loader;

use ReflectionClass;
use RuntimeException;
use Wtsvk\CnbFxRates\Routing\RouteCollection;
use Wtsvk\CnbFxRates\Routing\Attribute\Route;

class AttributeLoader implements LoaderInterface
{
    public function __construct (
        private RouteCollection $collection,
    ) { }

    public function load(mixed $class): void
    {
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes();

            foreach ($attributes as $attribute) {
                $attribute = $attribute->newInstance();
                if ($attribute instanceof Route) {
                    $callback = [$class, $method->getName()];
                    if (!is_callable($callback)) {
                        $error = sprintf('Method %s::%s() is not callable', $reflection->getName(), $method->getName());
                        throw new RuntimeException($error);
                    }

                    $attribute->setCallback($callback);
                    $this->collection->add($attribute);
                }
            }
        }
    }
}