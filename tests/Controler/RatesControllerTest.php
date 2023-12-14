<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Tests\Controller;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Wtsvk\CnbFxRates\Controller\RatesController;
use Wtsvk\CnbFxRates\Rate\ApiFacade;

final class RatesControllerTest extends TestCase
{
    private MockHttpClient $client;
    private ApiFacade $apiFacade;
    private RatesController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new MockHttpClient();
        $this->apiFacade = new ApiFacade($this->client, 'https://example.com');
        $this->controller = new RatesController($this->apiFacade);
    }

    public function testResponse(): void
    {
        $body = file_get_contents(__DIR__ . '/Fixtures/cnb-fx-response.txt');
        $this->client->setResponseFactory([
            new MockResponse($body),
        ]);

        $apiResponse = $this->controller->index();
        $expectedResponse = file_get_contents(__DIR__ . '/Fixtures/api-fx-response.json');

        self::assertSame($expectedResponse, $apiResponse->getContent());
    }
}