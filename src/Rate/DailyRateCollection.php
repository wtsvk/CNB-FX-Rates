<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Rate;

use DateTimeImmutable;
use JsonSerializable;

class DailyRateCollection implements JsonSerializable
{
    /**
     * @param DailyRate[] $rates
     */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly string $code,
        public readonly array $rates,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'code' => $this->code,
            'rates' => $this->rates,
        ];
    }
}