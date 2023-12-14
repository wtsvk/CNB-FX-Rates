<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Rate;

use DateTimeImmutable;
use JsonSerializable;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DailyRate implements JsonSerializable
{
    public function __construct(
        public readonly string $country,
        public readonly string $currency,
        public readonly int $amount,
        public readonly string $code,
        public readonly float $rate,
    ) {}

    public function millionCZK(): float
    {
        $million = 1000000;
        return $this->amount * ($million / $this->rate);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'code' => $this->code,
            'country' => $this->country,
            'millionCZK' => $this->millionCZK(),
        ];
    }
}