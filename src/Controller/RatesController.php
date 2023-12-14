<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Controller;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Wtsvk\CnbFxRates\Rate\ApiFacade;
use Wtsvk\CnbFxRates\Routing\Attribute\Route;

class RatesController
{
    public function __construct(
        private readonly ApiFacade $apiFacade,
    ) {}

    #[Route(path: '/rates', name: 'rates')]
    public function index(): Response
    {
        $response = $this->apiFacade->parse();

        return new JsonResponse([
            'response' => $response,
            'code' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }

    #[Route(path: '/rates/(\d{4}-\d{2}-\d{2})', name: 'rates-for-date')]
    public function date(string $date): Response
    {
        $datetime = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        $datetime = $datetime === false ? null : $datetime;
        $response = $this->apiFacade->parse($datetime);

        return new JsonResponse([
            'response' => $response,
            'code' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }
}