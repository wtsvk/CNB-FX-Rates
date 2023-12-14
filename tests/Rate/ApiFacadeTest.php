<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Tests\Controller;

use DateTimeImmutable;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Wtsvk\CnbFxRates\Controller\RatesController;
use Wtsvk\CnbFxRates\Rate\ApiFacade;
use Wtsvk\CnbFxRates\Rate\DailyRate;
use Wtsvk\CnbFxRates\Rate\DailyRateCollection;

final class ApiFacadeTest extends TestCase
{
    private MockHttpClient $client;
    private ApiFacade $apiFacade;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new MockHttpClient();
        $this->apiFacade = new ApiFacade($this->client, 'https://example.com');
    }

    public function testInvalidResponse(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(''),
        ]);

        $this->expectException(RuntimeException::class);
        $this->apiFacade->parse();
    }

    public function testTransportError(): void
    {
        $this->client->setResponseFactory([
            new MockResponse([new \RuntimeException('Error at transport level')]),
        ]);

        $this->expectException(RuntimeException::class);
        $this->apiFacade->parse();
    }

    public function testNoData(): void
    {
        $this->client->setResponseFactory([
            new MockResponse("14.12.2023 #241\nzemě|měna|množství|kód|kurz\n"),
        ]);

        $result = $this->apiFacade->parse();
        $date = DateTimeImmutable::createFromFormat('Y-m-d', '2023-12-14');
        self::assertInstanceOf(DateTimeImmutable::class, $date);
        $expectedResult = new DailyRateCollection($date, '#241', []);

        self::assertEquals($expectedResult, $result);
    }

    public function testWithData(): void
    {
        $this->client->setResponseFactory([
            new MockResponse("14.12.2023 #241\nzemě|měna|množství|kód|kurz\nAustrálie|dolar|1|AUD|14,993\n"),
        ]);

        $result = $this->apiFacade->parse();
        $date = DateTimeImmutable::createFromFormat('Y-m-d', '2023-12-14');
        self::assertInstanceOf(DateTimeImmutable::class, $date);
        $expectedResult = new DailyRateCollection($date, '#241', [
            new DailyRate('Austrálie', 'dolar', 1, 'AUD', 14.993),
        ]);

        self::assertEquals($expectedResult, $result);
    }
}