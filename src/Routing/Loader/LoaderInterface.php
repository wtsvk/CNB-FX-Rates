<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Routing\Loader;

interface LoaderInterface
{
    public function load(mixed $class): void;
}