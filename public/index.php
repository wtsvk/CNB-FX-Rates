<?php
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\CurlHttpClient;
use Wtsvk\CnbFxRates\Routing\RouteCollection;
use Wtsvk\CnbFxRates\Routing\Loader\AttributeLoader;
use Wtsvk\CnbFxRates\Controller\DefaultController;
use Wtsvk\CnbFxRates\Controller\RatesController;
use Wtsvk\CnbFxRates\Rate\ApiFacade;
use Wtsvk\CnbFxRates\Routing\RouteProcessor;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$defaultController = new DefaultController();

$httpClient = new CurlHttpClient();
$apiFacade = new ApiFacade($httpClient, $_ENV['CNB_FX_DAILY']);
$ratesController = new RatesController($apiFacade);

$routes = new RouteCollection();
$loader = new AttributeLoader($routes);
$loader->load($defaultController);
$loader->load($ratesController);
$processor = new RouteProcessor($routes);

$path = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$response = $processor->run($method, $path);
$response->send();