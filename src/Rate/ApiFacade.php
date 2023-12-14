<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Rate;

use DateTimeImmutable;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiFacade
{
    public function __construct(
        private HttpClientInterface $client,
        private readonly string $url,
    ) {}

    public function parse(?DateTimeImmutable $date = null): DailyRateCollection
    {
        $response = $this->download($date);
        $lines = array_filter(explode("\n", $response->getContent()));

        $matches = [];
        $firstLine = (string) array_shift($lines);
        $result = preg_match('~(\d{2}\.\d{2}\.\d{4})\s(\#\d+)~', $firstLine, $matches);
        if ($result !== 1) {
            throw new \RuntimeException('Invalid response');
        }

        $exchangeDate = DateTimeImmutable::createFromFormat('d.m.Y', $matches[1]);
        if ($exchangeDate === false) {
            throw new \RuntimeException('Invalid date');
        }
        $exchangeCode = $matches[2];
        array_shift($lines);
        $rates = [];

        foreach ($lines as $line) {
            $data = str_getcsv($line, '|');
            $rates[] = new DailyRate(
                (string) $data[0],
                (string) $data[1],
                (int) $data[2],
                (string) $data[3],
                (float) strtr((string) $data[4], [',' => '.']),
            );
        }

        return new DailyRateCollection($exchangeDate, $exchangeCode, $rates);
    }

    public function download(?DateTimeImmutable $date = null): ResponseInterface
    {
        return $this->client->request('GET', $this->url, [
            'query' => array_filter([
                'date' => $date !== null ? $date->format('d.m.Y') : null,
            ]),
        ]);
    }
}