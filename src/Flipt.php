<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class Flipt
{
    private const RELATIVE_EVALUATE_ENDPOINT = '/api/v1/evaluate';

    private HttpClient $client;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $evaluateEndoint;

    public static function create(string $baseUrl): self
    {
        return new static(
            HttpClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
            $baseUrl
        );
    }

    public function __construct(HttpClient $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory, string $baseUrl)
    {
        $this->client          = $client;
        $this->requestFactory  = $requestFactory;
        $this->streamFactory   = $streamFactory;

        if (mb_substr($baseUrl, -1) === '/') {
            $baseUrl = mb_substr($baseUrl, 0, -1);
        }

        $this->evaluateEndoint       = $baseUrl . static::RELATIVE_EVALUATE_ENDPOINT;
    }

    public function evaluate(EvaluateRequest $evaluateRequest): EvaluateResponse
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->evaluateEndoint
        );

        $json = json_encode($evaluateRequest);

        if ($json === false) {
            $json = '';
        }

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));

        $response = $this->client->sendRequest($request);
        $data     = json_decode($response->getBody()->getContents(), true);

        return new EvaluateResponse($data);
    }
}
