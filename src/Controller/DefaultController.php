<?php

declare(strict_types=1);

namespace Wtsvk\CnbFxRates\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Wtsvk\CnbFxRates\Routing\Attribute\Route;

class DefaultController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return new JsonResponse([
            'response' => 'Hello world!',
            'code' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }

    #[Route(path: '/', name: 'not-found', priority: 100)]
    public function notFound(): Response
    {
        return new JsonResponse([
            'response' => 'Not Found',
            'code' => Response::HTTP_NOT_FOUND,
        ], Response::HTTP_NOT_FOUND);
    }
}