<?php


require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

if ($request->getPathInfo() === '/api/health-check') {
    $response = new Response(
        json_encode(['status' => 'ok', 'timestamp' => time()]),
        Response::HTTP_OK,
        ['Content-Type' => 'application/json']
    );
    $response->send();
    exit;
}

$response = new Response(
    json_encode(['error' => 'Not Found']),
    Response::HTTP_NOT_FOUND,
    ['Content-Type' => 'application/json']
);
$response->send();